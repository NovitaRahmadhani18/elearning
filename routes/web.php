<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/* Route::get('/', function () { */
/*     return view('welcome'); */
/* }); */

/* Route::get('/dashboard', function () { */
/*     if (auth()->user()->hasRole('admin')) { */
/*         return redirect()->route('admin.dashboard'); */
/*     } elseif (auth()->user()->hasRole('teacher')) { */
/*         return redirect()->route('teacher.dashboard'); */
/*     } else { */
/*         return redirect()->route('student.dashboard'); */
/*     } */
/* })->middleware(['auth', 'verified'])->name('dashboard'); */

Route::middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('welcome');
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/teacher.php';
require __DIR__ . '/user.php';
