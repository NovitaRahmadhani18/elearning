<?php

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
        Route::resource('classroom', App\Http\Controllers\Admin\ClassroomController::class);
        Route::resource('monitoring', App\Http\Controllers\Admin\MonitoringController::class);
        Route::resource('system', App\Http\Controllers\Admin\SystemController::class);
    }
);
