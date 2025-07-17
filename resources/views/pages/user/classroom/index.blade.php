<x-layouts.user-layout>
    <x-slot name="header">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">My Classrooms</h1>
            <p class="mt-2 text-gray-600">Continue your learning journey</p>
        </div>
    </x-slot>

    @if ($classrooms->count() > 0)
        <!-- Featured Classroom (Latest with Progress) -->
        @php
            $featuredClassroom = $classrooms->first();
        @endphp

        <div class="mb-8 rounded-xl bg-blue-600 p-6 text-white shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <h2 class="mb-2 text-2xl font-bold">Continue Learning</h2>
                    <h3 class="mb-2 text-xl">{{ $featuredClassroom->title }}</h3>
                    <p class="mb-4 text-blue-100">
                        {{ $featuredClassroom->description ?? 'Keep up the great work!' }}
                    </p>

                    <!-- Progress -->
                    <div class="mb-3">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm font-medium">Your Progress</span>
                            <span class="text-sm font-bold">
                                {{ number_format($featuredClassroom->pivot->progress, 1) }}%
                            </span>
                        </div>
                        <div class="h-3 w-full overflow-hidden rounded-full bg-white/20">
                            <div
                                class="h-3 rounded-full bg-white transition-all duration-500"
                                style="width: {{ $featuredClassroom->pivot->progress }}%"
                            ></div>
                        </div>
                    </div>

                    <a
                        href="{{ route('user.classroom.show', $featuredClassroom->id) }}"
                        class="inline-flex items-center rounded-lg bg-white px-4 py-2 font-semibold text-blue-600 transition-colors hover:bg-blue-50"
                    >
                        <x-gmdi-play-arrow class="mr-2 h-4 w-4" />
                        Continue Learning
                    </a>
                </div>

                <!-- Icon -->
                <div class="hidden md:block">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-white/10">
                        <x-gmdi-school class="h-12 w-12 text-white" />
                    </div>
                </div>
            </div>
        </div>

        <!-- All Classrooms Grid -->
        <div class="mb-6">
            <h2 class="mb-4 text-2xl font-semibold text-gray-900">All Courses</h2>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($classrooms as $classroom)
                <x-user-classroom-card :classroom="$classroom" />
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="py-12 text-center">
            <div class="mx-auto mb-4 h-24 w-24 text-gray-400">
                <x-gmdi-school class="h-full w-full" />
            </div>
            <h3 class="mb-2 text-lg font-medium text-gray-900">No Classrooms Yet</h3>
            <p class="mb-6 text-gray-500">Join your first classroom to start learning!</p>
            <a
                href="{{ route('user.classroom.join.form', ['classroom' => 'demo']) }}"
                class="inline-flex items-center rounded-lg bg-primary px-4 py-2 font-medium text-white transition-colors hover:bg-primary-dark"
            >
                <x-gmdi-add class="mr-2 h-4 w-4" />
                Join Classroom
            </a>
        </div>
    @endif
</x-layouts.user-layout>
