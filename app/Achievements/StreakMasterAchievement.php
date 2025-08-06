<?php

namespace App\Achievements;

use Carbon\Carbon;

class StreakMasterAchievement
{
    public string $name = 'Streak Master';
    public string $description = 'Mengerjakan kuis 5 hari berturut-turut';
    public string $image = '/images/achievements/streak-master.svg';
    public bool $secret = false;

    /**
     * The point value that will be added to the user when unlocked.
     */
    public int $points = 75;

    /**
     * Check if the user qualifies for this achievement.
     */
    public function qualifier(object $user): bool
    {
        // Get all completed quiz submissions ordered by date
        $submissions = $user->quizSubmissions()
            ->where('is_completed', true)
            ->orderBy('completed_at')
            ->get()
            ->groupBy(function ($submission) {
                return $submission->completed_at->format('Y-m-d');
            });

        // Check for consecutive days
        $consecutiveDays = 0;
        $maxConsecutiveDays = 0;
        $dates = $submissions->keys()->sort();

        foreach ($dates as $index => $date) {
            if ($index === 0) {
                $consecutiveDays = 1;
            } else {
                $currentDate = Carbon::parse($date);
                $previousDate = Carbon::parse($dates[$index - 1]);

                if ($currentDate->diffInDays($previousDate) === 1) {
                    $consecutiveDays++;
                } else {
                    $maxConsecutiveDays = max($maxConsecutiveDays, $consecutiveDays);
                    $consecutiveDays = 1;
                }
            }
        }

        $maxConsecutiveDays = max($maxConsecutiveDays, $consecutiveDays);

        return $maxConsecutiveDays >= 5;
    }
}

