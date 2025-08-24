<?php

namespace App\Providers;

use App\Services\AchievementService;
use App\Events\ContentCompleted;
use App\Listeners\ProcessContentCompletion;
use App\Listeners\LogSuccessfulLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AchievementService::class, function ($app) {
            return new AchievementService(); // Uses default achievements
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            ContentCompleted::class,
            ProcessContentCompletion::class
        );

        Event::listen(
            Login::class,
            LogSuccessfulLogin::class
        );
    }
}
