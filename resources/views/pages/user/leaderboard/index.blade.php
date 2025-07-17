<x-layouts.user-layout>
    <x-slot name="header">Content Leaderboards</x-slot>
    <x-slot name="username">{{ auth()->user()->name }}</x-slot>
    <x-slot name="role">Student</x-slot>

    <x-slot name="navbar">
        <div class="flex space-x-8">
            <a href="{{ route('user.dashboard') }}" class="flex items-center text-gray-600 hover:text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('user.classroom.index') }}" class="flex items-center text-gray-600 hover:text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                </svg>
                My Classrooms
            </a>
            <a href="#" class="flex items-center border-b-2 border-primary font-medium text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm9 4a1 1 0 10-2 0v6a1 1 0 102 0V7zm-3 2a1 1 0 10-2 0v4a1 1 0 102 0V9zm-3 3a1 1 0 10-2 0v1a1 1 0 102 0v-1z"
                        clip-rule="evenodd" />
                </svg>
                Leaderboards
            </a>
            <a href="{{ route('user.lencana.index') }}" class="flex items-center text-gray-600 hover:text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zM10 13a3 3 0 100-6 3 3 0 000 6z"
                        clip-rule="evenodd" />
                </svg>
                Badges
            </a>
        </div>
    </x-slot>

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Content Leaderboards</h1>
        <p class="mt-2 text-gray-600">See how you rank in each material and quiz across all your courses</p>
    </div>

    @if ($contentLeaderboards->isEmpty())
        <!-- Empty State -->
        <div class="rounded-lg bg-white p-12 text-center shadow-sm">
            <x-gmdi-school class="mx-auto h-16 w-16 text-gray-400" />
            <h3 class="mt-4 text-xl font-semibold text-gray-900">No Content Available</h3>
            <p class="mt-2 text-gray-600">You haven't enrolled in any classrooms yet, or your classrooms don't have any
                content.</p>
            <div class="mt-6">
                <a href="{{ route('user.classroom.index') }}"
                    class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-dark">
                    <x-gmdi-add class="mr-2 h-4 w-4" />
                    Join a Classroom
                </a>
            </div>
        </div>
    @else
        <!-- Content Leaderboards Grid -->
        <div class="space-y-8">
            @foreach ($contentLeaderboards as $item)
                @php
                    $content = $item['content'];
                    $classroom = $item['classroom'];
                    $leaderboard = $item['leaderboard'];
                    $userRank = $item['user_rank'];
                    $totalParticipants = $item['total_participants'];
                    $isQuiz = $content->contentable_type === App\Models\Quiz::class;
                @endphp

                <div class="overflow-hidden rounded-lg bg-white shadow-sm border border-gray-200">
                    <!-- Content Header -->
                    <div class="bg-gray-50 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                @if ($isQuiz)
                                    <div class="mr-4 rounded-lg bg-purple-100 p-2">
                                        <x-gmdi-quiz class="h-6 w-6 text-purple-600" />
                                    </div>
                                @else
                                    <div class="mr-4 rounded-lg bg-blue-100 p-2">
                                        <x-gmdi-menu-book class="h-6 w-6 text-blue-600" />
                                    </div>
                                @endif

                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $content->contentable->title }}
                                    </h3>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <span>{{ $classroom->title }}</span>
                                        <span class="mx-2">•</span>
                                        <span class="capitalize">{{ $isQuiz ? 'Quiz' : 'Material' }}</span>
                                        @if ($content->contentable->points ?? 0)
                                            <span class="mx-2">•</span>
                                            <span>{{ $content->contentable->points ?? 10 }} points</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- User Rank Badge -->
                            @if ($userRank)
                                <div class="text-right">
                                    <div
                                        class="inline-flex items-center rounded-full {{ $userRank <= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-700' }} px-3 py-1 text-sm font-medium">
                                        @if ($userRank == 1)
                                            <x-gmdi-emoji-events class="mr-1 h-4 w-4" />
                                        @elseif($userRank <= 3)
                                            <x-gmdi-workspace-premium class="mr-1 h-4 w-4" />
                                        @else
                                            <x-gmdi-person class="mr-1 h-4 w-4" />
                                        @endif
                                        Your Rank: #{{ $userRank }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        of {{ $totalParticipants }} students
                                    </div>
                                </div>
                            @else
                                <div class="text-right">
                                    <div
                                        class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600">
                                        <x-gmdi-remove class="mr-1 h-4 w-4" />
                                        Not Started
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Leaderboard Content -->
                    <div class="p-6">
                        @if ($leaderboard->isEmpty())
                            <div class="text-center py-8">
                                <x-gmdi-people class="mx-auto h-12 w-12 text-gray-400" />
                                <p class="mt-2 text-sm text-gray-600">No one has completed this content yet</p>
                            </div>
                        @else
                            <!-- Top Performers -->
                            <div class="space-y-3">
                                @foreach ($leaderboard as $performer)
                                    @php
                                        $rankColor = match ($performer->rank) {
                                            1 => 'bg-yellow-50 border-yellow-200',
                                            2 => 'bg-gray-50 border-gray-200',
                                            3 => 'bg-orange-50 border-orange-200',
                                            default => 'bg-white border-gray-100',
                                        };

                                        $rankIcon = match ($performer->rank) {
                                            1 => 'emoji-events',
                                            2 => 'workspace-premium',
                                            3 => 'military-tech',
                                            default => null,
                                        };

                                        $isCurrentUser = $performer->id === auth()->id();
                                    @endphp

                                    <div
                                        class="rounded-lg border {{ $rankColor }} {{ $isCurrentUser ? 'ring-2 ring-primary/20' : '' }} p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <!-- Rank -->
                                                <div class="flex items-center justify-center w-8 h-8">
                                                    @if ($rankIcon)
                                                        <x-dynamic-component :component="'gmdi-' . $rankIcon"
                                                            class="h-6 w-6 text-yellow-600" />
                                                    @else
                                                        <span
                                                            class="font-bold text-gray-600">#{{ $performer->rank }}</span>
                                                    @endif
                                                </div>

                                                <!-- User Info -->
                                                <div>
                                                    <h4
                                                        class="font-semibold text-gray-900 {{ $isCurrentUser ? 'text-primary' : '' }}">
                                                        {{ $performer->name }}
                                                        @if ($isCurrentUser)
                                                            <span class="text-xs text-primary font-normal">(You)</span>
                                                        @endif
                                                    </h4>
                                                    <div class="flex items-center space-x-3 text-sm text-gray-600">
                                                        @if ($performer->is_completed)
                                                            <span class="flex items-center text-green-600">
                                                                <x-gmdi-check-circle class="mr-1 h-3 w-3" />
                                                                Completed
                                                            </span>
                                                            @if ($performer->completion_time)
                                                                <span class="flex items-center">
                                                                    <x-gmdi-schedule class="mr-1 h-3 w-3" />
                                                                    {{ gmdate('i:s', $performer->completion_time) }}
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="text-gray-500">Not completed</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Performance -->
                                            <div class="text-right">
                                                @if ($isQuiz && $performer->is_completed)
                                                    <div class="text-lg font-bold text-gray-900">
                                                        {{ number_format($performer->score, 1) }}%</div>
                                                    <div class="text-sm text-gray-600">
                                                        {{ $performer->correct_answers ?? 0 }}/{{ $performer->total_questions ?? 0 }}
                                                        correct
                                                    </div>
                                                    @if ($performer->points_earned)
                                                        <div class="text-xs text-green-600 font-medium">
                                                            +{{ number_format($performer->points_earned, 0) }} pts
                                                        </div>
                                                    @endif
                                                @elseif(!$isQuiz && $performer->is_completed)
                                                    <div class="text-lg font-bold text-green-600">
                                                        +{{ $performer->points_earned ?? ($content->contentable->points ?? 10) }}
                                                        pts
                                                    </div>
                                                    <div class="text-sm text-gray-600">Completed</div>
                                                @else
                                                    <div class="text-gray-400">-</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if ($totalParticipants > 5)
                                    <div class="text-center pt-2">
                                        <span class="text-sm text-gray-500">
                                            Showing top 5 of {{ $totalParticipants }} students
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.user-layout>
