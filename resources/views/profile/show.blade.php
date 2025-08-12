<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ __('Profile Preview') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-hero-pattern font-sans antialiased">
        <div class="min-h-screen">
            <!-- Header -->
            <div class="border-b border-neutral-200/50 bg-white/80 backdrop-blur-sm">
                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between py-6">
                        <div>
                            <h1 class="text-2xl font-semibold text-neutral-800">{{ __('Profile') }}</h1>
                            <p class="mt-1 text-sm text-neutral-600">
                                {{ __('Your account information overview') }}
                            </p>
                        </div>
                        <a
                            href="{{ route('dashboard') }}"
                            class="flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm text-neutral-600 hover:bg-neutral-50 hover:text-neutral-800"
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
            <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                <!-- Identity Card -->
                <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-start">
                        <div class="flex-shrink-0">
                            <img
                                class="h-24 w-24 rounded-full border border-neutral-200 object-cover"
                                src="{{ $user->profile_photo_url }}"
                                alt="{{ $user->name }}"
                            />
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h2 class="text-xl font-semibold text-neutral-900">{{ $user->name }}</h2>
                                    <div class="mt-0.5 flex flex-wrap items-center gap-2 text-sm text-neutral-600">
                                        <span>{{ $user->email }}</span>
                                        <span class="hidden text-neutral-400 sm:inline">•</span>
                                        @if (method_exists($user, 'getRoleNames'))
                                            <span class="text-neutral-500">
                                                {{ $user->getRoleNames()->join(', ') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <!-- Actions -->
                                    <div class="">
                                        <a
                                            href="{{ route('profile.edit') }}"
                                            class="inline-flex items-center rounded-md border border-neutral-300 bg-primary-dark px-4 py-2 text-sm font-medium text-white shadow-sm"
                                        >
                                            {{ __('Edit Profile') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Stats (role-agnostic) -->
                            <div class="mt-6">
                                <h3 class="mb-3 text-sm font-medium text-neutral-700">{{ __('Quick Stats') }}</h3>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                    <div class="rounded-md border border-neutral-200 bg-white p-3">
                                        <p class="text-[11px] uppercase tracking-wide text-neutral-500">
                                            {{ __('Classrooms') }}
                                        </p>
                                        <p class="mt-1 text-lg font-semibold text-neutral-900">
                                            {{ number_format($stats['classrooms'] ?? 0) }}
                                        </p>
                                    </div>
                                    <div class="rounded-md border border-neutral-200 bg-white p-3">
                                        <p class="text-[11px] uppercase tracking-wide text-neutral-500">
                                            {{ __('Last Login') }}
                                        </p>
                                        <p class="mt-1 text-sm text-neutral-900">
                                            @php
                                                $lastLogin = $stats['last_login'] ?? null;
                                            @endphp

                                            {{ $lastLogin ? \Illuminate\Support\Carbon::parse($lastLogin)->diffForHumans() : '—' }}
                                        </p>
                                    </div>
                                    <div class="rounded-md border border-neutral-200 bg-white p-3">
                                        <p class="text-[11px] uppercase tracking-wide text-neutral-500">
                                            {{ __('Member Since') }}
                                        </p>
                                        <p class="mt-1 text-sm text-neutral-900">
                                            {{ optional($user->created_at)->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div class="rounded-md border border-neutral-200 bg-white p-3">
                                        <p class="text-[11px] uppercase tracking-wide text-neutral-500">
                                            {{ __('Last Updated') }}
                                        </p>
                                        <p class="mt-1 text-sm text-neutral-900">
                                            {{ optional($user->updated_at)->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Personal Info -->
                    <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm lg:col-span-1">
                        <h3 class="mb-4 text-sm font-medium text-neutral-700">{{ __('Personal Info') }}</h3>
                        <dl class="divide-y divide-neutral-200/70">
                            <div class="grid grid-cols-3 gap-2 py-3 text-sm">
                                <dt class="col-span-1 text-neutral-500">{{ __('ID Number') }}</dt>
                                <dd class="col-span-2 text-neutral-900">{{ $user->nomor_induk ?? '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2 py-3 text-sm">
                                <dt class="col-span-1 text-neutral-500">{{ __('Gender') }}</dt>
                                <dd class="col-span-2 text-neutral-900">{{ $user->jk ?? '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2 py-3 text-sm">
                                <dt class="col-span-1 text-neutral-500">{{ __('Address') }}</dt>
                                <dd class="col-span-2 text-neutral-900">{{ $user->address ?? '—' }}</dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2 py-3 text-sm">
                                <dt class="col-span-1 text-neutral-500">{{ __('Member Since') }}</dt>
                                <dd class="col-span-2 text-neutral-900">
                                    {{ optional($user->created_at)->format('M d, Y') }}
                                </dd>
                            </div>
                            <div class="grid grid-cols-3 gap-2 py-3 text-sm">
                                <dt class="col-span-1 text-neutral-500">{{ __('Last Updated') }}</dt>
                                <dd class="col-span-2 text-neutral-900">
                                    {{ optional($user->updated_at)->format('M d, Y') }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Recent Activity (role-agnostic) -->
                    <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm lg:col-span-2">
                        <h3 class="mb-4 text-sm font-medium text-neutral-700">{{ __('Recent Activity') }}</h3>
                        @if (($recentActivities ?? collect())->isNotEmpty())
                            <ul class="divide-y divide-neutral-200/70">
                                @foreach ($recentActivities as $act)
                                    <li class="flex items-center justify-between py-3">
                                        <p class="text-sm text-neutral-800">
                                            {{ $act->description ?? __('Activity') }}
                                        </p>
                                        <p class="text-xs text-neutral-500">
                                            {{ optional($act->created_at)->diffForHumans() }}
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-neutral-500">{{ __('No recent activity.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
