<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Content extends Model
{
    use HasFactory;
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

    public function students()
    {
        return $this->belongsToMany(User::class, 'content_student', 'content_id', 'user_id')
            ->withPivot('status', 'score', 'completed_at')
            ->withTimestamps();
    }

    public function contentStudents()
    {
        return $this->hasMany(ContentStudent::class, 'content_id');
    }
}
