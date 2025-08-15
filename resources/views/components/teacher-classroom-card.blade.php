@php
    $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-indigo-500', 'bg-pink-500', 'bg-teal-500'];
    $colorIndex = $classroom->id % count($colors);
    $selectedColor = $colors[$colorIndex];

    $icons = ['menu-book', 'school', 'lightbulb', 'science', 'calculate'];
    $iconIndex = $classroom->id % count($icons);
    $selectedIcon = $icons[$iconIndex];
@endphp

<div class="group transform overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg classroom-card"
    id="classroom-{{ $classroom->id }}">

    <!-- Card Image/Header -->
    <div class="relative h-40 overflow-hidden">
        @if ($classroom->thumbnail_path)
            <img src="{{ Storage::url($classroom->thumbnail_path) }}" alt="{{ $classroom->title }}"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
        @else
            <!-- Solid Background with Icon -->
            <div class="{{ $selectedColor }} relative flex h-full w-full items-center justify-center">
                <x-dynamic-component :component="'gmdi-' . $selectedIcon" class="h-16 w-16 text-white" />
            </div>
        @endif

        <!-- Category Badge -->
        @if ($classroom->category)
            <div class="absolute left-3 top-3">
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-black/60 text-white backdrop-blur-sm">
                    {{ $classroom->category }}
                </span>
            </div>
        @endif

        <!-- Students Count Badge -->
        <div class="absolute right-3 top-3">
            <div class="rounded-full bg-black/60 px-3 py-1 text-xs font-semibold text-white backdrop-blur-sm">
                {{ $classroom->students_count ?? 0 }} students
            </div>
        </div>
    </div>

    <!-- Card Content -->
    <div class="p-5">
        <!-- Title and Description -->
        <div class="mb-4">
            <a href="{{ route('teacher.classroom.show', $classroom->id) }}"
                class="block transition-colors group-hover:text-primary">
                <h3 class="mb-1 line-clamp-1 text-lg font-semibold text-gray-900 hover:text-primary">
                    {{ $classroom->title }}
                </h3>
            </a>

            @if ($classroom->description)
                <p class="line-clamp-2 text-sm text-gray-600">
                    {{ $classroom->description }}
                </p>
            @else
                <p class="text-sm text-gray-500 italic">No description available</p>
            @endif
        </div>

        <!-- Teacher Info -->
        <div class="mb-4 flex items-center">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-500 text-xs font-bold text-white">
                {{ $classroom->teacher->initials() }}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">{{ $classroom->teacher->name }}</p>
                <p class="text-xs text-gray-500">Teacher</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="mb-4 grid grid-cols-3 gap-2 text-center">
            <div class="rounded-lg bg-gray-50 p-2">
                <div class="text-sm font-semibold text-gray-900">{{ $classroom->contents_count ?? 0 }}</div>
                <div class="text-xs text-gray-500">Contents</div>
            </div>
            <div class="rounded-lg bg-gray-50 p-2">
                <div class="text-sm font-semibold text-gray-900">{{ $classroom->quizzes_count ?? 0 }}</div>
                <div class="text-xs text-gray-500">Quizzes</div>
            </div>
            <div class="rounded-lg bg-gray-50 p-2">
                <div class="text-sm font-semibold text-gray-900">{{ $classroom->materials_count ?? 0 }}</div>
                <div class="text-xs text-gray-500">Materials</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-2">
            <a href="{{ route('teacher.classroom.show', $classroom->id) }}"
                class="flex-1 flex items-center justify-center rounded-lg bg-primary px-3 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors duration-200">
                <x-gmdi-visibility class="mr-1 h-4 w-4" />
                View
            </a>

            <a href="{{ route('teacher.classroom.edit', $classroom->id) }}"
                class="flex-1 flex items-center justify-center rounded-lg bg-secondary px-3 py-2 text-sm font-medium text-white hover:bg-secondary-dark transition-colors duration-200">
                <x-gmdi-edit class="mr-1 h-4 w-4" />
                Edit
            </a>

            <form action="{{ route('teacher.classroom.destroy', $classroom->id) }}" method="POST" class="flex-1"
                onsubmit="return confirm('Are you sure you want to delete {{ $classroom->title }}? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full flex items-center justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors duration-200">
                    <x-gmdi-delete class="mr-1 h-4 w-4" />
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
