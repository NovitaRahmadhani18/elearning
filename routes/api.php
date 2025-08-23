<?php

use App\Http\Controllers\Api\QuizSubmissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Quiz Submission API Routes
    Route::post('/quizzes/{content}/submissions', [QuizSubmissionController::class, 'start']);
    Route::get('/quiz-submissions/{submission}', [QuizSubmissionController::class, 'show']);
    Route::patch('/quiz-submissions/{submission}', [QuizSubmissionController::class, 'update']);
    Route::post('/quiz-submissions/{submission}/complete', [QuizSubmissionController::class, 'complete']);
});
