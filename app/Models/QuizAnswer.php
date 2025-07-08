<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    protected $fillable = [
        'quiz_submission_id',
        'question_id',
        'selected_option_id',
        'is_correct',
        'time_spent'
    ];

    protected $casts = [
        'is_correct' => 'boolean'
    ];

    public function quizSubmission(): BelongsTo
    {
        return $this->belongsTo(QuizSubmission::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    public function getTimeSpentFormattedAttribute(): string
    {
        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
} 