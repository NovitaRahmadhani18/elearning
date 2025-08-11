<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;


#[ObservedBy(\App\Observers\ClassroomActivityObserver::class)]
class ClassroomStudent extends Model
{
    protected $table = 'classroom_user';
    protected $guarded = [];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
