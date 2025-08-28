<?php

namespace App\Achievements;

use App\Contracts\AchievementContract;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StreakMaster implements AchievementContract
{

    public function slug(): string
    {
        return 'streak-master';
    }

    public function check(User $user, array $context = []): bool
    {
        // Cari semua content yang di submit user
        $submissions = $user->contents()
            ->wherePivot('status', 'completed')
            ->orderBy('pivot_completed_at', 'desc')
            ->get();

        // ambil 5 tanggal terakhir
        $submissions = $submissions->map(function ($submission) {
            return Carbon::parse($submission->pivot->completed_at)->toDateString();
        });


        // Cek apakah 5 tanggal terakhir adalah berturut-turut
        return $this->hasConsecutiveStreak($submissions, 5);
    }

    /**
     * Check if the user has consecutive submission streak
     *
     * @param \Illuminate\Support\Collection $dates
     * @param int $requiredStreak
     * @return bool
     */
    private function hasConsecutiveStreak($dates, $requiredStreak): bool
    {

        $dates = $dates->unique()->sortDesc()->values();

        $streak = 1;
        for ($i = 1; $i < $dates->count(); $i++) {
            $currentDate = Carbon::parse($dates[$i]);
            $previousDate = Carbon::parse($dates[$i - 1]);

            // Cek apakah tanggal sekarang adalah satu hari sebelum tanggal sebelumnya
            dump($currentDate->diffInDays($previousDate));

            if ((int)$currentDate->diffInDays($previousDate) === 1) {

                $streak++;
                if ($streak >= $requiredStreak) {
                    return true;
                }
            } else {
                // Reset streak jika tidak berurutan
                $streak = 1;
            }
        }

        return false;
    }
}
