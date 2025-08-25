<?php

namespace App\Achievements;

use App\Contracts\AchievementContract;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $loginDates = ActivityLog::where('user_id', $user->id)
            ->where('activity_type', 'user.login')
            ->orderByDesc('created_at')
            ->get() // Get all relevant logs
            ->map(fn($log) => Carbon::parse($log->created_at)->toDateString())
            ->unique(); // Extract unique dates

        Log::info(('Count of login dates: ' . $loginDates->count()));

        if ($loginDates->count() < 5) {
            return false;
        }

        // Ensure the dates are sorted in descending order (most recent first)
        $loginDates = $loginDates->sortDesc()->values();

        Log::info('Login dates: ' . $loginDates->join(', '));

        // Check if the 5 dates are consecutive
        for ($i = 0; $i < 4; $i++) {
            $currentDate = Carbon::parse($loginDates[$i]);
            $previousDate = Carbon::parse($loginDates[$i + 1]);

            Log::info("Comparing {$currentDate->toDateString()} and {$previousDate->toDateString()}");
            Log::info('Difference in days: ' . $currentDate->diffInDays($previousDate));

            if ($currentDate->diffInDays($previousDate) !== 1) {
                return false;
            }
        }

        return true;
    }
}
