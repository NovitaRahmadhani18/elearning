<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'contentable';
    protected $guarded = [];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function contentable()
    {
        return $this->morphTo();
    }

    public function completedByUser()
    {
        return $this->belongsToMany(User::class, 'content_users', 'content_id', 'user_id')
            ->withPivot('completion_time', 'points_earned', 'score', 'created_at', 'updated_at')
            ->withTimestamps();
    }

    public function isCompletedByUser()
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        return $this->completedByUser()->where('user_id', $user->id)->exists();
    }
}
