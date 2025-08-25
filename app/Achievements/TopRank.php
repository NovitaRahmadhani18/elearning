<?php

namespace App\Achievements;

use App\Contracts\AchievementContract;
use App\Models\User;
use App\Services\LeaderboardService;

class TopRank implements AchievementContract
{
    public function __construct(protected LeaderboardService $leaderboardService) {}

    public function slug(): string
    {
        return 'top-rank';
    }

    public function check(User $user, array $context = []): bool
    {
        if (!isset($context['submission'])) {
            return false;
        }

        $submission = $context['submission'];
        $content = $submission->quiz->content;

        $leaderboard = $this->leaderboardService->getLeaderboardForContent($content);

        // Get the top 3 user IDs from the leaderboard
        $topThree = $leaderboard->take(3)->pluck('user.id');

        return $topThree->contains($user->id);
    }
}
