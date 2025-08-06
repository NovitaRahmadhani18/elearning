<?php

namespace App\Achievements;

class QuizChampionAchievement
{
    public string $name = 'Quiz Champion';
    public string $description = 'Menyelesaikan kuis dengan nilai ≥ 85';
    public string $image = '/images/achievements/quiz-champion.svg';
    public bool $secret = false;

    /**
     * The point value that will be added to the user when unlocked.
     */
    public int $points = 50;

    /**
     * Check if the user qualifies for this achievement.
     */
    public function qualifier(object $user): bool
    {
        // Check if user has any quiz submission with score >= 85
        return $user->quizSubmissions()
            ->where('is_completed', true)
            ->where('score', '>=', 85)
            ->exists();
    }
}

