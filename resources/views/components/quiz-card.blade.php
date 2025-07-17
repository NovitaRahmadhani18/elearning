@props([
    'quiz',
])

<div
    class="group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:border-secondary/30 hover:shadow-md"
    x-init
    id="quiz-{{ $quiz->id }}"
>
    <!-- Quiz Header -->
    <div class="relative h-32 overflow-hidden bg-gradient-to-br from-secondary/10 to-secondary/30">
        <div class="bg-quiz-pattern absolute inset-0 opacity-5"></div>
        <div class="relative flex h-full items-center justify-center p-4">
            <div class="text-center">
                <x-gmdi-assignment class="mx-auto mb-2 h-12 w-12 text-secondary/70" />
                <div class="text-xs font-medium text-secondary-dark">{{ $quiz->questions->count() }} Questions</div>
            </div>
        </div>

        <!-- Quiz Type Badge -->
        <div class="absolute right-3 top-3">
            <span
                class="inline-flex items-center rounded-full bg-secondary/20 px-2 py-1 text-xs font-medium text-secondary-dark"
            >
                <x-gmdi-assignment class="mr-1 h-3 w-3" />
                Quiz
            </span>
        </div>

        <!-- Status Badge -->
        <div class="absolute left-3 top-3">
            @if ($quiz->due_time && $quiz->due_time->isPast())
                <span
                    class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800"
                >
                    <x-gmdi-schedule class="mr-1 h-3 w-3" />
                    Expired
                </span>
            @elseif ($quiz->start_time && $quiz->start_time->isFuture())
                <span
                    class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800"
                >
                    <x-gmdi-schedule class="mr-1 h-3 w-3" />
                    Scheduled
                </span>
            @else
                <span
                    class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800"
                >
                    <x-gmdi-play-circle class="mr-1 h-3 w-3" />
                    Active
                </span>
            @endif
        </div>
    </div>

    <!-- Quiz Content -->
    <div class="flex flex-1 flex-col p-4">
        <!-- Title -->
        <a
            href="{{ route('teacher.quizes.show', $quiz->id) }}"
            class="mb-2 block font-semibold text-gray-800 transition-colors hover:text-secondary"
        >
            {{ Str::limit($quiz->title, 50) }}
        </a>

        <!-- Classroom Info -->
        @if ($quiz->classroom)
            <div class="mb-3 flex items-center justify-between">
                <div class="flex items-center text-sm text-gray-600">
                    <x-gmdi-class class="mr-1 h-4 w-4" />
                    <span>{{ $quiz->classroom->title }}</span>
                </div>
                @if ($quiz->classroom->category)
                    <span
                        class="inline-flex items-center rounded-full bg-secondary/20 px-2 py-1 text-xs font-medium text-secondary-dark"
                    >
                        {{ $quiz->classroom->category }}
                    </span>
                @endif
            </div>
        @endif

        <!-- Description Preview -->
        @if ($quiz->description)
            <p
                class="mb-3 overflow-hidden text-sm text-gray-500"
                style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical"
            >
                {{ Str::limit(strip_tags($quiz->description), 80) }}
            </p>
        @endif

        <!-- Quiz Stats -->
        <div class="mb-3 flex items-center justify-between text-xs text-gray-500">
            <div class="flex items-center">
                <x-gmdi-access-time class="mr-1 h-3 w-3" />
                <span>{{ $quiz->time_limit ? $quiz->time_limit . ' min' : 'No limit' }}</span>
            </div>
            <div class="flex items-center">
                <x-gmdi-grade class="mr-1 h-3 w-3" />
                <span>{{ $quiz->point ?? 100 }} points</span>
            </div>
        </div>

        <!-- Due Time -->
        @if ($quiz->due_time)
            <div class="mb-3 flex items-center text-xs text-gray-500">
                <x-gmdi-schedule class="mr-1 h-3 w-3" />
                <span>Due {{ $quiz->due_time->diffForHumans() }}</span>
            </div>
        @endif

        <!-- Footer -->
        <div class="mt-auto flex items-center justify-between pt-2">
            <!-- Submissions Count -->
            <div class="flex items-center text-xs text-gray-500">
                <x-gmdi-group class="mr-1 h-3 w-3" />
                <span>{{ $quiz->submissions->count() }} submissions</span>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-1">
                <!-- Edit Button -->
                <a
                    href="{{ route('teacher.quizes.edit', $quiz->id) }}"
                    class="flex items-center justify-center rounded-lg bg-secondary/20 px-3 py-1.5 text-sm font-medium text-secondary-dark transition-colors hover:bg-secondary/30 hover:text-secondary-dark"
                    title="Edit Quiz"
                >
                    <x-gmdi-edit class="mr-1 h-4 w-4" />
                    <span>Edit</span>
                </a>

                <!-- Delete Button -->
                <form
                    action="{{ route('teacher.quizes.destroy', $quiz->id) }}"
                    method="POST"
                    x-init
                    x-target='quiz-{{ $quiz->id }}'
                    class="inline-block"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="flex items-center justify-center rounded-lg bg-red-50 px-3 py-1.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-100 hover:text-red-700"
                        title="Delete Quiz"
                        onclick="return confirm('Are you sure you want to delete this quiz?')"
                    >
                        <x-gmdi-delete class="mr-1 h-4 w-4" />
                        <span>Delete</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
