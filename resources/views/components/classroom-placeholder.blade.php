@props(['color', 'icon', 'title', 'category'])

<div class="h-full w-full {{ $color }} flex items-center justify-center relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-4 -left-4 w-20 h-20 bg-white rounded-full blur-sm"></div>
        <div class="absolute top-1/2 right-6 w-16 h-16 bg-white rounded-full blur-sm"></div>
        <div class="absolute bottom-6 left-1/4 w-12 h-12 bg-white rounded-full blur-sm"></div>
        <div class="absolute top-8 right-1/4 w-8 h-8 bg-white rounded-full blur-sm"></div>
    </div>

    <!-- Content -->
    <div class="text-center text-white relative z-10">
        <div class="bg-black/20 backdrop-blur-sm rounded-2xl p-6 border border-white/30 shadow-xl">
            <x-dynamic-component :component="'gmdi-' . $icon" class="mx-auto h-16 w-16 mb-3 text-white drop-shadow-lg" />

            <h4 class="text-xl font-bold tracking-wide text-white text-shadow-lg">
                {{ strtoupper(substr($title, 0, 2)) }}
            </h4>
            <p class="text-sm text-white/90 mt-1 font-medium text-shadow-md">
                {{ $category ?? 'Classroom' }}
            </p>
        </div>
    </div>
</div>
