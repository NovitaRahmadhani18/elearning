<x-layouts>
    <header class="bg-white shadow-sm">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <!-- Logo -->
                        <div class="flex items-center">LOGO</div>
                    </div>
                    <div class="ml-6 flex space-x-4">
                        @foreach ($menu['userMenu'] as $item)
                            <a
                                href="{{ route($item->url) }}"
                                @class([
                                    'space-x-1 rounded-md px-3 py-2 text-sm font-medium',
                                    'text-primary' => in_array(
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
                    <div class="flex items-center">
                        <x-layouts.components.settings-dropdown />
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>
</x-layouts>
