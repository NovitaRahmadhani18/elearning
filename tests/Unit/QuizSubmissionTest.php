<?php

use App\Models\QuizSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('formats time_spent as mm:ss and clamps negatives to 00:00', function () {
    // Negative clamps to 0
    $submission = new QuizSubmission(['time_spent' => -5]);
    expect($submission->time_spent_formatted)->toBe('00:00');

    // Zero
    $submission = new QuizSubmission(['time_spent' => 0]);
    expect($submission->time_spent_formatted)->toBe('00:00');

    // Under a minute
    $submission = new QuizSubmission(['time_spent' => 7]);
    expect($submission->time_spent_formatted)->toBe('00:07');

    // Over a minute
    $submission = new QuizSubmission(['time_spent' => 65]);
    expect($submission->time_spent_formatted)->toBe('01:05');

    // Several minutes
    $submission = new QuizSubmission(['time_spent' => 372]);
    expect($submission->time_spent_formatted)->toBe('06:12');
});
