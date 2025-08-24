<?php

namespace App\Achievements;

use App\Contracts\AchievementContract;
use App\Models\User;

class PerfectScore implements AchievementContract
{
    public function slug(): string
    {
        return 'perfect-score';
    }

    public function check(User $user, array $context = []): bool
    {
        if (!isset($context['submission'])) {
            return false;
        }

        return $context['submission']->score == 100;
    }
}
