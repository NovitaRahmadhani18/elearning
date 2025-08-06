<?php

namespace App\Observers;

use App\Models\QuizSubmission;
use App\Services\AchievementService;
use Illuminate\Support\Facades\Log;

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
                    Log::info("New achievements granted to user {$user->id}: " . implode(', ', $newAchievements));
                }
            }
        } catch (\Exception $e) {
            Log::error("Error checking achievements for quiz submission {$quizSubmission->id}: " . $e->getMessage());
        }
    }
}