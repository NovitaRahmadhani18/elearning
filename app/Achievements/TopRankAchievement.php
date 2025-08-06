<?php

namespace App\Achievements;

use LevelUp\Experience\Contracts\Achievement;
use App\Models\User;

class TopRankAchievement
{
    public string $name = 'Top Rank';
    public string $description = 'Berada di peringkat 3 besar leaderboard';
    public string $image = '/images/achievements/top-rank.svg';
    public bool $secret = false;

    /**
     * The point value that will be added to the user when unlocked.
     */
    public int $points = 150;

    /**
     * Check if the user qualifies for this achievement.
     */
    public function qualifier(object $user): bool
    {
        $leaderboard = User::whereHas('quizSubmissions', function ($query) {
            $query->where('is_completed', true);
        })
            ->withAvg('quizSubmissions as avg_score', 'score')
            ->having('avg_score', '>', 0)
            ->orderBy('avg_score', 'desc')
            ->get();

        // Need at least 3 users to have a meaningful top 3
        if ($leaderboard->count() < 3) {
            return false;
        }

        // Find user's position in leaderboard (1-indexed)
        $userPosition = $leaderboard->search(function ($leaderboardUser) use ($user) {
            return $leaderboardUser->id === $user->id;
        });

        // Check if user is in top 3 (positions 0, 1, 2 in 0-indexed array)
        return $userPosition !== false && $userPosition < 3;
    }
}

