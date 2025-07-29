<x-layouts.user-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600">Welcome back, {{ auth()->user()->name }}</p>
    </div>

    <!-- User XP Progress -->
    <div class="mb-8">
        <x-user-xp-progress />
    </div>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <div class="flex items-center">
                <div class="mr-4 rounded-lg bg-gray-100 p-3">
                    <x-gmdi-menu-book class="h-6 w-6 text-gray-600" />
                </div>
                <div>
                    <p class="text-sm text-gray-600">Class in Progress</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ $classroomInProgress->count() ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow-sm">
            <div class="flex items-center">
                <div class="mr-4 rounded-lg bg-gray-100 p-3">
                    <x-gmdi-check-circle class="h-6 w-6 text-gray-600" />
                </div>
                <div>
                    <p class="text-sm text-gray-600">Completed Class</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ $classroomCompleted->count() ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Current Courses -->
        <div class="lg:col-span-2">
            <div class="mb-6 rounded-lg bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-gray-800">Current Courses</h2>
                <div>
                    @forelse ($classrooms as $classroom)
                        <x-course-card :$classroom />
                    @empty
                        <div class="text-center text-gray-500">
                            <p>No current courses available.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Content -->
        <div class="lg:col-span-1">
            <!-- Upcoming Deadlines -->
            <div class="mb-6 rounded-lg bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-gray-800">Upcoming Deadlines</h2>

                <div class="space-y-4">
                    @forelse ($upcomingQuizzes as $quiz)
                        <div class="flex items-center">
                            <div class="mr-4 rounded-lg bg-gray-100 p-2">
                                <x-gmdi-event class="h-6 w-6 text-gray-600" />
                            </div>
                            <div>
                                <a
                                    class="block font-medium text-gray-800"
                                    href="{{ route('user.classroom.quiz.show', [$quiz->classroom->id, $quiz->id]) }}"
                                >
                                    {{ $quiz->title }}
                                </a>
                                <span class="font-medium text-gray-800">
                                    Due {{ $quiz->due_time->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500">
                            <p>No upcoming deadlines.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Achievement Badges -->
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-gray-800">Achievement Badges</h2>

                <div class="grid grid-cols-2 gap-4">
                    <x-badge
                        title="Quick Learner"
                        :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-6 w-6 text-gray-500\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z\' /></svg>'"
                    />

                    <x-badge
                        title="Top Student"
                        :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-6 w-6 text-gray-500\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z\' /></svg>'"
                    />

                    <x-badge
                        title="Expert"
                        :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-6 w-6 text-gray-500\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z\' /></svg>'"
                    />

                    <x-badge
                        title="Achiever"
                        :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-6 w-6 text-gray-500\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z\' /></svg>'"
                    />
                </div>
            </div>
        </div>
    </div>
</x-layouts.user-layout>
