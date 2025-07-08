<x-layouts>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="hidden min-h-screen w-72 border-r border-r-primary/20 bg-white md:block">
            <div class="h-16 border-b border-b-primary/20"></div>
            <div class="mt-4 space-y-1 px-4">
                @foreach ($menu['adminMenu'] as $item)
                    <x-sidebar-link
                        href="{{route($item->url)}}"
                        :active="
                            in_array(
                                request()->route()->getName(),
                                $item->routes ?? []
                            )
                        "
                        :icon="
                            svg($item->icon, 'h-5 w-5')
                        "
                    >
                        {{ $item->label }}
                    </x-sidebar-link>
                @endforeach
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex w-0 flex-1 flex-col">
            <!-- Header -->
            <header class="flex h-16 items-center justify-between border-b border-b-primary/20 bg-white px-6">
                <h1 class="text-xl font-medium text-gray-800">{{ $header }}</h1>
                <div class="flex items-center">
                    <button class="mr-4 text-gray-500">
                        <x-gmdi-notifications-o class="h-6 w-6" />
                    </button>

                    <x-layouts.components.settings-dropdown />
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-auto p-6">
                <div class="mx-auto max-w-7xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</x-layouts>
