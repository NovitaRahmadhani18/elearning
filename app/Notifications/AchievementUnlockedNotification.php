<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use LevelUp\Experience\Models\Achievement;

class AchievementUnlockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $achievement;

    public function __construct(Achievement $achievement)
    {
        $this->achievement = $achievement;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'achievement_unlocked',
            'title' => 'Achievement Unlocked!',
            'message' => "Congratulations! You've unlocked the '{$this->achievement->name}' achievement.",
            'achievement_id' => $this->achievement->id,
            'achievement_name' => $this->achievement->name,
            'achievement_description' => $this->achievement->description,
            'points_awarded' => $this->achievement->points,
        ];
    }
}
