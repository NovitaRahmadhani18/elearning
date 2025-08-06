<div class="min-h-screen bg-gray-50 p-6">
    <div class="mx-auto max-w-7xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Lencana Pencapaian</h1>
            <p class="mt-2 text-gray-600">Kumpulkan lencana dengan menyelesaikan berbagai tantangan</p>
        </div>

        <!-- Stats Cards -->
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-gmdi-emoji-events class="h-8 w-8 text-primary" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Lencana</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ $stats['unlocked_achievements'] }}/{{ $stats['total_achievements'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-gmdi-stars class="h-8 w-8 text-secondary" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Point</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ number_format($stats['total_experience']) }} Point
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-gmdi-trending-up class="h-8 w-8 text-green-500" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tingkat Penyelesaian</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ $stats['total_achievements'] > 0 ? round(($stats['unlocked_achievements'] / $stats['total_achievements']) * 100) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Achievement Grid -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($achievements as $achievement)
                <div
                    class="relative overflow-hidden rounded-lg bg-white shadow-sm transition-transform hover:scale-105"
                >
                    <!-- Achievement Status Overlay -->
                    @if (! $achievement['unlocked'])
                        <div class="absolute inset-0 z-10 flex items-center justify-center bg-gray-900 bg-opacity-60">
                            <x-gmdi-lock class="h-12 w-12 text-white opacity-80" />
                        </div>
                    @else
                        <div class="absolute right-4 top-4 z-10">
                            <div class="rounded-full bg-green-500 p-2">
                                <x-gmdi-check class="h-4 w-4 text-white" />
                            </div>
                        </div>
                    @endif

                    <div class="{{ ! $achievement['unlocked'] ? 'opacity-60' : '' }} p-6">
                        <!-- Achievement Icon -->
                        <div class="mb-4 flex justify-center">
                            @if ($achievement['image'])
                                <img
                                    src="{{ $achievement['image'] }}"
                                    alt="{{ $achievement['name'] }}"
                                    class="h-16 w-16"
                                />
                            @else
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-full bg-primary text-white"
                                >
                                    <x-gmdi-emoji-events class="h-8 w-8" />
                                </div>
                            @endif
                        </div>

                        <!-- Achievement Details -->
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $achievement['name'] }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ $achievement['description'] }}</p>

                            @if ($achievement['unlocked'] && $achievement['unlocked_at'])
                                <p class="mt-2 text-xs text-green-600">
                                    Diperoleh: {{ $achievement['unlocked_at']->format('d M Y') }}
                                </p>
                            @endif
                        </div>

                        <!-- Progress Bar (if applicable) -->
                        @if (! $achievement['unlocked'] && $achievement['progress'] > 0)
                            <div class="mt-4">
                                <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                                    <div
                                        class="h-2 rounded-full bg-primary transition-all duration-300"
                                        style="width: {{ $achievement['progress'] }}%"
                                    ></div>
                                </div>
                                <p class="mt-1 text-center text-xs text-gray-500">
                                    {{ $achievement['progress'] }}% complete
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if (empty($achievements))
            <div class="rounded-lg bg-white p-12 text-center shadow-sm">
                <x-gmdi-emoji-events class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada lencana</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai mengerjakan kuis untuk mendapatkan lencana pertama Anda!</p>
            </div>
        @endif
    </div>
</div>
