<?php

namespace App\Contracts;

use App\Models\User;

interface AchievementContract
{
    /**
     * Returns the unique slug for the achievement.
     *
     * @return string
     */
    public function slug(): string;

    /**
     * Check if the user qualifies for the achievement.
     *
     * @param  User  $user
     * @param  array  $context
     * @return bool
     */
    public function check(User $user, array $context = []): bool;
}
