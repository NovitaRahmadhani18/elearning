<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\QuizSubmission;
use App\Models\ClassroomStudent;
use App\Models\Content;
use App\Observers\QuizSubmissionObserver;
use App\Observers\ClassroomActivityObserver;
use App\Observers\ContentCompletionObserver;
use App\Services\AchievementService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register AchievementService as singleton
        $this->app->singleton(AchievementService::class, function ($app) {
            return new AchievementService();
        });

        // Register ActivityService as singleton
        $this->app->singleton(\App\Services\ActivityService::class, function ($app) {
            return new \App\Services\ActivityService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        /* QuizSubmission::observe(QuizSubmissionObserver::class); */
        /* ClassroomStudent::observe(ClassroomActivityObserver::class); */
        /* Content::observe(ContentCompletionObserver::class); */

        // mengambil menu dari file JSON
        $adminMenu = json_decode(file_get_contents(base_path('resources/menu/admin.json')));
        $teacherMenu = json_decode(file_get_contents(base_path('resources/menu/teacher.json')));
        $userMenu = json_decode(file_get_contents(base_path('resources/menu/user.json')));

        // membagikan menu ke seluruh aplikasi
        $this->app->make('view')->share('menu', [
            'adminMenu' => $adminMenu,
            'teacherMenu' => $teacherMenu,
            'userMenu' => $userMenu,
        ]);
    }
}
