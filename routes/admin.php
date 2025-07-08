<?php

use App\Http\Controllers\Admin\ClassroomController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'admin',
        'as' => 'admin.',
        'middleware' => [
            'auth',
            /* 'role:admin' */
        ],
    ],
    function () {
        Route::get('/', [App\Http\Controllers\DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::resource('users', App\Http\Controllers\Admin\UserManagementController::class);
        Route::resource('classroom', ClassroomController::class);
        Route::put('classrooms/{classroom}/students', [ClassroomController::class, 'syncStudents'])->name('classroom.students.sync');
        Route::resource('monitoring', App\Http\Controllers\Admin\MonitoringController::class);
        Route::resource('system', App\Http\Controllers\Admin\SystemController::class);
    }
);
