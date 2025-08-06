<?php

namespace App\Achievements;

class PerfectScoreAchievement
{
    public string $name = 'Perfect Score';
    public string $description = 'Mendapatkan nilai sempurna (100) pada salah satu kuis';
    public string $image = '/images/achievements/perfect-score.svg';
    public bool $secret = false;

    /**
     * The point value that will be added to the user when unlocked.
     */
    public int $points = 100;

    /**
     * Check if the user qualifies for this achievement.
     */
    public function qualifier(object $user): bool
    {
        // Check if user has any quiz submission with perfect score (100)
        return $user->quizSubmissions()
            ->where('is_completed', true)
            ->where('score', '>=',  100)
            ->exists();
    }
}

