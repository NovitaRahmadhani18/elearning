<?php

namespace App\Achievements;

use App\Contracts\AchievementContract;
use App\Models\ContentStudent;
use App\Models\Material;
use App\Models\User;

class LegendaryLearner implements AchievementContract
{
    public function slug(): string
    {
        return 'legendary-learner';
    }

    public function check(User $user, array $context = []): bool
    {
        // cek apakah user telah menyelesaikan minimal 20 materi
        // ambil semua content user yang telah diselesaikan
        $contnent = ContentStudent::query()
            ->where('user_id', $user->id)
            ->whereHas('content', function ($query) {
                $query->where('contentable_type', Material::class);
            })
            ->where('status', ContentStudent::STATUS_COMPLETED)
            ->count();

        // jika jumlah content yang telah diselesaikan lebih dari atau sama dengan 20, maka achievement ini didapatkan
        return $contnent >= 20;
    }
}
