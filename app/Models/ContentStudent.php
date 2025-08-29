<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentStudent extends Model
{
    protected $table = 'content_student';

    protected $fillable = [
        'content_id',
        'user_id',
        'status',
        'score',
        'completed_at',
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
