@props([
    'material',
    'showActions' => true,
    'showClassroom' => true,
    'showDescription' => true,
    'size' => 'default', // default, small, large
])

@php
    $cardClasses = match ($size) {
        'small' => 'h-32',
        'large' => 'h-48',
        default => 'h-40',
    };
@endphp

<div class="group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-lg hover:border-primary/40"
    x-init id="material-{{ $material->id }}">
    <!-- Material Thumbnail -->
    <div class="relative {{ $cardClasses }} bg-gradient-to-br from-primary-light to-primary/30 overflow-hidden">
        @if (!empty($material->thumbnailUrl))
            <img src="{{ $material->thumbnailUrl }}" alt="{{ $material->title }}"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy" />
        @else
            <div class="flex h-full items-center justify-center">
                <x-gmdi-description class="h-16 w-16 text-primary/50" />
            </div>
        @endif

        <!-- Material Type Badge -->
        <div class="absolute top-3 right-3">
            <span
                class="inline-flex items-center rounded-full bg-secondary px-2 py-1 text-xs font-medium text-gray-800">
                <x-gmdi-book class="mr-1 h-3 w-3" />
                Material
            </span>
        </div>

        <!-- Quick Actions Overlay -->
        <div
            class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
            <div class="flex space-x-2">
                <a href="{{ route('teacher.material.show', $material->id) }}"
                    class="flex items-center justify-center rounded-md bg-white/20 p-2 text-white backdrop-blur-sm hover:bg-white/30 transition-colors"
                    title="View Material">
                    <x-gmdi-visibility class="h-4 w-4" />
                </a>
                @if ($showActions)
                    <a href="{{ route('teacher.material.edit', $material->id) }}"
                        class="flex items-center justify-center rounded-md bg-white/20 p-2 text-white backdrop-blur-sm hover:bg-white/30 transition-colors"
                        title="Edit Material">
                        <x-gmdi-edit class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Material Content -->
    <div class="flex flex-1 flex-col p-4">
        <!-- Title -->
        <a href="{{ route('teacher.material.show', $material->id) }}"
            class="mb-2 block font-semibold text-gray-800 transition-colors hover:text-primary group-hover:text-primary">
            {{ Str::limit($material->title, 50) }}
        </a>

        <!-- Classroom Info -->
        @if ($showClassroom && $material->classroom)
            <div class="mb-3 flex items-center text-sm text-gray-600">
                <x-gmdi-class class="mr-1 h-4 w-4" />
                <span>{{ $material->classroom->title }}</span>
            </div>
        @endif

        <!-- Description Preview -->
        @if ($showDescription && $material->description)
            <p class="mb-3 text-sm text-gray-500 overflow-hidden"
                style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                {{ Str::limit(strip_tags($material->description), 80) }}
            </p>
        @endif

        <!-- Footer -->
        <div class="mt-auto flex items-center justify-between pt-2">
            <!-- Last Updated -->
            <div class="flex items-center text-xs text-gray-500">
                <x-gmdi-update class="mr-1 h-3 w-3" />
                <span>{{ $material->updated_at->diffForHumans() }}</span>
            </div>

            <!-- Actions -->
            @if ($showActions)
                <div class="flex items-center space-x-2">
                    <a href="{{ route('teacher.material.edit', $material->id) }}"
                        class="flex items-center justify-center rounded-md p-1.5 text-gray-500 transition-colors hover:bg-primary/10 hover:text-primary"
                        title="Edit Material">
                        <x-gmdi-edit class="h-4 w-4" />
                    </a>

                    <form action="{{ route('teacher.material.destroy', $material->id) }}" method="POST" x-init
                        x-target='material-{{ $material->id }}' class="inline-block">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            class="flex items-center justify-center rounded-md p-1.5 text-gray-500 transition-colors hover:bg-red-50 hover:text-red-600"
                            title="Delete Material"
                            onclick="return confirm('Are you sure you want to delete this material?')">
                            <x-gmdi-delete class="h-4 w-4" />
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
