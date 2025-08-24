<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'statuses';

    protected $fillable = [
        'name',
        'value',
    ];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'status_id');
    }
}
