<?php

namespace App\Notifications\Notifications;


use App\Models\Classroom;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewClassroomAssignment extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Classroom $classroom)
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
            'title'   => 'New Classroom Assignment',
            'message' => 'You have been assigned to a new classroom: ' . $this->classroom->name,
            'icon'    => 'book',
            'link'    => route('teacher.classrooms.show', $this->classroom->id),
            'type'    => 'new_classroom',
        ];
    }
}
