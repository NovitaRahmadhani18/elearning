<?php

namespace App\Achievements;

use App\Contracts\AchievementContract;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StreakMaster implements AchievementContract
{
    public function slug(): string
    {
        return 'streak-master';
    }

    public function check(User $user, array $context = []): bool
    {
        // This check is triggered by user activity (e.g., login), not quiz submission
        if (isset($context['submission'])) {
            return false;
        }

        $loginDates = DB::table('activity_log')
            ->where('user_id', $user->id)
            ->where('activity_type', 'user.login')
            ->orderByDesc('created_at')
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()
            ->limit(5)
            ->pluck('date');

        if ($loginDates->count() < 5) {
            return false;
        }

        // Check if the 5 dates are consecutive
        for ($i = 0; $i < 4; $i++) {
            $currentDate = Carbon::parse($loginDates[$i]);
            $previousDate = Carbon::parse($loginDates[$i + 1]);

            if ($currentDate->diffInDays($previousDate) !== 1) {
                return false;
            }
        }

        return true;
    }
}
