<?php

namespace App\Events;

use App\Models\Content;
use App\Models\QuizSubmission;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContentCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user,
        public Content $content,
        public ?array $data = null,
        public ?QuizSubmission $submission = null
    ) {}
}

