<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Content extends Model
{
    use LogsActivity;

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

    /**
     * Configure activity logging options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'contentable_type', 'contentable_id', 'classroom_id'])
            ->logOnlyDirty()
            ->useLogName('material_completion')
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Content created',
                'updated' => 'Content updated',
                'deleted' => 'Content deleted',
                default => "Content {$eventName}"
            })
            ->dontSubmitEmptyLogs();
    }
}
