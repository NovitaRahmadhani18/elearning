<?php

namespace App\Services;

use App\Achievements\FastLearner;
use App\Achievements\PerfectScore;
use App\Achievements\QuizChampion;
use App\Achievements\StreakMaster;
use App\Achievements\TopRank;
use App\Contracts\AchievementContract;
use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\QuizSubmission;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AchievementService
{
    /**
     * The registered achievement instances.
     *
     * @var array<AchievementContract>
     */
    protected array $achievements;

    /**
     * Create a new AchievementService instance.
     *
     * @param array<AchievementContract> $achievements
     */
    public function __construct()
    {
        $this->achievements = [
            QuizChampion::class,
            FastLearner::class,
            PerfectScore::class,
            StreakMaster::class,
            TopRank::class,
        ];
    }

    public function processQuizCompletion(User $user, QuizSubmission $submission): void
    {
        foreach ($this->achievements as $achievement) {
            $context = ['submission' => $submission];

            $achievement = app($achievement);

            if ($achievement->check($user, $context)) {
                $this->award($user, $achievement);
            }
        }
    }

    public function processUserActivity(User $user): void
    {
        foreach ($this->achievements as $achievement) {

            $achievement = app($achievement);

            if ($achievement->check($user)) {
                $this->award($user, $achievement);
            }
        }
    }

    private function award(User $user, AchievementContract $achievementInstance): void
    {
        $achievementModel = Achievement::firstWhere('slug', $achievementInstance->slug());

        if (!$achievementModel) {
            Log::warning('Achievement not found in database.', ['slug' => $achievementInstance->slug()]);
            return;
        }

        // Check if the user already has this achievement
        if ($user->achievements()->where('achievement_id', $achievementModel->id)->exists()) {
            return; // Already awarded
        }

        // Award the achievement
        $user->achievements()->attach($achievementModel->id, ['unlocked_at' => now()]);

        // Award points
        $user->increment('total_points', $achievementModel->points_reward);

        Log::info('Achievement awarded.', ['user_id' => $user->id, 'achievement' => $achievementInstance->slug()]);

        // Dispatch an event
        AchievementUnlocked::dispatch($user, $achievementModel);
    }
}
