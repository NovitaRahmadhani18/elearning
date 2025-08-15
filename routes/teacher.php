<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'teacher',
        'as' => 'teacher.',
        'middleware' => [
            'auth',
            'role:teacher'
        ],
    ],
    function () {
        Route::get('/', [App\Http\Controllers\DashboardController::class, 'teacherDashboard'])->name('dashboard');
        Route::resource('material', App\Http\Controllers\Teacher\MaterialController::class);

        Route::get('quizes/create', App\Livewire\CreateQuiz::class)->name('quizes.create');
        Route::get('quizes/{quiz}/edit', App\Livewire\EditQuiz::class)->name('quizes.edit');

        // Keep original routes for backward compatibility
        Route::resource('quizes', App\Http\Controllers\Teacher\QuizesController::class)->parameters([
            'quizes' => 'quiz'
        ])->except(['create', 'edit']);

        Route::resource('student', App\Http\Controllers\Teacher\StudentController::class);
        Route::resource('classroom', App\Http\Controllers\Teacher\ClassroomController::class);
    }
);
