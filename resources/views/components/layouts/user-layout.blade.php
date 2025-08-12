<x-layouts>
    <header class="bg-primary-light shadow-sm" x-data="{
        mobileMenuOpen: false,
        init() {
            this.$watch('mobileMenuOpen', value => {
                if (value) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
        }
    }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <!-- Logo -->
                        <div class="flex items-center">
                            <img src="{{ asset('images/achievements/Logo SDB.png') }}" alt="Logo"
                                class="h-10 w-10 md:h-12 md:w-12" />
                        </div>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:ml-6 md:flex md:space-x-4">
                        @foreach ($menu['userMenu'] as $item)
                            <a href="{{ route($item->url) }}" @class([
                                'space-x-1 rounded-md px-3 py-2 text-sm font-medium text-gray-500 hover:bg-primary-light hover:text-gray-900',
                                'text-primary-dark' => in_array(
                                    request()->route()->getName(),
                                    $item->routes ?? []),
                            ])>
                                <x-icon :name="$item->icon" class="ml-1 inline h-5 w-5" />
                                <span>
                                    {{ $item->label }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <x-mobile-menu-button @click="mobileMenuOpen = !mobileMenuOpen" x-bind:is-open="mobileMenuOpen"
                        class="md:hidden" />

                    <!-- Desktop Actions -->
                    <div class="hidden md:flex md:items-center md:space-x-4">
                        <!-- Notification Dropdown -->
                        <livewire:notification-dropdown />

                        <div
                            class="flex items-center rounded-lg bg-gradient-to-r from-secondary/10 to-secondary/20 px-3 py-2 shadow-sm">
                            <x-gmdi-stars class="mr-2 h-5 w-5 text-secondary" />
                            <span class="text-sm font-bold text-secondary-dark">
                                {{ number_format(auth()->user()->getPoints()) }} Point
                            </span>
                        </div>

                        <x-layouts.components.settings-dropdown />
                    </div>

                    <!-- Mobile Points Display -->
                    <div class="mr-2 md:hidden">
                        <div
                            class="flex items-center rounded-lg bg-gradient-to-r from-secondary/10 to-secondary/20 px-2 py-1 text-xs shadow-sm">
                            <x-gmdi-stars class="mr-1 h-4 w-4 text-secondary" />
                            <span class="font-bold text-secondary-dark">
                                {{ number_format(auth()->user()->getPoints()) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" class="border-t border-gray-200 pb-3 pt-4 md:hidden">
                <!-- Mobile Navigation Links -->
                <div class="space-y-1 px-2">
                    @foreach ($menu['userMenu'] as $item)
                        <a href="{{ route($item->url) }}" @click="mobileMenuOpen = false" @class([
                            'flex items-center space-x-2 rounded-md px-3 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900',
                            'bg-gray-100 text-primary-dark' => in_array(
                                request()->route()->getName(),
                                $item->routes ?? []),
                        ])>
                            <x-icon :name="$item->icon" class="h-5 w-5" />
                            <span>{{ $item->label }}</span>
                        </a>
                    @endforeach
                </div>

                <!-- Mobile User Section -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex items-center px-5">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-sm text-gray-500">{{ auth()->user()->firstRole ?? 'Student' }}</div>
                        </div>
                        <div class="ml-auto">
                            <button class="text-gray-400 hover:text-gray-500">
                                <x-gmdi-notifications-o class="h-6 w-6" />
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1 px-2">
                        <a href="{{ route('profile.show') }}" @click="mobileMenuOpen = false"
                            class="block rounded-md px-3 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900">
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" @click="mobileMenuOpen = false"
                                class="block w-full rounded-md px-3 py-2 text-left text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <!-- Achievement Notification -->
        <x-achievement-notification />

        <h1 class="mb-4 text-2xl font-semibold text-gray-900">
            {{ $header ?? '' }}
        </h1>
        @if (session('success'))
            <div class="mb-4 rounded bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded bg-red-100 p-4 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        {{ $slot }}
    </main>
</x-layouts>
