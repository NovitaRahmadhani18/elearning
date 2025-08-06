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
                <div class="mr-4 rounded-lg bg-secondary-light p-3">
                    <x-gmdi-menu-book class="h-6 w-6 text-secondary-dark" />
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
                <div class="mr-4 rounded-lg bg-secondary-light p-3">
                    <x-gmdi-check-circle class="h-6 w-6 text-secondary-dark" />
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
                            <div class="mr-4 rounded-lg bg-secondary-light p-2">
                                <x-gmdi-event class="h-6 w-6 text-secondary-dark" />
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
                    @forelse ($achievements as $achievement)
                        <div
                            class="relative overflow-hidden rounded-lg bg-white shadow-sm transition-transform hover:scale-105"
                        >
                            <!-- Achievement Status Overlay -->
                            @if (! $achievement['unlocked'])
                                <div
                                    class="absolute inset-0 z-10 flex items-center justify-center bg-gray-900 bg-opacity-60"
                                >
                                    <x-gmdi-lock class="h-12 w-12 text-white opacity-80" />
                                </div>
                            @else
                                <div class="absolute right-4 top-4 z-10">
                                    <div class="rounded-full bg-green-500 p-2">
                                        <x-gmdi-check class="h-4 w-4 text-white" />
                                    </div>
                                </div>
                            @endif

                            <div class="{{ ! $achievement['unlocked'] ? 'opacity-60' : '' }} p-6">
                                <!-- Achievement Icon -->
                                <div class="mb-4 flex justify-center">
                                    <img
                                        src="{{ $achievement['image'] }}"
                                        alt="{{ $achievement['name'] }}"
                                        class="h-16 w-16"
                                    />
                                </div>
                                <h3 class="text-center font-semibold text-gray-800">{{ $achievement['name'] }}</h3>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500">
                            <p>No achievements unlocked yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.user-layout>
