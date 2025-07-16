@props(['material'])

<div class="group relative flex items-center overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-lg hover:border-primary/40"
    x-init id="material-{{ $material->id }}">
    <!-- Material Thumbnail -->
    <div class="relative w-24 h-20 bg-gradient-to-br from-primary-light to-primary/30 overflow-hidden flex-shrink-0">
        @if (!empty($material->thumbnailUrl))
            <img src="{{ $material->thumbnailUrl }}" alt="{{ $material->title }}"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy" />
        @else
            <div class="flex h-full items-center justify-center">
                <x-gmdi-description class="h-8 w-8 text-primary/50" />
            </div>
        @endif

        <!-- Material Type Badge -->
        <div class="absolute top-1 right-1">
            <span
                class="inline-flex items-center rounded-full bg-secondary px-1.5 py-0.5 text-xs font-medium text-gray-800">
                <x-gmdi-book class="h-2.5 w-2.5" />
            </span>
        </div>
    </div>

    <!-- Material Content -->
    <div class="flex flex-1 flex-col p-4">
        <div class="flex items-start justify-between">
            <!-- Main Content -->
            <div class="flex-1 min-w-0">
                <!-- Title -->
                <a href="{{ route('teacher.material.show', $material->id) }}"
                    class="block font-semibold text-gray-800 transition-colors hover:text-primary group-hover:text-primary truncate">
                    {{ $material->title }}
                </a>

                <!-- Classroom Info -->
                @if ($material->classroom)
                    <div class="mt-1 flex items-center text-sm text-gray-600">
                        <x-gmdi-class class="mr-1 h-3 w-3" />
                        <span>{{ $material->classroom->title }}</span>
                    </div>
                @endif

                <!-- Description Preview -->
                @if ($material->description)
                    <p class="mt-1 text-sm text-gray-500 overflow-hidden"
                        style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;">
                        {{ Str::limit(strip_tags($material->description), 60) }}
                    </p>
                @endif

                <!-- Last Updated -->
                <div class="mt-2 flex items-center text-xs text-gray-500">
                    <x-gmdi-update class="mr-1 h-3 w-3" />
                    <span>{{ $material->updated_at->diffForHumans() }}</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2 ml-4">
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
        </div>
    </div>
</div>
