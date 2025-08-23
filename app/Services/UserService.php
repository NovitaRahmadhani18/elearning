<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Facades\DataTable;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function index()
    {
        $query = User::query();

        $result = DataTable::query($query)
            ->searchable(['name', 'email'])
            ->allowedFilters([
                'email_verified_at:NOT NULL',
                'email_verified_at:NULL',
            ])
            ->allowedSorts(['name', 'email', 'created_at'])
            ->make();

        return UserResource::collection($result);
    }

    public function countByRole(string $role): int
    {
        return User::where('role', $role)->count();
    }

    public function getRoles()
    {
        return RoleEnum::cases();
    }

    public function store(array $data): User
    {
        DB::beginTransaction();
        $avatarPath = null;
        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $avatarPath = $data['avatar']->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'id_number' => $data['id_number'],
            'address' => $data['address'],
            'gender' => $data['gender'],
            'is_active' => $data['is_active'] ?? true,
            'password' => bcrypt($data['password']),
            'avatar' => $avatarPath,
        ]);
        DB::commit();

        return $user;
    }

    public function updateUser(User $user, array $validatedData): User
    {
        return DB::transaction(function () use ($user, $validatedData) {

            // Handle avatar update
            if (isset($validatedData['avatar']) && $validatedData['avatar'] instanceof UploadedFile) {
                // Hapus avatar lama jika ada
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                // Simpan avatar baru dan perbarui path
                $validatedData['avatar'] = $validatedData['avatar']->store('avatars', 'public');

                $user->avatar = $validatedData['avatar'];
            }

            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);

                $user->password = $validatedData['password'];
            }

            $user->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role' => $validatedData['role'],
                'id_number' => $validatedData['id_number'],
                'address' => $validatedData['address'],
            ]);

            $user->save();

            return $user;
        });
    }

    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // Hapus avatar jika ada
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            return $user->delete();
        });
    }
}
