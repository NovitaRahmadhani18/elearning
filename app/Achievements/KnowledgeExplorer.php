<?php

namespace App\Achievements;

use App\Contracts\AchievementContract;
use App\Models\Achievement;
use App\Models\User;

class KnowledgeExplorer implements AchievementContract
{
    public function slug(): string
    {
        return 'knowledge-explorer';
    }

    public function check(User $user, array $context = []): bool
    {
        // ambil semua jumlah achievement yang dimiliki user, dan bandingkan dengan jumlah total achievement yang ada
        $achievementsCount = $user->achievements()->count();
        $totalAchievements = Achievement::count();

        // jika jumlah achievement yang dimiliki user lebih dari atau sama dengan 5, maka achievement ini didapatkan
        return $totalAchievements > 0 && $achievementsCount >= $totalAchievements - 1;
    }
}
