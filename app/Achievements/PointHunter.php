<?php

namespace App\Achievements;

use App\Contracts\AchievementContract;
use App\Models\User;

class PointHunter implements AchievementContract
{
    public function slug(): string
    {
        return 'point-hunter';
    }

    public function check(User $user, array $context = []): bool
    {
        // check apakah user memiliki minimal 1000 poin
        return $user->total_points >= 1000;
    }
}
