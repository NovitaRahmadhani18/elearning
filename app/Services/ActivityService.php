<?php

namespace App\Services;

use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityService
{
    /**
     * Get all activities for a specific user
     */
    public function getActivitiesForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Activity::where('causer_id', $user->id)
            ->where('causer_type', User::class)
            ->with(['subject', 'causer'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get activities by log name
     */
    public function getActivitiesByLogName(string $logName, int $perPage = 15): LengthAwarePaginator
    {
        return Activity::inLog($logName)
            ->with(['subject', 'causer'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get quiz completion activities for a user
     */
    public function getQuizActivitiesForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Activity::inLog('quiz_completion')
            ->where('causer_id', $user->id)
            ->where('causer_type', User::class)
            ->with(['subject', 'causer'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get material completion activities for a user
     */
    public function getMaterialActivitiesForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Activity::inLog('material_completion')
            ->where('causer_id', $user->id)
            ->where('causer_type', User::class)
            ->with(['subject', 'causer'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get classroom activities for a user
     */
    public function getClassroomActivitiesForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Activity::inLog('classroom_activity')
            ->where('causer_id', $user->id)
            ->where('causer_type', User::class)
            ->with(['subject', 'causer'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get achievement activities for a user
     */
    public function getAchievementActivitiesForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Activity::inLog('achievement_earned')
            ->where('causer_id', $user->id)
            ->where('causer_type', User::class)
            ->with(['subject', 'causer'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get recent activities for dashboard
     */
    public function getRecentActivitiesForUser(User $user, int $limit = 10): Collection
    {
        return Activity::where('causer_id', $user->id)
            ->where('causer_type', User::class)
            ->with(['subject', 'causer'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities for a specific classroom
     */
    public function getClassroomActivities(int $classroomId, int $perPage = 15): LengthAwarePaginator
    {
        return Activity::whereIn('log_name', ['quiz_completion', 'material_completion', 'classroom_activity'])
            ->where(function ($query) use ($classroomId) {
                $query->whereJsonContains('properties->classroom_id', $classroomId)
                    ->orWhereJsonContains('properties->classroom_name', function ($q) use ($classroomId) {
                        // This is a fallback, ideally we should always have classroom_id
                        $classroom = \App\Models\Classroom::find($classroomId);
                        return $classroom ? $classroom->title : null;
                    });
            })
            ->with(['subject', 'causer'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get activity statistics for a user
     */
    public function getUserActivityStats(User $user): array
    {
        $baseQuery = Activity::where('causer_id', $user->id)
            ->where('causer_type', User::class);

        return [
            'total_activities' => $baseQuery->count(),
            'quiz_completions' => $baseQuery->clone()->inLog('quiz_completion')->count(),
            'material_completions' => $baseQuery->clone()->inLog('material_completion')->count(),
            'classroom_joins' => $baseQuery->clone()->inLog('classroom_activity')
                ->where('description', 'like', '%joined%')->count(),
            'achievements_earned' => $baseQuery->clone()->inLog('achievement_earned')->count(),
            'perfect_scores' => $baseQuery->clone()->inLog('quiz_completion')
                ->whereJsonContains('properties->is_perfect_score', true)->count(),
            'high_scores' => $baseQuery->clone()->inLog('quiz_completion')
                ->whereJsonContains('properties->is_high_score', true)->count(),
        ];
    }

    /**
     * Get learning streak for a user based on activities
     */
    public function getUserLearningStreak(User $user): array
    {
        $activities = Activity::where('causer_id', $user->id)
            ->where('causer_type', User::class)
            ->whereIn('log_name', ['quiz_completion', 'material_completion'])
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->select('created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($activity) {
                return $activity->created_at->format('Y-m-d');
            });

        $currentStreak = 0;
        $longestStreak = 0;
        $tempStreak = 0;
        $lastDate = now()->format('Y-m-d');

        // Calculate current streak
        for ($i = 0; $i < 30; $i++) {
            $checkDate = now()->subDays($i)->format('Y-m-d');

            if ($activities->has($checkDate)) {
                if ($checkDate === $lastDate || now()->subDays($i + 1)->format('Y-m-d') === $lastDate) {
                    $currentStreak++;
                    $tempStreak++;
                    $lastDate = $checkDate;
                } else {
                    break;
                }
            } else {
                if ($i === 0) {
                    // No activity today, but check yesterday
                    continue;
                } else {
                    break;
                }
            }
        }

        // Calculate longest streak
        $tempStreak = 0;
        foreach ($activities->keys()->sort() as $date) {
            $tempStreak++;
            $longestStreak = max($longestStreak, $tempStreak);
        }

        return [
            'current_streak' => $currentStreak,
            'longest_streak' => $longestStreak,
            'active_days_this_month' => $activities->count(),
            'last_activity_date' => $activities->isNotEmpty()
                ? $activities->keys()->sort()->last()
                : null,
        ];
    }

    /**
     * Get formatted activity description for display
     */
    public function getFormattedDescription(Activity $activity): string
    {
        $properties = $activity->properties ?? collect();

        return match ($activity->log_name) {
            'quiz_completion' => $this->formatQuizActivity($activity, $properties),
            'material_completion' => $this->formatMaterialActivity($activity, $properties),
            'classroom_activity' => $this->formatClassroomActivity($activity, $properties),
            'achievement_earned' => $this->formatAchievementActivity($activity, $properties),
            default => $activity->description,
        };
    }

    /**
     * Format quiz completion activity
     */
    private function formatQuizActivity(Activity $activity, Collection $properties): string
    {
        $quizTitle = $properties->get('quiz_title', 'Unknown Quiz');
        $score = $properties->get('score_percentage', 0);
        $isPerfect = $properties->get('is_perfect_score', false);

        if ($isPerfect) {
            return "🎯 Perfect score ({$score}%) in '{$quizTitle}'";
        } elseif ($score >= 80) {
            return "⭐ High score ({$score}%) in '{$quizTitle}'";
        } else {
            return "✅ Completed '{$quizTitle}' with {$score}% score";
        }
    }

    /**
     * Format material completion activity
     */
    private function formatMaterialActivity(Activity $activity, Collection $properties): string
    {
        $contentTitle = $properties->get('content_title', 'Unknown Material');
        $points = $properties->get('points_earned', 0);

        if ($points > 0) {
            return "📚 Completed '{$contentTitle}' and earned {$points} points";
        } else {
            return "📚 Completed material '{$contentTitle}'";
        }
    }

    /**
     * Format classroom activity
     */
    private function formatClassroomActivity(Activity $activity, Collection $properties): string
    {
        $classroomName = $properties->get('classroom_name', 'Unknown Classroom');
        $joinMethod = $properties->get('join_method', 'direct');

        if (str_contains($activity->description, 'joined')) {
            return $joinMethod === 'invite_code'
                ? "🚪 Joined '{$classroomName}' using invite code"
                : "🚪 Joined '{$classroomName}' via invitation";
        } else {
            return "👋 Left classroom '{$classroomName}'";
        }
    }

    /**
     * Format achievement activity
     */
    private function formatAchievementActivity(Activity $activity, Collection $properties): string
    {
        $achievementName = $properties->get('achievement_name', 'Unknown Achievement');
        $triggerEvent = $properties->get('trigger_event', 'unknown');

        return "🏆 Earned '{$achievementName}' achievement from {$triggerEvent}";
    }
}
