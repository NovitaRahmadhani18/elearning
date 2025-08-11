<x-layouts>
    <div class="flex h-screen" x-data="{
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
        <!-- Mobile Backdrop -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="mobileMenuOpen = false"
            class="fixed inset-0 z-20 bg-gray-600 bg-opacity-75 md:hidden"></div>

        <!-- Mobile Sidebar -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-30 w-72 min-h-screen border-r border-r-primary-dark bg-primary-light md:hidden">
            <div class="flex h-16 items-center justify-between space-x-2 border-b border-b-primary-dark p-2">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/achievements/Logo SDB.png') }}" alt="Logo" class="h-12 w-12" />
                    <div class="flex flex-col">
                        <h1 class="font-semibold">E-Learning</h1>
                        <h2 class="text-sm">Sekolah Darma Bangsa</h2>
                    </div>
                </div>
                <button @click="mobileMenuOpen = false"
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    <x-gmdi-close class="h-6 w-6" />
                </button>
            </div>
            <div class="mt-4 space-y-1 px-4">
                @foreach ($menu['teacherMenu'] as $item)
                    <x-sidebar-link href="{{ route($item->url) }}" :active="in_array(request()->route()->getName(), $item->routes ?? [])" :icon="svg($item->icon, 'h-5 w-5')"
                        @click="mobileMenuOpen = false">
                        {{ $item->label }}
                    </x-sidebar-link>
                @endforeach
            </div>
        </div>

        <!-- Desktop Sidebar -->
        <div class="hidden min-h-screen w-72 border-r border-r-primary-dark bg-primary-light md:block">
            <div class="flex h-16 items-center space-x-2 border-b border-b-primary-dark p-2">
                <img src="{{ asset('images/achievements/Logo SDB.png') }}" alt="Logo" class="h-12 w-12" />
                <div class="flex flex-col">
                    <h1 class="font-semibold">E-Learning</h1>
                    <h2>Sekolah Darma Bangsa</h2>
                </div>
            </div>
            <div class="mt-4 space-y-1 px-4">
                @foreach ($menu['teacherMenu'] as $item)
                    <x-sidebar-link href="{{ route($item->url) }}" :active="in_array(request()->route()->getName(), $item->routes ?? [])" :icon="svg($item->icon, 'h-5 w-5')">
                        {{ $item->label }}
                    </x-sidebar-link>
                @endforeach
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex w-0 flex-1 flex-col">
            <!-- Header -->
            <header
                class="flex h-16 items-center justify-between border-b border-b-primary-dark bg-primary-light px-4 md:px-6">
                <!-- Mobile Menu Button -->
                <div class="flex items-center">
                    <x-mobile-menu-button @click="mobileMenuOpen = true" :is-open="false" class="md:hidden" />
                    <h1 class="ml-2 text-lg md:ml-0 md:text-xl font-medium text-gray-800">{{ $header }}</h1>
                </div>

                <div class="flex items-center">
                    <!-- Notification Dropdown -->
                    <div class="mr-2 md:mr-4">
                        <livewire:notification-dropdown />
                    </div>

                    <x-layouts.components.settings-dropdown />
                </div>
            </header>

            @if (session('success'))
                <div class="mx-4 mb-4 rounded bg-green-100 p-4 text-green-800 md:mx-6">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 mb-4 rounded bg-red-100 p-4 text-red-800 md:mx-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Content -->
            <main class="flex-1 overflow-auto p-4 md:p-6">
                <div class="mx-auto max-w-7xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</x-layouts>
