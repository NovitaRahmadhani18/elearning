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
        $achievements = [
            [
                'slug' => 'quiz-champion',
                'name' => 'Quiz Champion',
                'description' => 'Menyelesaikan kuis dengan nilai ≥ 85',
                'icon_path' => '/images/achievements/quiz-champion.png',
                'points_reward' => 20,
            ],
            [
                'slug' => 'fast-learner',
                'name' => 'Fast Learner',
                'description' => 'Menyelesaikan kuis dalam waktu < 10 menit',
                'icon_path' => '/images/achievements/fast-learner.png',
                'points_reward' => 15,
            ],
            [
                'slug' => 'perfect-score',
                'name' => 'Perfect Score',
                'description' => 'Mendapatkan nilai sempurna (100) pada salah satu kuis',
                'icon_path' => '/images/achievements/perfect-score.png',
                'points_reward' => 30,
            ],
            [
                'slug' => 'streak-master',
                'name' => 'Streak Master',
                'description' => 'Mengerjakan kuis 5 hari berturut-turut',
                'icon_path' => '/images/achievements/streak-master.png',
                'points_reward' => 50,
            ],
            [
                'slug' => 'top-rank',
                'name' => 'Top Rank',
                'description' => 'Berada di peringkat 3 besar leaderboard',
                'icon_path' => '/images/achievements/top-rank.png',
                'points_reward' => 25,
            ],
        ];

        foreach ($achievements as $achievement) {
            \App\Models\Achievement::updateOrCreate(['slug' => $achievement['slug']], $achievement);
        }
    }
}
