<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\ContentController as StudentContentController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // admin
    Route::group(
        [
            'prefix' => 'admin',
            'as' => 'admin.',
            'middleware' => 'role:admin'
        ],
        function () {
            Route::get('dashboard', [DashboardController::class, 'admin'])->name('dashboard');
            Route::resource('users', \App\Http\Controllers\Admin\UsersController::class);
            Route::resource('classrooms', \App\Http\Controllers\Admin\ClassroomController::class);

            Route::get('monitoring', [\App\Http\Controllers\Admin\MonitoringController::class, 'index'])->name('monitoring.index');
        }
    );

    // teacher
    Route::group(
        [
            'prefix' => 'teacher',
            'as' => 'teacher.',
            'middleware' => 'role:teacher'
        ],
        function () {
            Route::resource('classrooms', \App\Http\Controllers\Teacher\ClassroomController::class);
            Route::resource('materials', \App\Http\Controllers\Teacher\MaterialController::class)->parameters(['materials' => 'content']);
            Route::resource('quizzes', \App\Http\Controllers\Teacher\QuizController::class)->parameters(['quizzes' => 'content']);

            Route::get('student-tracking', [\App\Http\Controllers\Teacher\StudentTrackingController::class, 'index'])->name('student-tracking.index');
        }
    );

    // student
    Route::group(
        [
            'prefix' => 'student',
            'as' => 'student.',
            'middleware' => 'role:student'
        ],
        function () {
            Route::get('dashboard', [DashboardController::class, 'student'])->name('dashboard');
            Route::get('classrooms', [\App\Http\Controllers\Student\ClassroomController::class, 'index'])->name('classrooms.index');
            Route::get('classrooms/join/{classroom:invite_code}', [\App\Http\Controllers\Student\ClassroomController::class, 'joinForm'])
                ->name('classrooms.join.form');

            Route::post('classrooms/join/{classroom:invite_code}', [\App\Http\Controllers\Student\ClassroomController::class, 'join'])
                ->name('classrooms.join.store');

            Route::get('classrooms/{classroom}', [\App\Http\Controllers\Student\ClassroomController::class, 'show'])
                ->name('classrooms.show');

            // content

            Route::get('/contents/{content}', [StudentContentController::class, 'show'])
                ->name('contents.show');

            // quizzes
            Route::get('/quizzes/{content}/start', [\App\Http\Controllers\Student\ContentController::class, 'startQuiz'])
                ->name('quizzes.start');

            Route::post('/quizzes/{content}/answer', [\App\Http\Controllers\Student\ContentController::class, 'submitAnswer'])->name('quizzes.answer');

            Route::get('/quizzes/{content}/result', [\App\Http\Controllers\Student\ContentController::class, 'resultQuiz'])
                ->name('quizzes.result');

            // leaderboard
            Route::get('leaderboard', [\App\Http\Controllers\Student\LeaderboardController::class, 'index'])->name('leaderboard.index');

            // achievements
            Route::get('achievements', [\App\Http\Controllers\Student\AchievementController::class, 'index'])->name('achievements.index');
        }
    );


    Route::post('/images/upload', [\App\Http\Controllers\ImageUploadController::class, 'store'])
        ->name('images.upload');

    Route::middleware([
        'role:admin,teacher'
    ], function () {
        Route::post('/clasrooms/{classroom}/generate-invite-code', [\App\Http\Controllers\Teacher\ClassroomController::class, 'generateInviteCode'])
            ->name('classrooms.generate-invite-code');

        Route::post('/classrooms/{classroom}/generate-code', [\App\Http\Controllers\Teacher\ClassroomController::class, 'generateCode'])
            ->name('classrooms.generate-code');
    });
});



require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
