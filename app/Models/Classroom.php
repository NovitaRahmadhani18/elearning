<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;

class Classroom extends Model
{
    use HasFactory;

    protected $guarded = [];

    // when creating a classroom, then make unique invite code and secret code
    protected static function booted()
    {
        static::creating(function (Classroom $classroom) {
            $classroom->invite_code = strtoupper(uniqid());
            // random 8 numeric characters
            $classroom->secret_code = strtoupper(substr(md5(uniqid()), 0, 8));
        });
    }

    public function contents()
    {
        return $this->hasMany(Content::class, 'classroom_id');
    }

    public function quizzes()
    {
        return $this->hasManyThrough(Quiz::class, Content::class, 'classroom_id', 'id', 'id', 'contentable_id')
            ->where('contentable_type', Quiz::class);
    }

    public function materials()
    {
        return $this->hasManyThrough(Material::class, Content::class, 'classroom_id', 'id', 'id', 'contentable_id')
            ->where('contentable_type', Material::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->thumbnail_path) {
            return Storage::disk('public')->url($this->thumbnail_path);
        }

        return '';
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'classroom_user', 'classroom_id', 'user_id');
    }
}
