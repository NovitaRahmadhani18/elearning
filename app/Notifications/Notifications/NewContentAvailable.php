<?php

namespace App\Notifications\Notifications;

use App\Models\Content;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContentAvailable extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Content $content)
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
            'title'   => 'New Content Available',
            'message' => "New content '{$this->content->title}' has been added to the class {$this->content->classroom->name}.",
            'icon'    => 'book',
            'link'    => route('student.contents.show', $this->content->id),
            'type'    => 'new_content',
        ];
    }
}
