<?php

namespace App\Achievements;


class FastLearnerAchievement
{
    public string $name = 'Fast Learner';
    public string $description = 'Menyelesaikan kuis dalam waktu < 10 menit';
    public string $image = '/images/achievements/Fast Learner.png';
    public bool $secret = false;

    /**
     * The point value that will be added to the user when unlocked.
     */
    public int $points = 30;

    /**
     * Check if the user qualifies for this achievement.
     */
    public function qualifier(object $user): bool
    {
        // Check if user has any completed quiz in less than 10 minutes (600 seconds)
        return $user->quizSubmissions()
            ->where('is_completed', true)
            ->where('time_spent', '<', 600)
            ->exists();
    }
}
