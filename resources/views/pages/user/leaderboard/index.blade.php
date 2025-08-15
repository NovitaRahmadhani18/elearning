<x-layouts.user-layout>
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
            <p class="mt-2 text-gray-600">
                You haven't enrolled in any classrooms yet, or your classrooms don't have any content.
            </p>
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

                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
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
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $content->contentable->title }}
                                    </h3>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <span>{{ $classroom->fullTitle }}</span>
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
                                        class="{{ $userRank <= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-700' }} inline-flex items-center rounded-full px-3 py-1 text-sm font-medium"
                                    >
                                        @if ($userRank == 1)
                                            <x-gmdi-emoji-events class="mr-1 h-4 w-4" />
                                        @elseif ($userRank <= 3)
                                            <x-gmdi-workspace-premium class="mr-1 h-4 w-4" />
                                        @else
                                            <x-gmdi-person class="mr-1 h-4 w-4" />
                                        @endif
                                        Your Rank: #{{ $userRank }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">of {{ $totalParticipants }} students</div>
                                </div>
                            @else
                                <div class="text-right">
                                    <div
                                        class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600"
                                    >
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
                            <div class="py-8 text-center">
                                <x-gmdi-people class="mx-auto h-12 w-12 text-gray-400" />
                                <p class="mt-2 text-sm text-gray-600">No one has completed this content yet</p>
                            </div>
                        @else
                            <!-- Top Performers -->
                            <div class="space-y-3">
                                @foreach ($leaderboard as $performer)
                                    @php
                                        $rankColor = match ($performer->rank) {
                                            1 => 'border-yellow-200 bg-yellow-50',
                                            2 => 'border-gray-200 bg-gray-50',
                                            3 => 'border-orange-200 bg-orange-50',
                                            default => 'border-gray-100 bg-white',
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
                                        class="{{ $rankColor }} {{ $isCurrentUser ? 'ring-2 ring-primary-dark' : '' }} rounded-lg border p-4"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <!-- Rank -->
                                                <div class="flex h-8 w-8 items-center justify-center">
                                                    @if ($rankIcon)
                                                        <x-dynamic-component
                                                            :component="'gmdi-' . $rankIcon"
                                                            class="h-6 w-6 text-yellow-600"
                                                        />
                                                    @else
                                                        <span class="font-bold text-gray-600">
                                                            #{{ $performer->rank }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- User Info -->
                                                <div>
                                                    <h4
                                                        class="{{ $isCurrentUser ? 'text-primary-dark' : '' }} font-semibold text-gray-900"
                                                    >
                                                        {{ $performer->name }}
                                                        @if ($isCurrentUser)
                                                            <span class="text-xs font-normal text-primary-dark">
                                                                (You)
                                                            </span>
                                                        @endif
                                                    </h4>
                                                    <div class="flex items-center space-x-3 text-sm text-gray-600">
                                                        @if ($performer->is_completed)
                                                            <span class="flex items-center text-green-600">
                                                                <x-gmdi-check-circle class="mr-1 h-3 w-3" />
                                                                Completed
                                                            </span>
                                                            @if ($performer->completion_time && $isQuiz)
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
                                                        {{ number_format($performer->score, 1) }}%
                                                    </div>
                                                    <div class="text-sm text-gray-600">
                                                        {{ $performer->correct_answers ?? 0 }}/{{ $performer->total_questions ?? 0 }}
                                                        correct
                                                    </div>
                                                    @if ($performer->points_earned)
                                                        <div class="text-xs font-medium text-green-600">
                                                            +{{ number_format($performer->points_earned, 0) }} pts
                                                        </div>
                                                    @endif
                                                @elseif (! $isQuiz && $performer->is_completed)
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
                                    <div class="pt-2 text-center">
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
