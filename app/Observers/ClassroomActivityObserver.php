<?php

namespace App\Observers;

use App\Models\ClassroomStudent;
use App\Notifications\ClassroomJoinedNotification;
use Illuminate\Support\Facades\Log;

class ClassroomActivityObserver
{
    /**
     * Handle the ClassroomStudent "created" event.
     */
    public function created(ClassroomStudent $classroomStudent): void
    {
        $this->logClassroomJoin($classroomStudent);
        $this->sendClassroomJoinNotification($classroomStudent);
    }

    /**
     * Send notification to teacher when student joins classroom
     */
    protected function sendClassroomJoinNotification(ClassroomStudent $classroomStudent): void
    {
        try {
            $user = $classroomStudent->user;
            $classroom = $classroomStudent->classroom;
            $teacher = $classroom?->teacher;

            if ($teacher && $user) {
                $teacher->notify(new ClassroomJoinedNotification($user, $classroom));
                Log::info("Classroom join notification sent to teacher {$teacher->id} for user {$user->id} joining classroom {$classroom->id}");
            }
        } catch (\Exception $e) {
            Log::error("Error sending classroom join notification for ClassroomStudent {$classroomStudent->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the ClassroomStudent "deleted" event.
     */
    public function deleted(ClassroomStudent $classroomStudent): void
    {
        $this->logClassroomLeave($classroomStudent);
    }

    /**
     * Log detailed classroom join activity
     */
    protected function logClassroomJoin(ClassroomStudent $classroomStudent): void
    {
        try {
            Log::info("Logging classroom join activity for ClassroomStudent {$classroomStudent->id}");
            $user = $classroomStudent->user;
            $classroom = $classroomStudent->classroom;

            if (!$user || !$classroom) {
                Log::warning("Missing user or classroom data for classroom join activity");
                return;
            }

            // Get join method from request context if available
            $joinMethod = request()->has('invite_code') ? 'invite_code' : 'direct_invitation';
            $inviteCode = request()->input('invite_code');

            activity('classroom_activity')
                ->causedBy($user)
                ->performedOn($classroomStudent)
                ->withProperties([
                    'classroom_name' => $classroom->title,
                    'classroom_id' => $classroom->id,
                    'classroom_category' => $classroom->category ?? 'General',
                    'teacher_name' => $classroom->teacher?->name ?? 'Unknown Teacher',
                    'teacher_id' => $classroom->teacher_id,
                    'join_method' => $joinMethod,
                    'invite_code_used' => $inviteCode,
                    'user_role' => $user->getRoleNames()->first() ?? 'user',
                    'joined_at' => now()->toDateTimeString(),
                    'classroom_stats' => [
                        'total_students' => $classroom->students()->count(),
                        'total_contents' => $classroom->contents()->count(),
                        'total_quizzes' => $classroom->quizzes()->count(),
                    ]
                ])
                ->log(
                    $joinMethod === 'invite_code'
                        ? "Joined classroom '{$classroom->title}' using invite code"
                        : "Joined classroom '{$classroom->title}' via direct invitation"
                );

            Log::info("Classroom join activity logged for user {$user->id} joining classroom {$classroom->id} via {$joinMethod}");
        } catch (\Exception $e) {
            Log::error("Error logging classroom join activity for ClassroomStudent {$classroomStudent->id}: " . $e->getMessage());
        }
    }

    /**
     * Log detailed classroom leave activity
     */
    protected function logClassroomLeave(ClassroomStudent $classroomStudent): void
    {
        try {
            $user = $classroomStudent->user;
            $classroom = $classroomStudent->classroom;

            if (!$user || !$classroom) {
                Log::warning("Missing user or classroom data for classroom leave activity");
                return;
            }

            // Calculate user's progress in this classroom before leaving
            $completedQuizzes = \App\Models\QuizSubmission::whereHas('quiz.contents', function ($query) use ($classroom) {
                $query->where('classroom_id', $classroom->id);
            })->where('user_id', $user->id)->where('is_completed', true)->count();

            $totalQuizzes = $classroom->quizzes()->count();

            activity('classroom_activity')
                ->causedBy($user)
                ->performedOn($classroomStudent)
                ->withProperties([
                    'classroom_name' => $classroom->title,
                    'classroom_id' => $classroom->id,
                    'classroom_category' => $classroom->category ?? 'General',
                    'teacher_name' => $classroom->teacher?->name ?? 'Unknown Teacher',
                    'teacher_id' => $classroom->teacher_id,
                    'user_role' => $user->getRoleNames()->first() ?? 'user',
                    'left_at' => now()->toDateTimeString(),
                    'progress_before_leaving' => [
                        'completed_quizzes' => $completedQuizzes,
                        'total_quizzes' => $totalQuizzes,
                        'completion_percentage' => $totalQuizzes > 0 ? round(($completedQuizzes / $totalQuizzes) * 100, 2) : 0,
                    ],
                    'classroom_stats' => [
                        'remaining_students' => $classroom->students()->count() - 1, // -1 because this user is leaving
                        'total_contents' => $classroom->contents()->count(),
                    ]
                ])
                ->log("Left classroom '{$classroom->title}' with {$completedQuizzes}/{$totalQuizzes} quizzes completed");

            Log::info("Classroom leave activity logged for user {$user->id} leaving classroom {$classroom->id}");
        } catch (\Exception $e) {
            Log::error("Error logging classroom leave activity for ClassroomStudent {$classroomStudent->id}: " . $e->getMessage());
        }
    }
}
