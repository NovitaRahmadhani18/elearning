<?php

use App\Models\Quiz;
use App\Models\User;
use App\Models\Classroom;
use App\Models\QuizSubmission;

// Test initialization
$user = User::find(1); // Adjust user ID as needed
$classroom = Classroom::find(1); // Adjust classroom ID as needed
$quiz = Quiz::find(1); // Adjust quiz ID as needed

if ($user && $classroom && $quiz) {
    // Delete existing submission to test fresh start
    QuizSubmission::where('quiz_id', $quiz->id)
        ->where('user_id', $user->id)
        ->delete();

    echo "Testing fresh quiz start...\n";

    // Create new submission
    $submission = QuizSubmission::create([
        'quiz_id' => $quiz->id,
        'user_id' => $user->id,
        'started_at' => now(),
        'total_questions' => $quiz->questions()->count(),
        'answers' => null
    ]);

    echo "Submission created:\n";
    echo "ID: " . $submission->id . "\n";
    echo "Is completed: " . ($submission->is_completed ? 'Yes' : 'No') . "\n";
    echo "Completed at: " . ($submission->completed_at ? $submission->completed_at : 'Not set') . "\n";
    echo "Answers: " . json_encode($submission->answers) . "\n";
    echo "Total questions: " . $submission->total_questions . "\n";

    // Test the logic
    $hasAnswers = $submission->answers && count($submission->answers) > 0;
    echo "Has answers: " . ($hasAnswers ? 'Yes' : 'No') . "\n";

    // Check current question index
    $currentQuestionIndex = $hasAnswers ? count($submission->answers) : 0;
    echo "Current question index: " . $currentQuestionIndex . "\n";

    // Check if completed
    $isCompleted = $submission->is_completed || $submission->completed_at;
    echo "Should be completed: " . ($isCompleted ? 'Yes' : 'No') . "\n";
} else {
    echo "Missing required data (user, classroom, or quiz)\n";
}
