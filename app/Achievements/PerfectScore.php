<?php

namespace App\Achievements;

use App\Contracts\AchievementContract;
use App\Models\User;
use App\Services\LeaderboardService;

class PerfectScore implements AchievementContract
{

    public function slug(): string
    {
        return 'perfect-score';
    }

    public function check(User $user, array $context = []): bool
    {
        // ambil semua submission user, lalu cek apakah ada minimal 5 submission yang memiliki score 100 atau lebih
        $submissions = $user->quizSubmissions()
            ->where('score', '>=', 100)
            ->get();

        return $submissions->count() >= 5;
    }
}
