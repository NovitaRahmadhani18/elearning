<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
