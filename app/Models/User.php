<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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

    public function profileable(): MorphTo
    {
        return $this->morphTo();
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


    public function addProfile(string $role): void
    {
        // check if user already has a profile and change role
        if ($this->profile && $this->role !== $role) {
            // if the user already has a profile, we can update it based on the new role
            $this->profile->delete();
        }

        // create user profile based on role
        switch ($role) {
            case 'user':
                $profile = new StudentProfile(['user_id' => $this->id]);
                $profile->save();
                break;
            case 'teacher':
                $profile = new TeacherProfile(['user_id' => $this->id]);
                $profile->save();
                break;
            case 'admin':
                // Admins do not have a profile, so we can skip this
                return;
            default:
                throw new \InvalidArgumentException("Role {$role} is not supported.");
        }

        // associate the profile with the user
        $this->profileable()->associate($profile);
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
}
