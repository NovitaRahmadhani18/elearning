<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'teacher',
        'as' => 'teacher.',
        'middleware' => [
            'auth',
            /* 'role:teacher' */
        ],
    ],
    function () {
        Route::get('/', [App\Http\Controllers\DashboardController::class, 'teacherDashboard'])->name('dashboard');
        Route::resource('material', App\Http\Controllers\Teacher\MaterialController::class);
        Route::resource('quizes', App\Http\Controllers\Teacher\QuizesController::class);
        Route::resource('student', App\Http\Controllers\Teacher\StudentController::class);
    }
);
