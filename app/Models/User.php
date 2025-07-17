<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use LevelUp\Experience\Concerns\GiveExperience;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, GiveExperience;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'last_name',
        'photo_profile_path',
        'is_active',
        'nomor_induk',
        'address',
        'jk',
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
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            // Jika ada path di database, kembalikan URL dari disk 'public'
            return Storage::disk('public')->url($this->profile_photo_path);
        }

        // Jika tidak ada, kembalikan URL default yang digenerate dari nama pengguna
        return $this->defaultProfilePhotoUrl();
    }

    /**
     * Membuat URL foto profil default.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
    }

    protected function getFirstRoleAttribute(): string
    {
        return $this->roles->first()?->name ?? 'user';
    }

    protected function getFullNameAttribute(): string
    {
        return trim("{$this->name} {$this->last_name}");
    }

    public function addMedia($file)
    {
        // handle uploading media files as profile photos to proper storage without libraries with unique name file name

        // check if user has a profile photo
        if ($this->profile_photo_path) {
            // delete the old profile photo
            Storage::disk('public')->delete($this->profile_photo_path);
        }

        $path = $file->store('profile-photos', 'public');

        // update the profile photo path
        $this->profile_photo_path = $path;
        $this->save();

        return $this;
    }

    public function getLastLoginAttribute(): ?string
    {
        // Get the last login time from the activity log
        $lastLogin = Activity::query()
            ->with('causer')
            ->where('causer_id', $this->id)
            ->where('event', 'login')
            ->latest()
            ->first();

        if ($lastLogin) {
            return $lastLogin->created_at;
        }

        return null;
    }

    public function classrooms()
    {
        if ($this->hasRole('teacher') || $this->hasRole('admin')) {
            return $this->hasMany(Classroom::class, 'teacher_id');
        }

        return $this->belongsToMany(Classroom::class, 'classroom_user')
            ->withPivot('progress')
            ->withTimestamps()
            ->orderBy('created_at', 'desc');
    }

    public function classroomStudents()
    {
        if (!$this->role('user')) {
            return collect();
        }

        // Get the classrooms where the user is a studenta
        return $this->belongsToMany(Classroom::class, 'classroom_user')
            ->withPivot('progress')
            ->withTimestamps()
            ->orderBy('created_at', 'desc');
    }

    public function completedContents()
    {
        return $this->belongsToMany(Content::class, 'content_users')
            ->withTimestamps();
    }

    public function quizSubmissions()
    {
        return $this->hasMany(QuizSubmission::class);
    }

    public function quizAnswers()
    {
        return $this->hasManyThrough(QuizAnswer::class, QuizSubmission::class);
    }

    public function getQuizSubmissionsCountAttribute(): int
    {
        return $this->quizSubmissions()->count();
    }

    public function getClassroomProgress($classroomId)
    {
        // Get total contents in the classroom
        $totalContents = \App\Models\Content::where('classroom_id', $classroomId)->count();

        if ($totalContents === 0) {
            return 0;
        }

        // Get completed contents for this classroom
        $completedContents = $this->completedContents()
            ->where('classroom_id', $classroomId)
            ->count();

        // Calculate progress percentage
        return round(($completedContents / $totalContents) * 100, 2);
    }

    public static function updateClassroomProgress($userId, $classroomId)
    {
        $user = self::find($userId);
        if (!$user) return;

        $newProgress = $user->getClassroomProgress($classroomId);
        $user->classrooms()->updateExistingPivot($classroomId, [
            'progress' => $newProgress
        ]);

        return $newProgress;
    }
}
