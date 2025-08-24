<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'id_number',
        'address',
        'gender',
        'avatar',
        'is_active',
        'total_points',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'role' => RoleEnum::class,
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function teachedClassrooms()
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_students', 'student_id', 'classroom_id')
            ->withTimestamps();
    }

    public function contents()
    {
        return $this->belongsToMany(Content::class, 'content_student', 'user_id', 'content_id')
            ->withPivot('status', 'completed_at', 'score')
            ->withTimestamps();
    }

    public function classroomStudents()
    {
        return $this->hasMany(ClassroomStudent::class, 'student_id');
    }

    public function studentPoints(): HasMany
    {
        return $this->hasMany(StudentPoint::class);
    }

    public function quizSubmissions(): HasMany
    {
        return $this->hasMany(QuizSubmission::class, 'student_id');
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')->withTimestamps();
    }





    public function hasRole(RoleEnum $role): bool
    {
        return $this->role === $role;
    }

    /**
     * @param array<string> $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        if (!$this->role) {
            return false;
        }
        return in_array($this->role->value, $roles);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleEnum::ADMIN);
    }
}
