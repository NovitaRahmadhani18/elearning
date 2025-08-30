<?php

namespace App\Achievements;

use App\Contracts\AchievementContract;
use App\Models\User;

class QuizChallenger implements AchievementContract
{
    public function slug(): string
    {
        return 'quiz-challenger';
    }

    public function check(User $user, array $context = []): bool
    {
        // ambil semua submission user, lalu cek apakah ada minimal 5 submission yang memiliki score 100 atau lebih
        $submissions = $user->quizSubmissions()
            ->where('score', '>=', 80)
            ->get();

        return $submissions->count() >= 10;
    }
}
