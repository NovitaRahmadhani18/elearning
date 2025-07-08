<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentUser extends Model
{
    protected $table = 'content_users';
    protected $guarded = [];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }
}
