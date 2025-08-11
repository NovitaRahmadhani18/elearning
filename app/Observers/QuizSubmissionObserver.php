<?php

namespace App\Observers;

use App\Models\QuizSubmission;
use App\Services\AchievementService;
use App\Notifications\QuizCompletedNotification;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Contracts\Activity;

class QuizSubmissionObserver
{
    protected AchievementService $achievementService;

    public function __construct()
    {
        $this->achievementService = app(AchievementService::class);
    }

    /**
     * Handle the QuizSubmission "created" event.
     */
    public function created(QuizSubmission $quizSubmission): void
    {
        if ($quizSubmission->is_completed) {
            $this->checkAchievements($quizSubmission);
        }
    }

    /**
     * Handle the QuizSubmission "updated" event.
     */
    public function updated(QuizSubmission $quizSubmission): void
    {
        // Check if submission was just completed
        if ($quizSubmission->is_completed && $quizSubmission->wasChanged('is_completed')) {
            $this->checkAchievements($quizSubmission);
            $this->logQuizCompletion($quizSubmission);
            $this->sendQuizCompletionNotification($quizSubmission);
        }
    }

    /**
     * Send notification to teacher when quiz is completed
     */
    protected function sendQuizCompletionNotification(QuizSubmission $quizSubmission): void
    {
        try {
            $quiz = $quizSubmission->quiz;
            $content = $quiz?->contents?->first();
            $classroom = $content?->classroom;
            $teacher = $classroom?->teacher;

            if ($teacher) {
                $teacher->notify(new QuizCompletedNotification($quizSubmission));
                Log::info("Quiz completion notification sent to teacher {$teacher->id} for submission {$quizSubmission->id}");
            }
        } catch (\Exception $e) {
            Log::error("Error sending quiz completion notification for submission {$quizSubmission->id}: " . $e->getMessage());
        }
    }

    /**
     * Log detailed quiz completion activity
     */
    protected function logQuizCompletion(QuizSubmission $quizSubmission): void
    {
        try {
            $quiz = $quizSubmission->quiz;
            $user = $quizSubmission->user;
            $content = $quiz?->contents?->first();
            $classroom = $content?->classroom;

            $isPerfectScore = $quizSubmission->score_percentage >= 100;
            $isHighScore = $quizSubmission->score_percentage >= 80;

            activity('quiz_completion')
                ->causedBy($user)
                ->performedOn($quizSubmission)
                ->withProperties([
                    'quiz_title' => $quiz?->title ?? 'Unknown Quiz',
                    'classroom_name' => $classroom?->title ?? 'Unknown Classroom',
                    'score' => $quizSubmission->score,
                    'score_percentage' => $quizSubmission->score_percentage,
                    'total_questions' => $quizSubmission->total_questions,
                    'correct_answers' => $quizSubmission->correct_answers,
                    'time_spent' => $quizSubmission->time_spent,
                    'time_spent_formatted' => $quizSubmission->time_spent_formatted,
                    'is_perfect_score' => $isPerfectScore,
                    'is_high_score' => $isHighScore,
                    'completion_date' => $quizSubmission->completed_at?->toDateTimeString(),
                ])
                ->log(
                    $isPerfectScore
                        ? 'Perfect score achieved in quiz completion'
                        : ($isHighScore
                            ? 'High score achieved in quiz completion'
                            : 'Quiz completed'
                        )
                );

            Log::info("Quiz completion activity logged for user {$user?->id}, quiz {$quiz?->id}, score: {$quizSubmission->score_percentage}%");
        } catch (\Exception $e) {
            Log::error("Error logging quiz completion activity for submission {$quizSubmission->id}: " . $e->getMessage());
        }
    }

    /**
     * Check achievements for the user who completed the quiz
     */
    protected function checkAchievements(QuizSubmission $quizSubmission): void
    {
        try {
            $user = $quizSubmission->user;
            if ($user) {
                $newAchievements = $this->achievementService->checkAchievementsForUser($user);

                if (!empty($newAchievements)) {
                    // Log achievement earning activity
                    $this->logAchievementEarning($user, $newAchievements, $quizSubmission);
                    Log::info("New achievements granted to user {$user->id}: " . implode(', ', $newAchievements));
                }
            }
        } catch (\Exception $e) {
            Log::error("Error checking achievements for quiz submission {$quizSubmission->id}: " . $e->getMessage());
        }
    }

    /**
     * Log achievement earning activity
     */
    protected function logAchievementEarning($user, array $achievementNames, QuizSubmission $quizSubmission): void
    {
        try {
            foreach ($achievementNames as $achievementName) {
                activity('achievement_earned')
                    ->causedBy($user)
                    ->performedOn($quizSubmission)
                    ->withProperties([
                        'achievement_name' => $achievementName,
                        'trigger_event' => 'quiz_completion',
                        'quiz_title' => $quizSubmission->quiz?->title ?? 'Unknown Quiz',
                        'quiz_score' => $quizSubmission->score_percentage,
                        'user_level' => $user->level ?? 1,
                        'user_xp' => $user->xp ?? 0,
                        'earned_at' => now()->toDateTimeString(),
                    ])
                    ->log("Achievement '{$achievementName}' earned from quiz completion");
            }
        } catch (\Exception $e) {
            Log::error("Error logging achievement earning activity: " . $e->getMessage());
        }
    }
}
