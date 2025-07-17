<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Material;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classrooms = auth()->user()->classrooms()
            ->with([
                'teacher',
                'contents',
                'quizzes',
                'materials'
            ])
            ->withCount(['contents', 'quizzes', 'materials'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Update progress for each classroom
        foreach ($classrooms as $classroom) {
            $realProgress = auth()->user()->getClassroomProgress($classroom->id);

            // Update pivot table with real progress
            auth()->user()->classrooms()->updateExistingPivot($classroom->id, [
                'progress' => $realProgress
            ]);

            // Update the current object to reflect the new progress
            $classroom->pivot->progress = $realProgress;
        }

        return view('pages.user.classroom.index', compact('classrooms'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function joinForm(Classroom $classroom)
    {
        if (auth()->user()->classrooms->contains($classroom)) {
            return to_route('user.classroom.show', $classroom)
                ->with('success', 'You are already enrolled in this classroom.');
        }

        // Show the join form with the classroom code pre-filled
        return view('pages.user.classroom.join', compact('classroom'));
    }

    public function join(Request $request, Classroom $classroom)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        if ($request->code !== $classroom->secret_code) {
            return to_route('user.classroom.join.form', $classroom->invite_code)
                ->with('error', 'Invalid classroom code.');
        }

        if (auth()->user()->classrooms->contains($classroom)) {
            return to_route('user.classroom.index')
                ->with('error', 'You are already enrolled in this classroom.');
        }

        // Enroll user in the classroom
        auth()->user()->classrooms()->attach($classroom);

        return to_route('user.classroom.show', $classroom)
            ->with('success', 'You have successfully joined the classroom.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {

        $classroom->load(['teacher', 'contents.contentable']);

        if (!auth()->user()->classrooms->contains($classroom)) {
            return to_route('user.classroom.index')
                ->with('error', 'You are not enrolled in this classroom.');
        }

        // Auto-complete expired quizzes for this user in this classroom
        $expiredQuizzesCompleted = \App\Services\ExpiredQuizService::autoCompleteExpiredQuizzesInClassroom(
            $classroom->id,
            auth()->id()
        );

        // Show notification if any expired quizzes were auto-completed
        if ($expiredQuizzesCompleted > 0) {
            session()->flash(
                'info',
                "Notice: {$expiredQuizzesCompleted} expired quiz(es) have been automatically completed to unlock subsequent content."
            );
        }

        // check completed contents
        $completedContents = auth()->user()->completedContents()
            ->where('classroom_id', $classroom->id)
            ->pluck('contentable.id')
            ->toArray();

        return view('pages.user.classroom.show', compact('classroom', 'completedContents'));
    }


    public function showMaterial(Classroom $classroom, Material $material)
    {
        $currentContent = $material->contents()
            ->where('classroom_id', $classroom->id)
            ->firstOrFail();

        // Check if this is the first time user opens this material
        if (!$currentContent->isCompletedByUser()) {
            // Award points from the material (not fixed 10 points)
            $pointsToAward = $material->points > 0 ? $material->points : 10; // Default to 10 if no points set

            // Calculate completion time (assume started when attached, completed now)
            $pivotData = auth()->user()->completedContents()->where('content_id', $currentContent->id)->first();
            $startTime = $pivotData ? $pivotData->pivot->created_at : now();
            $completionTime = now()->diffInSeconds($startTime);

            // Simply add points without auditing details
            auth()->user()->addPoints($pointsToAward);

            // Mark the content as completed with leaderboard data
            auth()->user()->completedContents()->syncWithoutDetaching([
                $currentContent->id => [
                    'completion_time' => $completionTime,
                    'points_earned' => $pointsToAward,
                    'score' => null // Materials don't have scores
                ]
            ]);

            // Update classroom progress
            $newProgress = auth()->user()->getClassroomProgress($classroom->id);
            auth()->user()->classrooms()->updateExistingPivot($classroom->id, [
                'progress' => $newProgress
            ]);

            // Add success message with points earned
            session()->flash('success', "Great! You've earned {$pointsToAward} points for viewing this material!");
        }

        return view('pages.user.classroom.show-material', compact('classroom', 'material', 'currentContent'));
    }

    public function showQuiz(Classroom $classroom, Quiz $quiz)
    {
        return view('pages.user.classroom.show-quiz', compact('classroom', 'quiz'));
    }

    public function reviewQuiz(Classroom $classroom, Quiz $quiz)
    {
        // Verify user is enrolled in the classroom
        if (!auth()->user()->classrooms()->where('classroom_id', $classroom->id)->exists()) {
            return to_route('user.classroom.index')
                ->with('error', 'You are not enrolled in this classroom.');
        }

        // Check if user has completed the quiz
        $submission = $quiz->submissions()
            ->where('user_id', auth()->id())
            ->where('is_completed', true)
            ->first();

        if (!$submission) {
            return to_route('user.classroom.show', $classroom)
                ->with('error', 'You have not completed this quiz yet.');
        }

        // Load quiz with questions and options
        $quiz->load(['questions.options']);

        // Get user's answers
        $userAnswers = $submission->quizAnswers()->with('question', 'selectedOption')->get();

        return view('pages.user.classroom.review-quiz', compact('classroom', 'quiz', 'submission', 'userAnswers'));
    }

    public function autoSubmitQuiz(Classroom $classroom, Quiz $quiz)
    {
        // Verify user is enrolled in the classroom
        if (!auth()->user()->classrooms()->where('classroom_id', $classroom->id)->exists()) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        // Get existing submission
        $submission = $quiz->submissions()
            ->where('user_id', auth()->id())
            ->where('is_completed', false)
            ->first();

        if (!$submission) {
            return response()->json(['message' => 'No active submission found'], 200);
        }

        try {
            DB::transaction(function () use ($submission, $quiz, $classroom) {
                // Calculate final time spent
                $totalTimeSpent = $submission->started_at ?
                    now()->diffInSeconds($submission->started_at) : 0;

                // Get user answers from submission
                $userAnswers = $submission->answers ?? [];
                $correctAnswers = collect($userAnswers)->where('is_correct', true)->count();
                $totalQuestions = $quiz->questions()->count();

                // Calculate score using the formula: (quiz_points / total_questions) * correct_answers
                $finalScore = $totalQuestions > 0 ?
                    (($quiz->points / $totalQuestions) * $correctAnswers) : 0;

                // Update submission
                $submission->update([
                    'is_completed' => true,
                    'completed_at' => now(),
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $correctAnswers,
                    'time_spent' => $totalTimeSpent,
                    'score' => $finalScore
                ]);

                // Mark content as completed and award points
                $content = $quiz->contents()->where('classroom_id', $classroom->id)->first();
                if ($content) {
                    // Sync with leaderboard data
                    auth()->user()->completedContents()->syncWithoutDetaching([
                        $content->id => [
                            'completion_time' => $totalTimeSpent,
                            'points_earned' => $finalScore,
                            'score' => $finalScore // Quiz score for leaderboard
                        ]
                    ]);

                    auth()->user()->addPoints($finalScore);

                    // Update classroom progress
                    $newProgress = auth()->user()->getClassroomProgress($classroom->id);
                    auth()->user()->classrooms()->updateExistingPivot($classroom->id, [
                        'progress' => $newProgress
                    ]);
                }
            });

            return response()->json(['message' => 'Quiz auto-submitted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Auto-submit quiz error: ' . $e->getMessage());
            return response()->json(['error' => 'Auto-submit failed'], 500);
        }
    }
}
