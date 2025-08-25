<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{

    protected $table = 'activity_log';

    protected $fillable = [
        'user_id',
        'activity_type',
        'subject_id',
        'subject_type',
        'description',
        'created_at',
        'updated_at',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
