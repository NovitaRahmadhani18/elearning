<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        // Add any attributes that you want to be mass assignable
        'user_id',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->morphOne(User::class, 'profileable');
    }
}
