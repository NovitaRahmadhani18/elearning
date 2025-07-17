<x-layouts.user-layout>
    <x-slot name="header"></x-slot>

    <div class="mx-auto w-full max-w-7xl rounded-md border border-primary/20 bg-white px-4 py-6 sm:px-6 lg:px-8" x-data
        @pageshow.window="if ($event.persisted) window.location.reload()">
        <!-- Classroom Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $classroom->title }}</h1>

                    @if ($classroom->category)
                        <span
                            class="inline-flex items-center rounded-full bg-secondary/20 px-3 py-1 text-sm font-medium text-secondary-dark">
                            {{ $classroom->category }}
                        </span>
                    @endif

                    @if ($classroom->description)
                        <p class="mt-1 text-gray-600">{{ $classroom->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Progress Overview -->
            <div class="mt-4 flex items-center space-x-6">
                <div class="flex items-center text-sm text-gray-600">
                    <x-gmdi-assignment class="mr-1 h-4 w-4" />
                    <span>{{ $classroom->contents->count() }} contents</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <x-gmdi-check-circle class="mr-1 h-4 w-4 text-green-600" />
                    <span>{{ count($completedContents) }} completed</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <x-gmdi-person class="mr-1 h-4 w-4" />
                    <span>{{ $classroom->teacher->name }}</span>
                </div>
            </div>

            <!-- Progress Bar -->
            @if ($classroom->contents->count() > 0)
                @php
                    $progressPercentage = (count($completedContents) / $classroom->contents->count()) * 100;
                @endphp

                <div class="mt-4">
                    <div class="mb-2 flex items-center justify-between text-sm text-gray-600">
                        <span>Learning Progress</span>
                        <span>{{ round($progressPercentage) }}%</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-200">
                        <div class="h-2 rounded-full bg-gradient-to-r from-secondary to-secondary-dark transition-all duration-500"
                            style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Content Section -->
        <div class="mb-6">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">Course Content</h2>
            <p class="mb-6 text-sm text-gray-600">
                Complete the materials and quizzes in order to progress through the course.
            </p>
        </div>

        @php
            $previousContentId = null;
            $isLocked = false;
        @endphp

        <div class="space-y-6">
            @forelse ($classroom->contents as $item)
                @php
                    // Konten pertama tidak pernah terkunci
                    if ($loop->first) {
                        $isLocked = false;
                    } else {
                        // Konten terkunci jika konten sebelumnya belum selesai
                        $isLocked = !in_array($previousContentId, $completedContents);
                    }

                    // Check if content is completed
                    $isCompleted = in_array($item->id, $completedContents);
                @endphp

                @if ($item->contentable instanceof \App\Models\Material)
                    <!-- Material Card -->
                    <div
                        class="{{ $isLocked ? 'opacity-60' : 'hover:border-secondary/30' }} group relative overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-md">
                        <!-- Status Badge -->
                        @if ($isCompleted)
                            <div class="absolute right-3 top-3 z-10">
                                <span
                                    class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                    <x-gmdi-check-circle class="mr-1 h-3 w-3" />
                                    Completed
                                </span>
                            </div>
                        @elseif ($isLocked)
                            <div class="absolute right-3 top-3 z-10">
                                <span
                                    class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                    <x-gmdi-lock class="mr-1 h-3 w-3" />
                                    Locked
                                </span>
                            </div>
                        @endif

                        <!-- Material Header -->
                        <div class="relative h-24 overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100">
                            <div class="bg-material-pattern absolute inset-0 opacity-10"></div>
                            <div class="relative flex h-full items-center justify-center">
                                <div class="text-center">
                                    <x-gmdi-menu-book class="mx-auto mb-1 h-8 w-8 text-blue-600" />
                                    <div class="text-xs font-medium text-blue-700">Material</div>
                                </div>
                            </div>
                        </div>

                        <!-- Material Content -->
                        <div class="p-4">
                            <h3 class="mb-2 font-semibold text-gray-800 transition-colors group-hover:text-secondary">
                                {{ $item->contentable->title }}
                            </h3>

                            <!-- Points Display -->
                            <div class="mb-3 flex items-center text-sm text-gray-600">
                                <x-gmdi-stars class="mr-1 h-4 w-4 text-secondary" />
                                <span>{{ $item->contentable->points ?? 10 }} points</span>
                            </div>

                            <!-- Action Button -->
                            <div class="flex items-center justify-between">
                                <div class="text-xs text-gray-500">
                                    @if ($isCompleted)
                                        <span class="flex items-center text-green-600">
                                            <x-gmdi-check class="mr-1 h-3 w-3" />
                                            Viewed
                                        </span>
                                    @else
                                        <span>Click to view</span>
                                    @endif
                                </div>

                                <a href="{{ $isLocked ? '#' : route('user.classroom.material.show', [$classroom->id, $item->contentable->id]) }}"
                                    @class([
                                        'inline-flex items-center rounded-md px-4 py-2 text-sm font-medium transition-all duration-200',
                                        'bg-secondary text-white shadow-sm hover:bg-secondary-dark hover:shadow-md' => !$isLocked,
                                        'cursor-not-allowed bg-gray-300 text-gray-500' => $isLocked,
                                    ])>
                                    @if ($isCompleted)
                                        <x-gmdi-visibility class="mr-1 h-4 w-4" />
                                        View Again
                                    @else
                                        <x-gmdi-play-arrow class="mr-1 h-4 w-4" />
                                        Start Reading
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif ($item->contentable instanceof \App\Models\Quiz)
                    <!-- Quiz Card -->
                    @php
                        $quiz = $item->contentable;
                        $isExpired = $quiz->due_time && $quiz->due_time->isPast();
                        $hasSubmission = $quiz
                            ->submissions()
                            ->where('user_id', auth()->id())
                            ->exists();
                    @endphp
                    <div
                        class="{{ $isLocked ? 'opacity-60' : 'hover:border-secondary/30' }} group relative overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-md">
                        <!-- Status Badge -->
                        @if ($isCompleted)
                            @if ($isExpired && $hasSubmission)
                                <div class="absolute right-3 top-3 z-10">
                                    <span
                                        class="inline-flex items-center rounded-full bg-orange-100 px-2 py-1 text-xs font-medium text-orange-800">
                                        <x-gmdi-schedule class="mr-1 h-3 w-3" />
                                        Expired
                                    </span>
                                </div>
                            @elseif ($isExpired)
                                <div class="absolute right-3 top-3 z-10">
                                    <span
                                        class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">
                                        <x-gmdi-warning class="mr-1 h-3 w-3" />
                                        Missed
                                    </span>
                                </div>
                            @else
                                <div class="absolute right-3 top-3 z-10">
                                    <span
                                        class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                        <x-gmdi-check-circle class="mr-1 h-3 w-3" />
                                        Completed
                                    </span>
                                </div>
                            @endif
                        @elseif ($isLocked)
                            <div class="absolute right-3 top-3 z-10">
                                <span
                                    class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                    <x-gmdi-lock class="mr-1 h-3 w-3" />
                                    Locked
                                </span>
                            </div>
                        @elseif ($isExpired)
                            <div class="absolute right-3 top-3 z-10">
                                <span
                                    class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">
                                    <x-gmdi-schedule class="mr-1 h-3 w-3" />
                                    Expired
                                </span>
                            </div>
                        @endif

                        <!-- Quiz Header -->
                        <div class="relative h-24 overflow-hidden bg-gradient-to-br from-purple-50 to-purple-100">
                            <div class="bg-quiz-pattern absolute inset-0 opacity-10"></div>
                            <div class="relative flex h-full items-center justify-center">
                                <div class="text-center">
                                    <x-gmdi-quiz class="mx-auto mb-1 h-8 w-8 text-purple-600" />
                                    <div class="text-xs font-medium text-purple-700">Quiz</div>
                                </div>
                            </div>
                        </div>

                        <!-- Quiz Content -->
                        <div class="p-4">
                            <h3 class="mb-2 font-semibold text-gray-800 transition-colors group-hover:text-secondary">
                                {{ $item->contentable->title }}
                            </h3>

                            <!-- Quiz Info -->
                            <div class="mb-3 space-y-1">
                                <div class="flex items-center text-sm text-gray-600">
                                    <x-gmdi-schedule class="mr-1 h-4 w-4" />
                                    <span class="font-medium">Opens:</span>
                                    <span class="ml-1">{{ $item->contentable->formatted_start_time }}</span>
                                </div>
                                <div
                                    class="flex items-center text-sm {{ $isExpired ? 'text-red-600' : 'text-gray-600' }}">
                                    <x-gmdi-event class="mr-1 h-4 w-4" />
                                    <span class="font-medium">Due:</span>
                                    <span class="ml-1">{{ $item->contentable->formatted_due_time }}</span>
                                    @if ($isExpired)
                                        <span
                                            class="ml-2 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">
                                            Expired
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <x-gmdi-help class="mr-1 h-4 w-4" />
                                    <span>{{ $item->contentable->questions->count() }} questions</span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="flex items-center justify-between">
                                <div class="text-xs text-gray-500">
                                    @if ($item->contentable->hasUserSubmitted(auth()->user()->id))
                                        <span class="flex items-center text-green-600">
                                            <x-gmdi-assignment-turned-in class="mr-1 h-3 w-3" />
                                            Submitted
                                        </span>
                                    @else
                                        <span>Ready to start</span>
                                    @endif
                                </div>

                                @if ($item->contentable->hasUserSubmitted(auth()->user()->id))
                                    <a href="{{ $isLocked ? '#' : route('user.classroom.quiz.start', [$classroom->id, $item->contentable->id]) }}"
                                        @class([
                                            'inline-flex items-center rounded-md px-4 py-2 text-sm font-medium transition-all duration-200',
                                            'bg-green-600 text-white shadow-sm hover:bg-green-700 hover:shadow-md' => !$isLocked,
                                            'cursor-not-allowed bg-gray-300 text-gray-500' => $isLocked,
                                        ])>
                                        <x-gmdi-visibility class="mr-1 h-4 w-4" />
                                        View Results
                                    </a>
                                @else
                                    <a href="{{ $isLocked ? '#' : route('user.classroom.quiz.show', [$classroom->id, $item->contentable->id]) }}"
                                        @class([
                                            'inline-flex items-center rounded-md px-4 py-2 text-sm font-medium transition-all duration-200',
                                            'bg-purple-600 text-white shadow-sm hover:bg-purple-700 hover:shadow-md' => !$isLocked,
                                            'cursor-not-allowed bg-gray-300 text-gray-500' => $isLocked,
                                        ])>
                                        <x-gmdi-play-arrow class="mr-1 h-4 w-4" />
                                        Start Quiz
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                @php
                    // Simpan ID konten saat ini untuk pengecekan di iterasi berikutnya
                    $previousContentId = $item->id;
                @endphp
            @empty
                <div class="rounded-lg border border-gray-200 bg-white p-12 text-center">
                    <x-gmdi-folder-open class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                    <h3 class="mb-2 text-lg font-medium text-gray-900">No Content Available</h3>
                    <p class="text-gray-500">This classroom doesn't have any materials or quizzes yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.user-layout>
