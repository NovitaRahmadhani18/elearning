<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime',
        'due_time' => 'datetime',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(QuizSubmission::class);
    }

    public function contents(): MorphMany
    {
        return $this->morphMany(Content::class, 'contentable');
    }

    public function classroom()
    {
        return $this->hasOneThrough(Classroom::class, Content::class, 'contentable_id', 'id', 'id', 'classroom_id')
            ->where('contentable_type', self::class);
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? $this->start_time->format('l, d F Y, H:i') : null;
    }

    public function getFormattedDueTimeAttribute()
    {
        return $this->due_time ? $this->due_time->format('l, d F Y, H:i') : null;
    }

    public function getTimeLimitInMinutesAttribute()
    {
        return $this->time_limit > 0 ? floor($this->time_limit / 60) . " minutes" : 'No time limit';
    }

    public function getTimeLimitInSecondsAttribute()
    {
        return $this->time_limit;
    }

    public function hasUserSubmitted($userId): bool
    {
        return $this->submissions()->where('user_id', $userId)->exists();
    }

    public function getUserSubmission($userId): ?QuizSubmission
    {
        return $this->submissions()->where('user_id', $userId)->first();
    }
}
