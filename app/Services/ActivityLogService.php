<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    /**
     * Log a user activity.
     *
     * @param  User  $user
     * @param  string  $activityType
     * @param  Model|null  $subject
     * @param  string  $description
     * @return void
     */
    public function log(User $user, string $activityType, ?Model $subject, string $description): void
    {
        $log = new ActivityLog([
            'activity_type' => $activityType,
            'description' => $description,
        ]);

        $log->user_id = $user->id;

        if ($subject) {
            $log->subject()->associate($subject);
        }

        $log->save();
    }
}