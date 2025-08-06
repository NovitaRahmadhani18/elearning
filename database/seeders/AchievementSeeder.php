<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use LevelUp\Experience\Models\Achievement;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'Quiz Champion',
                'description' => 'Menyelesaikan kuis dengan nilai ≥ 85',
                'image' => '/images/achievements/Quiz Champion.png',
                'is_secret' => false,
            ],
            [
                'name' => 'Fast Learner',
                'description' => 'Menyelesaikan kuis dalam waktu < 10 menit',
                'image' => '/images/achievements/Fast Learner.png',
                'is_secret' => false,
            ],
            [
                'name' => 'Perfect Score',
                'description' => 'Mendapatkan nilai sempurna (100) pada salah satu kuis',
                'image' => '/images/achievements/Perfect Score.png',
                'is_secret' => false,
            ],
            [
                'name' => 'Streak Master',
                'description' => 'Mengerjakan kuis 5 hari berturut-turut',
                'image' => '/images/achievements/Streak master.png',
                'is_secret' => false,
            ],
            [
                'name' => 'Top Rank',
                'description' => 'Berada di peringkat 3 besar leaderboard',
                'image' => '/images/achievements/Top Rank.png',
                'is_secret' => false,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::firstOrCreate(
                ['name' => $achievement['name']],
                $achievement
            );
        }
    }
}

