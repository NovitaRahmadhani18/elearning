<?php

namespace App\Services;

use App\Models\User;
use App\Achievements\QuizChampionAchievement;
use App\Achievements\FastLearnerAchievement;
use App\Achievements\PerfectScoreAchievement;
use App\Achievements\StreakMasterAchievement;
use App\Achievements\TopRankAchievement;
use Illuminate\Support\Facades\Log;
use LevelUp\Experience\Models\Achievement;

class AchievementService
{
    /**
     * All available achievement classes
     */
    protected array $achievementClasses = [
        QuizChampionAchievement::class,
        FastLearnerAchievement::class,
        PerfectScoreAchievement::class,
        StreakMasterAchievement::class,
        TopRankAchievement::class,
    ];

    /**
     * Check all achievements for a user after quiz completion
     */
    public function checkAchievementsForUser(User $user): array
    {
        $newAchievements = [];

        foreach ($this->achievementClasses as $achievementClass) {
            $achievement = new $achievementClass();
            
            // Check if user already has this achievement
            if ($this->userHasAchievement($user, $achievement->name)) {
                continue;
            }

            // Check if user qualifies for this achievement
            if ($achievement->qualifier($user)) {
                $this->grantAchievement($user, $achievement);
                $newAchievements[] = $achievement->name;
                
                Log::info("Achievement granted: {$achievement->name} to user {$user->id}");
            }
        }

        return $newAchievements;
    }

    /**
     * Check if user already has a specific achievement
     */
    protected function userHasAchievement(User $user, string $achievementName): bool
    {
        return $user->achievements()
            ->where('name', $achievementName)
            ->exists();
    }

    /**
     * Grant an achievement to a user
     */
    protected function grantAchievement(User $user, $achievementInstance): void
    {
        // Find or create the achievement in database
        $achievement = Achievement::firstOrCreate([
            'name' => $achievementInstance->name,
        ], [
            'description' => $achievementInstance->description,
            'image' => $achievementInstance->image,
            'is_secret' => $achievementInstance->secret,
        ]);

        // Attach achievement to user if not already attached
        if (!$user->achievements()->where('achievement_id', $achievement->id)->exists()) {
            $user->achievements()->attach($achievement->id, [
                'progress' => 100, // 100% complete since it's unlocked
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Give experience points
            $user->giveExperience($achievementInstance->points, 'Achievement: ' . $achievementInstance->name);
            
            // Flash a success message for the user
            session()->flash('achievement_unlocked', [
                'name' => $achievementInstance->name,
                'description' => $achievementInstance->description,
                'points' => $achievementInstance->points,
            ]);
        }
    }

    /**
     * Get user's achievements with progress
     */
    public function getUserAchievements(User $user): array
    {
        $userAchievements = $user->achievements()->withPivot('progress', 'created_at')->get();
        $allAchievements = Achievement::all();

        $result = [];

        foreach ($allAchievements as $achievement) {
            $userAchievement = $userAchievements->where('id', $achievement->id)->first();
            
            $result[] = [
                'id' => $achievement->id,
                'name' => $achievement->name,
                'description' => $achievement->description,
                'image' => $achievement->image,
                'is_secret' => $achievement->is_secret,
                'unlocked' => $userAchievement !== null,
                'progress' => $userAchievement ? $userAchievement->pivot->progress : 0,
                'unlocked_at' => $userAchievement ? $userAchievement->pivot->created_at : null,
            ];
        }

        return $result;
    }

    /**
     * Force check all achievements for all users (useful for migration/updates)
     */
    public function checkAllUsersAchievements(): void
    {
        User::whereHas('quizSubmissions')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $this->checkAchievementsForUser($user);
            }
        });
    }
}