<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomStudent extends Model
{

    public $fillable = [
        'classroom_id',
        'student_id',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function getProgressAttribute()
    {
        $totalContents = $this->classroom->contents()->count();
        if ($totalContents === 0) {
            return 0;
        }

        $completedContents = $this->student->contents()
            ->where('classroom_id', $this->classroom_id)
            ->wherePivot('status', "completed")
            ->count();

        return ($completedContents / $totalContents) * 100;
    }
}
