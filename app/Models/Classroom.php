<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    protected $fillable = [
        'name',
        'description',
        'teacher_id',
        'code',
        'category_id',
        'status_id',
        'thumbnail',
        'invite_code',
    ];

    protected $with = [
        'teacher',
        'category',
        'status',
    ];


    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function category()
    {
        return $this->belongsTo(ClassroomCategory::class, 'category_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->category->name . ')';
    }

    public function students()
    {
        return $this->hasMany(ClassroomStudent::class, 'classroom_id');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }
}

