<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UsersController extends Controller
{
    public function __construct(protected UserService $userService) {}



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('admin/users/index', [
            'users' => $this->userService->index(),
            'count' => [
                'admin' => $this->userService->countByRole('admin'),
                'teacher' => $this->userService->countByRole('teacher'),
                'student' => $this->userService->countByRole('student'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('admin/users/create', [
            'roles' => $this->userService->getRoles(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,teacher,student', // Adjust roles as needed
            'id_number' => 'required|numeric|unique:users,id_number',
            'gender' => 'required|in:male,female',
            'avatar' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = $this->userService->store($data);
            if (!$user) {
                return redirect()->back()->withErrors(['error' => 'Failed to create user.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return inertia('admin/users/edit', [
            'user' => UserResource::make($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $userId = $user->id;
        $data = $request->validate(
            [
                'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                // Gunakan Rule::unique untuk mengabaikan user saat ini saat pengecekan
                'id_number' => ['required', 'numeric', Rule::unique('users')->ignore($userId)],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
                'gender' => ['required', 'in:male,female'],
                'role' => ['required', 'string', 'in:admin,teacher,student'],
                'is_active' => ['required', 'boolean'],
                // Password bersifat 'nullable', artinya tidak wajib diisi
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            ]
        );

        try {
            $user = $this->userService->updateUser($user, $data);
            if (!$user) {
                return to_route('admin.users.index')->withErrors(['error' => 'Failed to update user.']);
            }
        } catch (\Exception $e) {
            return to_route('admin.users.index')->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }

        return to_route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $this->userService->deleteUser($user);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete user: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
