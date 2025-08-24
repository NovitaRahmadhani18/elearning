<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassroomCategory extends Model
{
    use HasFactory;
    public $timestamps = false;
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
