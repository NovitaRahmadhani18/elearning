@props(['material'])

<div class="group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-md hover:border-primary/30"
    x-init id="material-{{ $material->id }}">
    <!-- Material Thumbnail -->
    <div class="relative h-40 bg-gradient-to-br from-primary-light to-primary/30 overflow-hidden">
        @if (!empty($material->thumbnailUrl))
            <img src="{{ $material->thumbnailUrl }}" alt="{{ $material->title }}" class="h-full w-full object-cover"
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
    </div>

    <!-- Material Content -->
    <div class="flex flex-1 flex-col p-4">
        <!-- Title -->
        <a href="{{ route('teacher.material.show', $material->id) }}"
            class="mb-2 block font-semibold text-gray-800 transition-colors hover:text-primary">
            {{ Str::limit($material->title, 50) }}
        </a>

        <!-- Classroom Info -->
        @if ($material->classroom)
            <div class="mb-3 flex items-center text-sm text-gray-600">
                <x-gmdi-class class="mr-1 h-4 w-4" />
                <span>{{ $material->classroom->title }}</span>
            </div>
        @endif

        <!-- Description Preview -->
        @if ($material->description)
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
            <div class="flex items-center space-x-1">
                <!-- Edit Button -->
                <a href="{{ route('teacher.material.edit', $material->id) }}"
                    class="flex items-center justify-center rounded-lg bg-secondary/20 px-3 py-1.5 text-sm font-medium text-secondary-dark transition-colors hover:bg-secondary/30 hover:text-secondary-dark"
                    title="Edit Material">
                    <x-gmdi-edit class="mr-1 h-4 w-4" />
                    <span>Edit</span>
                </a>

                <!-- Delete Button -->
                <form action="{{ route('teacher.material.destroy', $material->id) }}" method="POST" x-init
                    x-target='material-{{ $material->id }}' class="inline-block">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="flex items-center justify-center rounded-lg bg-red-50 px-3 py-1.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-100 hover:text-red-700"
                        title="Delete Material"
                        onclick="return confirm('Are you sure you want to delete this material?')">
                        <x-gmdi-delete class="mr-1 h-4 w-4" />
                        <span>Delete</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
