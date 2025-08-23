<?php

namespace App\Http\Controllers\Api;

use App\Events\ContentCompleted;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\QuizSubmissionResource;
use App\Models\Answer;
use App\Models\Content;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class QuizSubmissionController extends Controller
{
    /**
     * Start a new quiz submission.
     */
    public function start(Content $content, Request $request)
    {
        Gate::authorize('view-content', $content);

        if ($content->contentable_type !== Quiz::class) {
            return response()->json(['message' => 'This content is not a quiz.'], 422);
        }

        $quiz = $content->contentable;
        $user = $request->user();

        // Check for existing incomplete submission
        $existingSubmission = QuizSubmission::where('student_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->first();

        if ($existingSubmission) {
            $existingSubmission->load('quiz.questions.answers', 'answers');
            return QuizSubmissionResource::make($existingSubmission);
        }

        // Create a new submission
        $submission = QuizSubmission::create([
            'student_id' => $user->id,
            'quiz_id' => $quiz->id,
            'started_at' => now(),
        ]);

        $submission->load('quiz.questions.answers');

        return QuizSubmissionResource::make($submission);
    }

    /**
     * Show an existing quiz submission (for resuming).
     */
    public function show(QuizSubmission $submission)
    {
        $submission->load('quiz.questions.answers', 'answers');

        return QuizSubmissionResource::make($submission);
    }

    /**
     * Update a quiz submission with the user's answers (autosave).
     */
    public function update(Request $request, QuizSubmission $submission)
    {

        if ($submission->completed_at) {
            return response()->json(['message' => 'This quiz has already been completed.'], 422);
        }

        $duration = $submission->quiz->duration_minutes ?? 60; // Default to 60 minutes if not set
        $deadline = $submission->started_at->addMinutes($duration);
        if (now()->isAfter($deadline)) {
            return response()->json(['message' => "Time's up! You can no longer save answers."], 422);
        }

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*.question_id' => ['required', Rule::exists('questions', 'id')->where('quiz_id', $submission->quiz_id)],
            'answers.*.answer_id' => ['required', Rule::exists('answers', 'id')],
        ]);

        $submittedAnswerIds = collect($validated['answers'])->pluck('answer_id');
        $answerModels = Answer::whereIn('id', $submittedAnswerIds)->get()->keyBy('id');

        $answersToUpsert = [];
        foreach ($validated['answers'] as $answer) {
            $answersToUpsert[] = [
                'quiz_submission_id' => $submission->id,
                'question_id' => $answer['question_id'],
                'answer_id' => $answer['answer_id'],
                'is_correct' => $answerModels->get($answer['answer_id'])->is_correct ?? false,
            ];
        }

        if (!empty($answersToUpsert)) {
            DB::table('submission_answers')->upsert(
                $answersToUpsert,
                ['quiz_submission_id', 'question_id'], // Unique by combination
                ['answer_id', 'is_correct'] // Columns to update
            );
        }

        return response()->noContent();
    }

    /**
     * Mark a quiz submission as complete, grade it, and dispatch completion event.
     */
    public function complete(Request $request, QuizSubmission $submission)
    {

        if ($submission->completed_at) {
            return response()->json(['message' => 'This quiz has already been completed.'], 422);
        }

        $duration = $submission->quiz->duration_minutes ?? 60; // Default to 60 minutes if not set
        $deadline = $submission->started_at->addMinutes($duration);
        if (now()->isAfter($deadline)) {
            return response()->json(['message' => "Time's up! This quiz can no longer be submitted."], 422);
        }

        // Final save of answers before grading
        if ($request->has('answers')) {
            $this->update($request, $submission);
        }

        // Grade the quiz
        $quiz = $submission->quiz;
        $totalQuestions = $quiz->questions()->count();
        $correctAnswers = 0;

        $submittedAnswers = DB::table('submission_answers')
            ->where('quiz_submission_id', $submission->id)
            ->pluck('answer_id', 'question_id');

        $correctAnswerIds = DB::table('answers')
            ->whereIn('question_id', $submittedAnswers->keys())
            ->where('is_correct', true)
            ->pluck('id');

        foreach ($submittedAnswers as $questionId => $answerId) {
            if ($correctAnswerIds->contains($answerId)) {
                $correctAnswers++;
            }
        }

        $score = ($totalQuestions > 0) ? ($correctAnswers / $totalQuestions) * 100 : 0;

        // Update submission record
        $submission->update([
            'completed_at' => now(),
            'score' => $score,
            'duration_seconds' => now()->diffInSeconds($submission->started_at),
        ]);

        // Dispatch completion event
        ContentCompleted::dispatch($submission->student, $quiz->content, ['score' => $score]);

        // Add calculated fields to the submission model for the resource
        $submission->correct_answers_count = $correctAnswers;
        $submission->total_questions = $totalQuestions;
        $submission->accuracy = ($totalQuestions > 0) ? ($correctAnswers / $totalQuestions) * 100 : 0;
        $submission->incorrect_answers_count = $totalQuestions - $correctAnswers;

        $submission->load('quiz.questions.answers'); // Ensure quiz questions are loaded for the resource

        return QuizSubmissionResource::make($submission);
    }
}
