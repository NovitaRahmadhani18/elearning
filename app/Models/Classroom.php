<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;

class Classroom extends Model
{

    protected $guarded = [];

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }


    public function getImageUrlAttribute()
    {
        if ($this->thumbnail_path) {
            return Storage::disk('public')->url($this->thumbnail_path);
        }

        return '';
    }
}
