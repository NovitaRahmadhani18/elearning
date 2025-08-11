<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

#[ObservedBy(\App\Observers\QuizSubmissionObserver::class)]
class QuizSubmission extends Model
{
    use LogsActivity;
    protected $fillable = [
        'quiz_id',
        'user_id',
        'started_at',
        'completed_at',
        'score',
        'total_questions',
        'correct_answers',
        'time_spent',
        'is_completed',
        'answers'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'answers' => 'array',
        'is_completed' => 'boolean'
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quizAnswers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public function getScorePercentageAttribute(): float
    {
        if ($this->total_questions == 0) {
            return 0;
        }
        return round(($this->correct_answers / $this->total_questions) * 100, 2);
    }

    public function getTimeSpentFormattedAttribute(): string
    {
        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_completed) {
            return 'Completed';
        }
        return 'In Progress';
    }

    /**
     * Configure activity logging options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['score', 'total_questions', 'correct_answers', 'time_spent', 'is_completed'])
            ->logOnlyDirty()
            ->useLogName('quiz_completion')
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Quiz submission started',
                'updated' => $this->is_completed ? 'Quiz completed' : 'Quiz submission updated',
                'deleted' => 'Quiz submission deleted',
                default => "Quiz submission {$eventName}"
            })
            ->dontSubmitEmptyLogs();
    }
}
