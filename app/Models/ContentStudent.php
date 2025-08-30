<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentStudent extends Model
{
    use HasFactory;
    const STATUS_COMPLETED = 'completed';

    protected $table = 'content_student';

    protected $fillable = [
        'content_id',
        'user_id',
        'status',
        'score',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];


    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
