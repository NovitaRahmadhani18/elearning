<x-layouts.admin-layout>
    <x-slot name="header">User Management &gt; Edit User</x-slot>

    <div class="flex min-h-[70vh] items-center justify-center">
        <form
            class="w-full max-w-2xl rounded-lg border border-primary/20 bg-white p-10"
            method="POST"
            enctype="multipart/form-data"
            action="{{ route('admin.users.update', $user->id) }}"
        >
            @csrf
            @method('PUT')
            <div class="mb-8 flex flex-col items-center">
                <x-upload-avatar-toggle :avatar-url="$user->avatar_url ?? null" />
            </div>
            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="first_name" value="First Name" />
                    <x-text-input
                        id="first_name"
                        name="name"
                        type="text"
                        class="mt-1"
                        required
                        placeholder="Enter first name"
                        :value="old('name', $user->name)"
                    />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="last_name" value="Last Name" />
                    <x-text-input
                        id="last_name"
                        name="last_name"
                        type="text"
                        class="mt-1"
                        required
                        placeholder="Enter last name"
                        :value="old('last_name', $user->last_name)"
                    />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>
            </div>
            <div class="mb-6">
                <x-input-label for="email" value="Email Address" />
                <x-text-input
                    id="email"
                    name="email"
                    type="email"
                    class="mt-1"
                    required
                    placeholder="Enter email address"
                    :value="old('email', $user->email)"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div class="mb-6">
                <x-input-label value="Role" />
                <div class="mt-2 flex flex-col space-y-2">
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            name="role"
                            value="admin"
                            class="form-radio text-primary focus:ring-primary"
                            required
                            @checked(old('role', $user->firstRole) === 'admin')
                        />
                        <span class="ml-2">Administrator</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            name="role"
                            value="teacher"
                            class="form-radio text-primary focus:ring-primary"
                            @checked(old('role', $user->firstRole) === 'teacher')
                        />
                        <span class="ml-2">Teacher</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            name="role"
                            value="user"
                            class="form-radio text-primary focus:ring-primary"
                            @checked(old('role', $user->firstRole) === 'user')
                        />
                        <span class="ml-2">Student</span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>
            <div class="mb-6">
                <x-upload-avatar-toggle type="toggle" :checked="old('status', $user->is_active)" />
            </div>
            <div class="mb-6">
                <x-input-label for="password" value="Password" />
                <x-password-input id="password" name="password" placeholder="Enter new password (optional)" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="mb-8">
                <x-input-label for="password_confirmation" value="Confirm Password" />
                <x-password-input
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Confirm new password"
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
            <div class="flex justify-end">
                <div class="flex flex-row justify-end gap-2">
                    <x-secondary-button type="button" onclick="window.history.back()">Cancel</x-secondary-button>
                    <x-primary-button type="submit" class="w-fit">Update User</x-primary-button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin-layout>
