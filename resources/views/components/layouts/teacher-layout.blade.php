<x-layouts>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="hidden min-h-screen w-72 border-r border-r-primary-dark bg-primary-light md:block">
            <div class="h-16 border-b border-b-primary-dark"></div>
            <div class="mt-4 space-y-1 px-4">
                @foreach ($menu['teacherMenu'] as $item)
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
            <header class="flex h-16 items-center justify-between border-b border-b-primary-dark bg-primary-light px-6">
                <h1 class="text-xl font-medium text-gray-800">{{ $header }}</h1>
                <div class="flex items-center">
                    <button class="mr-4 text-gray-500">
                        <x-gmdi-notifications-o class="h-6 w-6" />
                    </button>

                    <x-layouts.components.settings-dropdown />
                </div>
            </header>

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

            <!-- Content -->
            <main class="flex-1 overflow-auto p-6">
                <div class="mx-auto max-w-7xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</x-layouts>
