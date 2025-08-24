<?php

namespace App\Listeners;

use App\Services\AchievementService;
use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Log the activity
        app(ActivityLogService::class)->log(
            $user,
            'user.login',
            null,
            'User logged in.'
        );

        // Process achievements related to user activity
        app(AchievementService::class)->processUserActivity($user);
    }
}
