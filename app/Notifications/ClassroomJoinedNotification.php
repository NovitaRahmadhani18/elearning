<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Classroom;

class ClassroomJoinedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $classroom;

    public function __construct(User $user, Classroom $classroom)
    {
        $this->user = $user;
        $this->classroom = $classroom;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'classroom_joined',
            'title' => 'New Student Joined',
            'message' => "{$this->user->name} joined your classroom: {$this->classroom->title}",
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'classroom_id' => $this->classroom->id,
            'classroom_title' => $this->classroom->title,
        ];
    }
}
