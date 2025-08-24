<?php

namespace App\Http\Controllers\Student;

use App\Events\ContentCompleted;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\QuizSubmissionResource;
use App\Http\Resources\ContentResource;
use App\Models\Content;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Services\ContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContentController extends Controller
{

    public function __construct(protected ContentService $contentService) {}

    public function show(Request $request, Content $content)
    {
        Gate::authorize('view-content', $content);

        $user = $request->user();

        if ($content->contentable_type === Material::class) {
            // Dispatch the event to mark as complete and award points
            ContentCompleted::dispatch($user, $content);

            return inertia('student/content/material-show', [
                'content' => ContentResource::make($content->load('contentable')),
            ]);
        }

        // For quizzes, just show the info page.
        // The ContentCompleted event will be dispatched after a quiz is submitted.
        return inertia('student/content/quiz-show', [
            'content' => ContentResource::make($content->load('contentable')),
        ]);
    }

    public function startQuiz(Content $content)
    {
        Gate::authorize('view-content', $content);

        $submission = auth()->user()->quizSubmissions()
            ->where('quiz_id', $content->contentable->id)
            ->first();

        if ($submission && $submission->completed_at) {
            return to_route('student.quizzes.result', $content)
                ->withErrors(['error' => 'You have already completed this quiz.']);
        }


        if ($content->contentable_type !== Quiz::class) {
            return to_route('student.contents.show', $content)
                ->withErrors(['error' => 'This content is not a quiz.']);
        }

        $quiz = $content->contentable;
        $user = auth()->user();

        if ($submission) {
            $submission->load('quiz.questions.answers', 'answers');
            return inertia('student/content/quiz-start', [
                'content' => ContentResource::make($content->load('contentable')),
                'quizSubmission' => QuizSubmissionResource::make($submission),
            ]);
        }

        // Create a new submission
        $submission = QuizSubmission::create([
            'student_id' => $user->id,
            'quiz_id' => $quiz->id,
            'started_at' => now(),
        ]);

        $submission->load('quiz.questions.answers');

        return inertia('student/content/quiz-start', [
            'content' => ContentResource::make($content->load('contentable')),
            'quizSubmission' => QuizSubmissionResource::make($submission),
        ]);
    }

    public function submitAnswer(Request $request, Content $content)
    {

        $data = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_id' => 'required|exists:answers,id',
        ]);

        $submission = auth()->user()->quizSubmissions()
            ->where('quiz_id', $content->contentable->id)
            ->whereNull('completed_at')
            ->first();

        if (!$submission) {
            return to_route('student.contents.show', $content)
                ->withErrors(['error' => 'You have not started this quiz yet.']);
        }

        try {
            $this->contentService->selectAnswer(
                $content,
                $data
            );

            return to_route('student.quizzes.start', $content)
                ->with('success', 'Your answer has been submitted successfully.');
        } catch (\Throwable $th) {
            return to_route('student.quizzes.start', $content)
                ->withErrors(['error' => 'Failed to submit your answer: ' . $th->getMessage()]);
        }
    }

    public function resultQuiz(Content $content)
    {
        if ($content->contentable_type !== 'App\Models\Quiz') {
            return redirect()->back()->withErrors(['error' => 'This content is not a quiz.']);
        }

        $submission = auth()->user()->quizSubmissions()
            ->where('quiz_id', $content->contentable->id)
            ->first();

        if (!$submission) {
            return to_route('student.quizzes.start', $content)
                ->withErrors(['error' => 'You have not completed this quiz yet.']);
        }

        if (!$submission->completed_at) {
            $submission->completed_at = now();
            $submission->duration_seconds = $submission->started_at->diffInSeconds($submission->completed_at);
            $submission->score = $this->contentService->calculateQuizPoint(
                $content,
                $submission
            );

            $submission->save();
            ContentCompleted::dispatch(auth()->user(), $content, [
                'score' => $submission->score,
                'duration' => $submission->duration_seconds,
            ]);
        }



        return inertia('student/content/quiz-result', [
            'content' => ContentResource::make($content->load(['contentable', 'classroom'])),
            'quizSubmission' => QuizSubmissionResource::make($submission->load('quiz.questions.answers', 'answers')),
        ]);
    }

    public function reviewQuiz(Content $content)
    {

        if ($content->contentable_type !== 'App\Models\Quiz') {
            return redirect()->back()->withErrors(['error' => 'This content is not a quiz.']);
        }

        $submission = auth()->user()->quizSubmissions()
            ->where('quiz_id', $content->contentable->id)
            ->whereNotNull('completed_at')
            ->first();

        if (!$submission) {
            return to_route('student.quizzes.start', $content)
                ->withErrors(['error' => 'You have not completed this quiz yet.']);
        }


        return inertia('student/content/quiz-review', [
            'content' => ContentResource::make($content->load('contentable')),
            'quizSubmission' => QuizSubmissionResource::make($submission->load('quiz.questions.answers', 'answers')),
        ]);
    }
}
