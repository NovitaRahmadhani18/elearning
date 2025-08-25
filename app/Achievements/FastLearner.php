<?php

namespace App\Achievements;

use App\Contracts\AchievementContract;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FastLearner implements AchievementContract
{
    public function slug(): string
    {
        return 'fast-learner';
    }

    public function check(User $user, array $context = []): bool
    {
        if (!isset($context['submission'])) {
            return false;
        }

        return $context['submission']->duration_seconds < 600;
    }
}
