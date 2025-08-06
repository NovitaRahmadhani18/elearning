<x-layouts>
    <header class="bg-primary-light shadow-sm">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <!-- Logo -->
                        <div class="flex items-center">
                            <img src="{{ asset('images/achievements/Logo SDB.png') }}" alt="Logo" class="h-12 w-12" />
                        </div>
                    </div>
                    <div class="ml-6 flex space-x-4">
                        @foreach ($menu['userMenu'] as $item)
                            <a
                                href="{{ route($item->url) }}"
                                @class([
                                    'space-x-1 rounded-md px-3 py-2 text-sm font-medium text-gray-500 hover:bg-primary-light hover:text-gray-900',
                                    'text-primary-dark' => in_array(
                                        request()
                                            ->route()
                                            ->getName(),
                                        $item->routes ?? [],
                                    ),
                                ])
                            >
                                <x-icon :name="$item->icon" class="ml-1 inline h-5 w-5" />
                                <span>
                                    {{ $item->label }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center">
                    <button class="mr-4 text-gray-500">
                        <x-gmdi-notifications-o class="h-6 w-6" />
                    </button>
                    <div
                        class="ml-4 flex items-center rounded-lg bg-gradient-to-r from-secondary/10 to-secondary/20 px-3 py-2 shadow-sm"
                    >
                        <x-gmdi-stars class="mr-2 h-5 w-5 text-secondary" />
                        <span class="text-sm font-bold text-secondary-dark">
                            {{ number_format( auth()->user()->getPoints(),) }} Point
                        </span>
                    </div>
                    <div class="flex items-center">
                        <x-layouts.components.settings-dropdown />
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
