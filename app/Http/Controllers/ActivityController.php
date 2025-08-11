<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    protected ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Display user's activity dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();

        $stats = $this->activityService->getUserActivityStats($user);
        $recentActivities = $this->activityService->getRecentActivitiesForUser($user, 10);
        $learningStreak = $this->activityService->getUserLearningStreak($user);

        // Format activities for display
        $formattedActivities = $recentActivities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'description' => $this->activityService->getFormattedDescription($activity),
                'log_name' => $activity->log_name,
                'created_at' => $activity->created_at,
                'time_ago' => $activity->created_at->diffForHumans(),
                'properties' => $activity->properties,
            ];
        });

        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'stats' => $stats,
            'learning_streak' => $learningStreak,
            'recent_activities' => $formattedActivities,
        ]);
    }

    /**
     * Get activities by type
     */
    public function getActivitiesByType(Request $request, string $type)
    {
        $user = auth()->user();
        $perPage = $request->get('per_page', 15);

        $activities = match ($type) {
            'quiz' => $this->activityService->getQuizActivitiesForUser($user, $perPage),
            'material' => $this->activityService->getMaterialActivitiesForUser($user, $perPage),
            'classroom' => $this->activityService->getClassroomActivitiesForUser($user, $perPage),
            'achievement' => $this->activityService->getAchievementActivitiesForUser($user, $perPage),
            default => $this->activityService->getActivitiesForUser($user, $perPage),
        };

        $formattedActivities = $activities->getCollection()->map(function ($activity) {
            return [
                'id' => $activity->id,
                'description' => $this->activityService->getFormattedDescription($activity),
                'log_name' => $activity->log_name,
                'created_at' => $activity->created_at,
                'time_ago' => $activity->created_at->diffForHumans(),
                'properties' => $activity->properties,
            ];
        });

        return response()->json([
            'type' => $type,
            'activities' => $formattedActivities,
            'pagination' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ]
        ]);
    }

    /**
     * Get classroom activities (for teachers)
     */
    public function getClassroomActivities(Request $request, int $classroomId)
    {
        // Add authorization check here (ensure user is teacher of this classroom)

        $perPage = $request->get('per_page', 15);
        $activities = $this->activityService->getClassroomActivities($classroomId, $perPage);

        $formattedActivities = $activities->getCollection()->map(function ($activity) {
            return [
                'id' => $activity->id,
                'description' => $this->activityService->getFormattedDescription($activity),
                'log_name' => $activity->log_name,
                'user' => $activity->causer ? [
                    'id' => $activity->causer->id,
                    'name' => $activity->causer->name,
                ] : null,
                'created_at' => $activity->created_at,
                'time_ago' => $activity->created_at->diffForHumans(),
                'properties' => $activity->properties,
            ];
        });

        return response()->json([
            'classroom_id' => $classroomId,
            'activities' => $formattedActivities,
            'pagination' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ]
        ]);
    }

    /**
     * Get activity statistics for admin dashboard
     */
    public function getSystemStats()
    {
        $totalActivities = Activity::count();
        $todayActivities = Activity::whereDate('created_at', today())->count();
        $weekActivities = Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        $activitiesByType = Activity::selectRaw('log_name, COUNT(*) as count')
            ->groupBy('log_name')
            ->get()
            ->pluck('count', 'log_name');

        $dailyActivities = Activity::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'total_activities' => $totalActivities,
            'today_activities' => $todayActivities,
            'week_activities' => $weekActivities,
            'activities_by_type' => $activitiesByType,
            'daily_activities_last_30_days' => $dailyActivities,
        ]);
    }
}
