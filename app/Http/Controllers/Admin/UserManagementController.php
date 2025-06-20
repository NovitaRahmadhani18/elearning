<?php

namespace App\Http\Controllers\Admin;

use App\CustomClasses\Column;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // get count user each role
        $countUserByRole = User::with('roles')
            ->get()
            ->groupBy(function ($user) {
                return $user->roles->first()->name ?? 'No Role';
            })
            ->map(function ($users, $role) {
                return [
                    'role' => $role,
                    'count' => $users->count(),
                ];
            });


        // query for user management table
        $query = User::with('roles')
            ->latest();

        $query->when(request('search'), function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        });

        $tableData = \App\CustomClasses\TableData::make(
            $query,
            [
                Column::make('', 'User')->setView('reusable-table.column.user-card'),
                Column::make('', 'role')->setView('reusable-table.column.user-role'),
                Column::make('is_active', 'status')->setView('reusable-table.column.user-status'),
                Column::make('created_at', 'join date')->setView('reusable-table.column.date-format'),
                Column::make('lastLogin', 'last login')->setView('reusable-table.column.date-dif-for-human'),
                Column::make('id', 'actions')->setView('reusable-table.column.actions.admin-users-action'),
            ],
            perPage: request('perPage', 10),
            id: 'user-management-table',
        );

        return view('pages.admin.users.index', compact(
            'tableData',
            'countUserByRole'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'avatar' => 'nullable|image|max:2048',
            'last_name' => 'nullable|string|max:255',
        ]);

        // create the user with transaction
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'is_active' => $request->status ?? false,
                'last_name' => $request->last_name,
            ]);

            if ($request->hasFile('avatar')) {
                $user->addMedia($request->file('avatar'));
            }

            if ($request->role) {
                $user->assignRole($request->role);
                // create user profile if role is not 'admin'
                $user->addProfile($request->role);
            }

            $user->save();
        });

        // redirect to the user management page with success message
        return to_route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('pages.admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'avatar' => 'nullable|image|max:2048',
            'last_name' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->last_name = $request->last_name;
            $user->is_active = $request->status ? true : false;

            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            $user->save();

            if ($request->hasFile('avatar')) {
                $user->addMedia($request->file('avatar'));
            }

            if ($request->role) {
                $user->syncRoles([$request->role]);

                $user->addProfile($request->role);
            }
        });

        return to_route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // check if the user is the last admin
        if ($user->hasRole('admin') && User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->count() <= 1) {
            return to_route('admin.users.index')
                ->with('error', 'You cannot delete the last admin user.');
        }

        // chck if the user is the authenticated user
        if ($user->id === auth()->id()) {
            return to_route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }


        DB::transaction(function () use ($user) {
            $user->delete();
        });

        return to_route('admin.users.index')
            ->with('success', 'User deleted successfully.')
            ->with('event', 'user:deleted');
    }
}
