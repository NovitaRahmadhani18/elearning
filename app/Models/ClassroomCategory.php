<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomCategory extends Model
{
    protected $table = 'classrooms_categories';

    protected $fillable = [
        'name',
        'value',
    ];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'category_id');
    }
}
