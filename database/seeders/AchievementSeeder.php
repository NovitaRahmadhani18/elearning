<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* - Quiz Challenger: “Beat 10 quizzes with 80+. Challenge conquered!”. */
        /**/
        /* - Knowledge Explorer : “Read 20 materials. You’re officially a knowledge adventurer!”. */
        /**/
        /* - Top Ranker : “Reach the Top 3. Rule the leaderboard!”.  */
        /**/
        /* - Point Hunter : “Collect 1000 points. You’re a real point master!”.   */
        /**/
        /* - legendary learner : “Earn every badge. Respect!” */
        /**/
        /* - Perfect Score : “Score 100 in 5 quizzes. Flawless victory!” */

        $achievements = [
            [
                'slug' => 'quiz-challenger',
                'name' => 'Quiz Challenger',
                'description' => 'Beat 10 quizzes with 80+. Challenge conquered!',
                'icon_path' => '/images/achievements_rev/quiz-challenger.png',
                'points_reward' => 40,
            ],
            [
                'slug' => 'knowledge-explorer',
                'name' => 'Knowledge Explorer',
                'description' => 'Read 20 materials. You’re officially a knowledge adventurer!',
                'icon_path' => '/images/achievements_rev/knowledge-explorer.png',
                'points_reward' => 30,
            ],
            [
                'slug' => 'top-ranker',
                'name' => 'Top Ranker',
                'description' => 'Reach the Top 3. Rule the leaderboard!',
                'icon_path' => '/images/achievements_rev/top-rank.png',
                'points_reward' => 50,
            ],
            [
                'slug' => 'point-hunter',
                'name' => 'Point Hunter',
                'description' => 'Collect 1000 points. You’re a real point master!',
                'icon_path' => '/images/achievements_rev/point-hunter.png',
                'points_reward' => 60,
            ],
            [
                'slug' => 'legendary-learner',
                'name' => 'Legendary Learner',
                'description' => "Earn every badge. Respect!",
                'icon_path' => '/images/achievements_rev/legendary-learner.png',
                'points_reward' => 100,
            ],
            [
                'slug' => 'perfect-score',
                'name' => "Perfect Score",
                'description' => "Score 100 in 5 quizzes. Flawless victory!",
                'icon_path' => '/images/achievements_rev/perfect-score.png',
                'points_reward' => 70,
            ]

        ];



        foreach ($achievements as $achievement) {
            \App\Models\Achievement::updateOrCreate(['slug' => $achievement['slug']], $achievement);
        }
    }
}
