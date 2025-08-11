<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\QuizSubmission;

class QuizCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $quizSubmission;

    public function __construct(QuizSubmission $quizSubmission)
    {
        $this->quizSubmission = $quizSubmission;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $quiz = $this->quizSubmission->quiz;
        $user = $this->quizSubmission->user;

        return [
            'type' => 'quiz_completed',
            'title' => 'Quiz Completed',
            'message' => "{$user->name} completed the quiz: {$quiz->title}",
            'quiz_id' => $quiz->id,
            'quiz_title' => $quiz->title,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'score' => $this->quizSubmission->score,
            'score_percentage' => $this->quizSubmission->score_percentage,
            'completed_at' => $this->quizSubmission->completed_at?->toDateTimeString(),
        ];
    }
}
