<?php

use App\Livewire\InteractiveQuiz;
use App\Livewire\StartQuiz;
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

        Route::get('join/{classroom:invite_code}', [App\Http\Controllers\User\ClassroomController::class, 'joinForm'])
            ->name('classroom.join.form');

        Route::post('join/{classroom:invite_code}', [App\Http\Controllers\User\ClassroomController::class, 'join'])
            ->name('classroom.join.submit');

        Route::resource('classroom', App\Http\Controllers\User\ClassroomController::class)->except(['show']);

        Route::middleware(['classroom.enrollment'])
            ->group(function () {
                Route::get('classroom/{classroom}', [App\Http\Controllers\User\ClassroomController::class, 'show'])
                    ->name('classroom.show');
                Route::get('classroom/{classroom}/material/{material}', [App\Http\Controllers\User\ClassroomController::class, 'showMaterial'])
                    ->name('classroom.material.show')->middleware('classroom.content.lock');
                Route::get('classroom/{classroom}/quiz/{quiz}', [App\Http\Controllers\User\ClassroomController::class, 'showQuiz'])
                    ->name('classroom.quiz.show')->middleware('classroom.content.lock');
                Route::get('classroom/{classroom}/quiz/{quiz}/start', StartQuiz::class)
                    ->name('classroom.quiz.start')->middleware('classroom.content.lock');
                Route::get('classroom/{classroom}/quiz/{quiz}/review', [App\Http\Controllers\User\ClassroomController::class, 'reviewQuiz'])
                    ->name('classroom.quiz.review')->middleware('classroom.content.lock');
            });


        Route::resource('leaderboard', App\Http\Controllers\User\LeaderboardController::class);
        Route::resource('lencana', App\Http\Controllers\User\LencanaController::class);
        Route::resource('profile', App\Http\Controllers\User\ProfileController::class);
    }
);
