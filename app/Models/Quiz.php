<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Quiz extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'duration_minutes',
    ];

    protected $with = [
        'questions'
    ];

    public function content(): MorphOne
    {
        return $this->morphOne(Content::class, 'contentable');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}

