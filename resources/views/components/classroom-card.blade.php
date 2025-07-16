@php
    $colors = [
        'bg-gradient-to-br from-blue-600 to-blue-800',
        'bg-gradient-to-br from-green-600 to-green-800',
        'bg-gradient-to-br from-purple-600 to-purple-800',
        'bg-gradient-to-br from-indigo-600 to-indigo-800',
        'bg-gradient-to-br from-pink-600 to-pink-800',
        'bg-gradient-to-br from-teal-600 to-teal-800',
    ];
    $colorIndex = $classroom->id % count($colors);
    $selectedColor = $colors[$colorIndex];

    $icons = ['menu-book', 'school', 'lightbulb', 'science', 'calculate'];
    $iconIndex = $classroom->id % count($icons);
    $selectedIcon = $icons[$iconIndex];
@endphp

<div class="group overflow-hidden rounded-xl bg-white shadow-sm border border-gray-200 classroom-card"
    id="classroom-{{ $classroom->id }}">

    <!-- Card Image -->
    <div class="relative h-48 overflow-hidden">
        @if ($classroom->thumbnail_path)
            <img src="{{ Storage::url($classroom->thumbnail_path) }}" alt="{{ $classroom->title }}"
                class="h-full w-full object-cover" />
        @else
            <x-classroom-placeholder :color="$selectedColor" :icon="$selectedIcon" :title="$classroom->title" :category="$classroom->category" />
        @endif
    </div>

    <!-- Card Content -->
    <div class="p-6">
        <!-- Title and Description (Title as Navigation) -->
        <div class="mb-4">
            <a href="{{ route('admin.classroom.show', $classroom->id) }}"
                class="block hover:text-primary transition-colors">
                <h3 class="text-lg font-semibold text-gray-900 line-clamp-1 hover:text-primary">
                    {{ $classroom->title }}
                </h3>
            </a>
            @if ($classroom->category)
                <div class="mt-1 mb-2">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-800 text-white">
                        {{ $classroom->category }}
                    </span>
                </div>
            @endif
            <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                {{ $classroom->description ?? 'No description available' }}
            </p>
        </div>

        <!-- Stats -->
        <div class="mb-4 flex items-center space-x-4 text-sm text-gray-500">
            <div class="flex items-center">
                <x-gmdi-group class="mr-1 h-4 w-4" />
                <span>{{ $classroom->students_count ?? 0 }} students</span>
            </div>
            <div class="flex items-center">
                <x-gmdi-quiz class="mr-1 h-4 w-4" />
                <span>{{ $classroom->quizzes_count ?? 0 }} quizzes</span>
            </div>
            <div class="flex items-center">
                <x-gmdi-description class="mr-1 h-4 w-4" />
                <span>{{ $classroom->materials_count ?? 0 }} materials</span>
            </div>
        </div>

        <!-- Teacher Info -->
        <div class="mb-4 flex items-center">
            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                <x-gmdi-person class="h-4 w-4 text-gray-600" />
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">{{ $classroom->teacher->name }}</p>
                <p class="text-xs text-gray-500">Teacher</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-2">
            <a href="{{ route('admin.classroom.edit', $classroom->id) }}"
                class="flex-1 flex items-center justify-center rounded-md bg-secondary px-3 py-2 text-sm font-medium text-white hover:bg-secondary-dark transition-colors">
                <x-gmdi-edit class="mr-1 h-4 w-4" />
                Edit
            </a>

            <form action="{{ route('admin.classroom.destroy', $classroom->id) }}" method="POST"
                x-target="classroom-{{ $classroom->id }}" class="flex-1"
                @ajax:before="confirm('Are you sure you want to delete {{ $classroom->title }}? This action cannot be undone.') || $event.preventDefault()"
                @ajax:success="$el.closest('.classroom-card').remove()"
                @ajax:error="alert('Error: ' + ($event.detail.message || 'Failed to delete classroom'))">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full flex items-center justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                    <x-gmdi-delete class="mr-1 h-4 w-4" />
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
