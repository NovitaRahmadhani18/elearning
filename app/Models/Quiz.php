<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime',
        'due_time' => 'datetime',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
