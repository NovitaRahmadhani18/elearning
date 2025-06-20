<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'user',
        'as' => 'user.',
        'middleware' => [
            'auth',
            /* 'role:user' */
        ],
    ],
    function () {
        Route::get('/', [App\Http\Controllers\DashboardController::class, 'userDashboard'])->name('dashboard');
        Route::resource('classroom', App\Http\Controllers\User\ClassroomController::class);
        Route::resource('leaderboard', App\Http\Controllers\User\LeaderboardController::class);
        Route::resource('lencana', App\Http\Controllers\User\LencanaController::class);
        Route::resource('profile', App\Http\Controllers\User\ProfileController::class);
    }
);
