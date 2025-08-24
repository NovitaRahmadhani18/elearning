<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = [
        'question_id',
        'answer_text',
        'image_path',
        'is_correct',
    ];

    protected $hidden = [
        'is_correct',
    ];


    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}

