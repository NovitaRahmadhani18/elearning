<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Content extends Model
{
    protected $fillable = [
        'classroom_id',
        'title',
        'description',
        'points',
        'order',
        'contentable_id',
        'contentable_type',
    ];

    protected $with = [
        'classroom',
        'contentable',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function contentable(): MorphTo
    {
        return $this->morphTo();
    }
}

