<?php

use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Services\AchievementService;
use LevelUp\Experience\Models\Achievement;

it('can award Quiz Champion achievement for score >= 85', function () {
    $user = User::factory()->create();
    $quiz = Quiz::factory()->create();
    
    // Create a quiz submission with score >= 85
    QuizSubmission::create([
        'user_id' => $user->id,
        'quiz_id' => $quiz->id,
        'score' => 90,
        'total_questions' => 10,
        'correct_answers' => 9,
        'time_spent' => 300,
        'is_completed' => true,
        'started_at' => now()->subMinutes(5),
        'completed_at' => now(),
    ]);

    $achievementService = new AchievementService();
    $newAchievements = $achievementService->checkAchievementsForUser($user);

    expect($newAchievements)->toContain('Quiz Champion');
    expect($user->achievements()->where('name', 'Quiz Champion')->exists())->toBeTrue();
});

it('can award Fast Learner achievement for completion < 10 minutes', function () {
    $user = User::factory()->create();
    $quiz = Quiz::factory()->create();
    
    // Create a quiz submission completed in less than 10 minutes (600 seconds)
    QuizSubmission::create([
        'user_id' => $user->id,
        'quiz_id' => $quiz->id,
        'score' => 75,
        'total_questions' => 10,
        'correct_answers' => 7,
        'time_spent' => 450, // 7.5 minutes
        'is_completed' => true,
        'started_at' => now()->subMinutes(8),
        'completed_at' => now(),
    ]);

    $achievementService = new AchievementService();
    $newAchievements = $achievementService->checkAchievementsForUser($user);

    expect($newAchievements)->toContain('Fast Learner');
    expect($user->achievements()->where('name', 'Fast Learner')->exists())->toBeTrue();
});

it('can award Perfect Score achievement for score = 100', function () {
    $user = User::factory()->create();
    $quiz = Quiz::factory()->create();
    
    // Create a quiz submission with perfect score
    QuizSubmission::create([
        'user_id' => $user->id,
        'quiz_id' => $quiz->id,
        'score' => 100,
        'total_questions' => 10,
        'correct_answers' => 10,
        'time_spent' => 600,
        'is_completed' => true,
        'started_at' => now()->subMinutes(10),
        'completed_at' => now(),
    ]);

    $achievementService = new AchievementService();
    $newAchievements = $achievementService->checkAchievementsForUser($user);

    expect($newAchievements)->toContain('Perfect Score');
    expect($user->achievements()->where('name', 'Perfect Score')->exists())->toBeTrue();
});

it('does not award achievements twice', function () {
    $user = User::factory()->create();
    $quiz = Quiz::factory()->create();
    
    // Create achievement first
    $achievement = Achievement::create([
        'name' => 'Quiz Champion',
        'description' => 'Menyelesaikan kuis dengan nilai ≥ 85',
        'image' => '/images/achievements/quiz-champion.svg',
    ]);
    
    // Award achievement manually
    $user->achievements()->attach($achievement->id, ['progress' => 100]);
    
    // Create qualifying submission
    QuizSubmission::create([
        'user_id' => $user->id,
        'quiz_id' => $quiz->id,
        'score' => 90,
        'total_questions' => 10,
        'correct_answers' => 9,
        'time_spent' => 300,
        'is_completed' => true,
        'started_at' => now()->subMinutes(5),
        'completed_at' => now(),
    ]);

    $achievementService = new AchievementService();
    $newAchievements = $achievementService->checkAchievementsForUser($user);

    expect($newAchievements)->not->toContain('Quiz Champion');
    expect($user->achievements()->where('name', 'Quiz Champion')->count())->toBe(1);
});