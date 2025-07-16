@props(['material'])

<div class="group relative flex items-center overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-md hover:border-primary/40 p-3"
    x-init id="material-{{ $material->id }}">
    <!-- Material Thumbnail -->
    <div
        class="relative w-12 h-12 bg-gradient-to-br from-primary-light to-primary/30 rounded-md overflow-hidden flex-shrink-0">
        @if (!empty($material->thumbnailUrl))
            <img src="{{ $material->thumbnailUrl }}" alt="{{ $material->title }}"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy" />
        @else
            <div class="flex h-full items-center justify-center">
                <x-gmdi-description class="h-6 w-6 text-primary/50" />
            </div>
        @endif
    </div>

    <!-- Material Content -->
    <div class="flex-1 min-w-0 ml-3">
        <!-- Title -->
        <a href="{{ route('teacher.material.show', $material->id) }}"
            class="block font-medium text-sm text-gray-800 transition-colors hover:text-primary group-hover:text-primary truncate">
            {{ $material->title }}
        </a>

        <!-- Classroom Info -->
        @if ($material->classroom)
            <div class="mt-1 flex items-center text-xs text-gray-500">
                <x-gmdi-class class="mr-1 h-3 w-3" />
                <span class="truncate">{{ $material->classroom->title }}</span>
            </div>
        @endif

        <!-- Last Updated -->
        <div class="mt-1 text-xs text-gray-400">
            {{ $material->updated_at->diffForHumans() }}
        </div>
    </div>

    <!-- Quick Action -->
    <div class="ml-2 opacity-0 group-hover:opacity-100 transition-opacity">
        <a href="{{ route('teacher.material.show', $material->id) }}"
            class="flex items-center justify-center rounded-md p-1 text-gray-500 transition-colors hover:bg-primary/10 hover:text-primary"
            title="View Material">
            <x-gmdi-visibility class="h-4 w-4" />
        </a>
    </div>
</div>
