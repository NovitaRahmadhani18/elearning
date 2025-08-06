<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ __('Profile') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-hero-pattern font-sans antialiased">
        <div class="min-h-screen">
            <!-- Header -->
            <div class="border-b border-neutral-200/50 bg-white/80 backdrop-blur-sm">
                <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between py-6">
                        <div>
                            <h1 class="text-2xl font-semibold text-neutral-800">{{ __('Profile') }}</h1>
                            <p class="mt-1 text-sm text-neutral-600">
                                {{ __('Manage your account information and security settings') }}
                            </p>
                        </div>
                        <a
                            href="{{ route('dashboard') }}"
                            class="flex items-center gap-2 rounded-lg bg-white/60 px-3 py-2 text-sm text-neutral-600 hover:bg-white/80 hover:text-neutral-800"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                />
                            </svg>
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Profile Information -->
                    <div class="lg:col-span-2">
                        <div class="rounded-lg border border-neutral-200/50 bg-white/90 p-6 shadow-lg backdrop-blur-sm">
                            <div class="mb-6 border-b border-neutral-100 pb-4">
                                <h2 class="text-lg font-medium text-neutral-800">{{ __('Profile Information') }}</h2>
                                <p class="mt-1 text-sm text-neutral-600">
                                    {{ __("Update your account's profile information and email address.") }}
                                </p>
                            </div>

                            <!-- Verification Form -->
                            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                                @csrf
                            </form>

                            <!-- Profile Update Form -->
                            <form
                                method="post"
                                action="{{ route('profile.update') }}"
                                class="space-y-6"
                                enctype="multipart/form-data"
                            >
                                @csrf
                                @method('patch')

                                <div class="space-y-4">
                                    <!-- Profile Photo -->
                                    <div class="flex items-center space-x-4 border-b border-neutral-100 pb-4">
                                        <div class="flex-shrink-0">
                                            <img
                                                id="profile-photo-preview"
                                                class="h-16 w-16 rounded-full border-2 border-primary object-cover"
                                                src="{{ $user->profile_photo_url }}"
                                                alt="{{ $user->name }}"
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-neutral-700">
                                                {{ __('Profile Photo') }}
                                            </h4>
                                            <p class="mb-2 text-xs text-neutral-500">
                                                {{ __('Upload a new profile picture (JPG, PNG, max 2MB)') }}
                                            </p>
                                            <div class="flex items-center gap-2">
                                                <label
                                                    for="profile_photo"
                                                    class="inline-flex cursor-pointer items-center rounded-md bg-primary px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1"
                                                >
                                                    <svg
                                                        class="mr-1 h-3 w-3"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                        ></path>
                                                    </svg>
                                                    {{ __('Choose Photo') }}
                                                </label>
                                                <input
                                                    id="profile_photo"
                                                    name="profile_photo"
                                                    type="file"
                                                    accept="image/jpeg,image/png,image/jpg"
                                                    class="hidden"
                                                    onchange="previewProfilePhoto(event)"
                                                />
                                                @if ($user->profile_photo_path)
                                                    <button
                                                        type="button"
                                                        onclick="removeProfilePhoto()"
                                                        class="inline-flex items-center rounded-md bg-red-500 px-2 py-1.5 text-xs font-medium text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                                    >
                                                        <svg
                                                            class="mr-1 h-3 w-3"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                            ></path>
                                                        </svg>
                                                        {{ __('Remove') }}
                                                    </button>
                                                @endif
                                            </div>
                                            @error('profile_photo')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="">
                                        <div>
                                            <label for="name" class="mb-2 block text-sm font-medium text-neutral-700">
                                                {{ __('Name') }}
                                            </label>
                                            <input
                                                id="name"
                                                name="name"
                                                type="text"
                                                class="w-full rounded-md border border-neutral-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"
                                                value="{{ old('name', $user->name) }}"
                                                required
                                                autofocus
                                                autocomplete="given-name"
                                            />
                                            @error('name')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="email" class="mb-2 block text-sm font-medium text-neutral-700">
                                            {{ __('Email') }}
                                        </label>
                                        <input
                                            id="email"
                                            name="email"
                                            type="email"
                                            class="w-full rounded-md border border-neutral-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"
                                            value="{{ old('email', $user->email) }}"
                                            required
                                            autocomplete="username"
                                        />
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror

                                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                            <div class="mt-3 rounded-md bg-secondary-light p-3">
                                                <p class="text-sm text-neutral-800">
                                                    {{ __('Your email address is unverified.') }}
                                                    <button
                                                        form="send-verification"
                                                        class="ml-1 text-sm text-primary-dark underline hover:text-primary"
                                                    >
                                                        {{ __('Click here to re-send the verification email.') }}
                                                    </button>
                                                </p>

                                                @if (session('status') === 'verification-link-sent')
                                                    <p class="mt-2 text-sm text-green-600">
                                                        {{ __('A new verification link has been sent to your email address.') }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label
                                                for="nomor_induk"
                                                class="mb-2 block text-sm font-medium text-neutral-700"
                                            >
                                                {{ __('Student/Teacher ID') }}
                                            </label>
                                            <input
                                                id="nomor_induk"
                                                name="nomor_induk"
                                                type="text"
                                                class="w-full rounded-md border border-neutral-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"
                                                value="{{ old('nomor_induk', $user->nomor_induk) }}"
                                                placeholder="{{ __('Enter your student or teacher ID') }}"
                                            />
                                            @error('nomor_induk')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="jk" class="mb-2 block text-sm font-medium text-neutral-700">
                                                {{ __('Gender') }}
                                            </label>
                                            <select
                                                id="jk"
                                                name="jk"
                                                class="w-full rounded-md border border-neutral-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"
                                            >
                                                <option value="">{{ __('Select Gender') }}</option>
                                                <option value="L" {{ old('jk', $user->jk) == 'L' ? 'selected' : '' }}>
                                                    {{ __('Male') }}
                                                </option>
                                                <option value="P" {{ old('jk', $user->jk) == 'P' ? 'selected' : '' }}>
                                                    {{ __('Female') }}
                                                </option>
                                            </select>
                                            @error('jk')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="address" class="mb-2 block text-sm font-medium text-neutral-700">
                                            {{ __('Address') }}
                                        </label>
                                        <textarea
                                            id="address"
                                            name="address"
                                            rows="3"
                                            class="w-full rounded-md border border-neutral-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"
                                            placeholder="{{ __('Enter your full address') }}"
                                        >
{{ old('address', $user->address) }}</textarea
                                        >
                                        @error('address')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 pt-4">
                                    <button
                                        type="submit"
                                        class="rounded-md bg-primary-dark px-4 py-2 text-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                    >
                                        {{ __('Save Changes') }}
                                    </button>

                                    @if (session('status') === 'profile-updated')
                                        <p class="text-sm text-green-600">{{ __('Saved successfully.') }}</p>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Password Update -->
                    <div class="lg:col-span-1">
                        <div class="rounded-lg border border-neutral-200/50 bg-white/90 p-6 shadow-lg backdrop-blur-sm">
                            <div class="mb-6 border-b border-neutral-100 pb-4">
                                <h3 class="text-lg font-medium text-neutral-800">{{ __('Security') }}</h3>
                                <p class="mt-1 text-sm text-neutral-600">
                                    {{ __('Update your password to keep your account secure.') }}
                                </p>
                            </div>

                            <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                                @csrf
                                @method('put')

                                <div>
                                    <label
                                        for="update_password_current_password"
                                        class="mb-2 block text-sm font-medium text-neutral-700"
                                    >
                                        {{ __('Current Password') }}
                                    </label>
                                    <input
                                        id="update_password_current_password"
                                        name="current_password"
                                        type="password"
                                        class="w-full rounded-md border border-neutral-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"
                                        autocomplete="current-password"
                                    />
                                    @error('current_password', 'updatePassword')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        for="update_password_password"
                                        class="mb-2 block text-sm font-medium text-neutral-700"
                                    >
                                        {{ __('New Password') }}
                                    </label>
                                    <input
                                        id="update_password_password"
                                        name="password"
                                        type="password"
                                        class="w-full rounded-md border border-neutral-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"
                                        autocomplete="new-password"
                                    />
                                    @error('password', 'updatePassword')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        for="update_password_password_confirmation"
                                        class="mb-2 block text-sm font-medium text-neutral-700"
                                    >
                                        {{ __('Confirm Password') }}
                                    </label>
                                    <input
                                        id="update_password_password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        class="w-full rounded-md border border-neutral-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"
                                        autocomplete="new-password"
                                    />
                                    @error('password_confirmation', 'updatePassword')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="pt-4">
                                    <button
                                        type="submit"
                                        class="w-full rounded-md bg-neutral-800 px-4 py-2 text-white hover:bg-neutral-900 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2"
                                    >
                                        {{ __('Update Password') }}
                                    </button>

                                    @if (session('status') === 'password-updated')
                                        <p class="mt-2 text-center text-sm text-green-600">
                                            {{ __('Password updated successfully.') }}
                                        </p>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script>
        function previewProfilePhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('profile-photo-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function removeProfilePhoto() {
            // Reset the file input
            document.getElementById('profile_photo').value = '';

            // Add hidden input to indicate photo removal
            let removeInput = document.getElementById('remove_profile_photo');
            if (!removeInput) {
                removeInput = document.createElement('input');
                removeInput.type = 'hidden';
                removeInput.name = 'remove_profile_photo';
                removeInput.id = 'remove_profile_photo';
                removeInput.value = '1';
                document.querySelector('form[action="{{ route('profile.update') }}"]').appendChild(removeInput);
            } else {
                removeInput.value = '1';
            }

            // Reset preview to default
            document.getElementById('profile-photo-preview').src =
                'https://ui-avatars.com/api/?name=' +
                encodeURIComponent('{{ $user->name }}') +
                '&color=7F9CF5&background=EBF4FF';
        }
    </script>
</html>
