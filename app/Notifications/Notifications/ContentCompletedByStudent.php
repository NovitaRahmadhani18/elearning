<?php

namespace App\Notifications\Notifications;


use App\Models\Content;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContentCompletedByStudent extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $student, public Content $content)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => 'Student Task Completed',
            'message' => "Student '{$this->student->name}' has completed the content '{$this->content->title}'.",
            'icon'    => 'award',
            'link'    => route('teacher.classrooms.showStudent', [$this->content->classroom->id, $this->student->id]),
            'type'    => 'content_completed',
        ];
    }
}
