<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\Content;
use App\Models\QuizSubmission;
use Illuminate\Support\Facades\Log;

class ExpiredQuizService
{
    /**
     * Auto-complete expired quiz for a user
     */
    public static function autoCompleteExpiredQuiz(Quiz $quiz, int $userId): bool
    {
        try {
            // Check if quiz has a deadline and it's passed
            if (!$quiz->due_time || !$quiz->due_time->isPast()) {
                return false;
            }

            // Check if user already has any submission for this quiz
            $existingSubmission = $quiz->submissions()
                ->where('user_id', $userId)
                ->first();

            // If user has no submission, create a completed one with 0 score
            if (!$existingSubmission) {
                $quiz->submissions()->create([
                    'user_id' => $userId,
                    'started_at' => now(),
                    'completed_at' => now(),
                    'score' => 0,
                    'total_questions' => $quiz->questions()->count(),
                    'correct_answers' => 0,
                    'time_spent' => 0,
                    'is_completed' => true,
                    'answers' => []
                ]);

                Log::info("Auto-completed expired quiz for user", [
                    'quiz_id' => $quiz->id,
                    'user_id' => $userId,
                    'reason' => 'No submission found, created new with 0 score'
                ]);
            } else if (!$existingSubmission->is_completed) {
                // If user has incomplete submission, complete it with current progress
                $existingSubmission->update([
                    'completed_at' => now(),
                    'is_completed' => true,
                    'score' => $existingSubmission->score ?: 0
                ]);

                Log::info("Auto-completed expired quiz for user", [
                    'quiz_id' => $quiz->id,
                    'user_id' => $userId,
                    'reason' => 'Incomplete submission found, completed with current progress'
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to auto-complete expired quiz", [
                'quiz_id' => $quiz->id,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check and auto-complete expired quiz content for a user
     */
    public static function handleExpiredQuizContent(Content $content, int $userId): bool
    {
        if ($content->contentable_type !== Quiz::class) {
            return false;
        }

        $quiz = $content->contentable;

        if (self::autoCompleteExpiredQuiz($quiz, $userId)) {
            // Mark the content as completed for the user
            $user = \App\Models\User::find($userId);
            if ($user) {
                $user->completedContents()->syncWithoutDetaching($content->id);
            }
            return true;
        }

        return false;
    }

    /**
     * Auto-complete all expired quizzes for a user in a classroom
     */
    public static function autoCompleteExpiredQuizzesInClassroom(int $classroomId, int $userId): int
    {
        $completedCount = 0;

        // Get all quiz contents in the classroom
        $quizContents = Content::where('classroom_id', $classroomId)
            ->where('contentable_type', Quiz::class)
            ->with('contentable')
            ->get();

        foreach ($quizContents as $content) {
            if (self::handleExpiredQuizContent($content, $userId)) {
                $completedCount++;
            }
        }

        if ($completedCount > 0) {
            Log::info("Auto-completed expired quizzes for user in classroom", [
                'classroom_id' => $classroomId,
                'user_id' => $userId,
                'completed_count' => $completedCount
            ]);
        }

        return $completedCount;
    }
}
