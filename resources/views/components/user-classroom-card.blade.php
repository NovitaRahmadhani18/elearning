@props([
    'classroom',
])

@php
    $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-indigo-500', 'bg-pink-500', 'bg-teal-500'];
    $colorIndex = $classroom->id % count($colors);
    $selectedColor = $colors[$colorIndex];

    $icons = ['menu-book', 'school', 'lightbulb', 'science', 'calculate'];
    $iconIndex = $classroom->id % count($icons);
    $selectedIcon = $icons[$iconIndex];

    $progress = $classroom->pivot->progress ?? 0;
    $progressColor = match (true) {
        $progress >= 90 => 'bg-green-500',
        $progress >= 70 => 'bg-blue-500',
        $progress >= 50 => 'bg-yellow-500',
        $progress >= 25 => 'bg-orange-500',
        default => 'bg-red-500',
    };
@endphp

<div
    class="group transform overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
>
    <!-- Card Image/Header -->
    <div class="relative h-40 overflow-hidden">
        @if ($classroom->thumbnail_path)
            <img
                src="{{ Storage::url($classroom->thumbnail_path) }}"
                alt="{{ $classroom->title }}"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
            />
        @else
            <!-- Solid Background with Icon -->
            <div class="{{ $selectedColor }} relative flex h-full w-full items-center justify-center">
                <x-dynamic-component :component="'gmdi-' . $selectedIcon" class="h-16 w-16 text-white" />
            </div>
        @endif

        <!-- Progress Badge -->
        <div class="absolute right-3 top-3">
            <div class="rounded-full bg-black/60 px-3 py-1 text-xs font-semibold text-white backdrop-blur-sm">
                {{ number_format($progress, 0) }}% Complete
            </div>
        </div>
    </div>

    <!-- Card Content -->
    <div class="p-5">
        <!-- Title and Description -->
        <div class="mb-4">
            <a
                href="{{ route('user.classroom.show', $classroom->id) }}"
                class="block transition-colors group-hover:text-primary"
            >
                <h3 class="mb-1 line-clamp-1 text-lg font-semibold text-gray-900">
                    {{ $classroom->title }}
                </h3>
            </a>

            @if ($classroom->description)
                <p class="line-clamp-2 text-sm text-gray-600">
                    {{ $classroom->description }}
                </p>
            @endif
        </div>

        <!-- Teacher Info -->
        <div class="mb-4 flex items-center">
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-500 text-xs font-bold text-white">
                {{ $classroom->teacher->initials() }}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">{{ $classroom->teacher->name }}</p>
                <p class="text-xs text-gray-500">Instructor</p>
            </div>
        </div>

        <!-- Progress Section -->
        <div class="mb-4">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Progress</span>
                <span class="{{ $progress >= 50 ? 'text-green-600' : 'text-gray-600' }} text-sm font-bold">
                    {{ number_format($progress, 1) }}%
                </span>
            </div>

            <!-- Progress Bar -->
            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                <div
                    class="{{ $progressColor }} h-2 rounded-full transition-all duration-500 ease-out"
                    style="width: {{ $progress }}%"
                ></div>
            </div>

            <!-- Progress Status -->
            <div class="mt-2 text-xs text-gray-500">
                @if ($progress >= 100)
                    <span class="font-medium text-green-600">✓ Course Completed</span>
                @elseif ($progress >= 75)
                    <span class="font-medium text-blue-600">Almost there!</span>
                @elseif ($progress >= 50)
                    <span class="font-medium text-yellow-600">Good progress</span>
                @elseif ($progress > 0)
                    <span class="font-medium text-orange-600">Getting started</span>
                @else
                    <span class="text-gray-500">Not started</span>
                @endif
            </div>
        </div>

        <!-- Stats -->
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

        <!-- Action Button -->
        <a
            href="{{ route('user.classroom.show', $classroom->id) }}"
            class="block w-full rounded-lg bg-primary-dark px-4 py-3 text-center font-medium text-white transition-colors duration-200 hover:bg-primary-dark group-hover:shadow-md"
        >
            <span class="flex items-center justify-center">
                <x-gmdi-play-arrow class="mr-2 h-4 w-4" />
                @if ($progress >= 100)
                    Review Course
                @elseif ($progress > 0)
                    Continue Learning
                @else
                    Start Learning
                @endif
            </span>
        </a>
    </div>
</div>
