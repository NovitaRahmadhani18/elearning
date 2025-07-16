@props([
    'score',
    'totalQuestions',
    'correctAnswers',
    'timeSpent',
    'performanceLevel',
    'classroom',
    'quiz',
    'quizAnswers',
    'quizQuestions',
])

<div
    class="min-h-screen w-full max-w-xl transform rounded-md bg-black/20 p-4 backdrop-blur-sm transition-all duration-300"
>
    <div class="text-center">
        <!-- Trophy Icon -->
        <div
            class="mb-6 inline-flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 text-4xl text-white"
        >
            <x-gmdi-emoji-events class="h-12 w-12" />
        </div>

        <!-- Results Title -->
        <h2 class="mb-4 text-4xl font-bold text-white">Quiz Completed!</h2>

        <div class="mb-8 flex w-full gap-4 rounded-md bg-black/40 p-4 text-white">
            <div>
                <span
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/80 text-center text-2xl font-bold text-white ring-2 ring-white"
                >
                    {{ auth()->user()->initials() }}
                </span>
            </div>
            <div class="text-left">
                <h1>
                    {{ auth()->user()->name }}
                </h1>
                <p class="text-sm text-white/80">{{ $classroom->title }} - {{ $quiz->title }}</p>
            </div>
        </div>

        <!-- Score Display -->
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-2xl bg-primary/40 p-6 text-white">
                <x-gmdi-star class="mx-auto mb-3 h-8 w-8" />
                <div class="text-3xl font-bold">{{ $score }}%</div>
                <div class="text-sm">Final Score</div>
            </div>
            <div class="rounded-2xl bg-primary/40 p-6 text-white">
                <x-gmdi-thumb-up class="mx-auto mb-3 h-8 w-8" />
                <div class="text-3xl font-bold">{{ $correctAnswers }}/{{ $totalQuestions }}</div>
                <div class="text-sm">Correct Answers</div>
            </div>
            <div class="rounded-2xl bg-primary/40 p-6 text-white">
                <x-gmdi-schedule class="mx-auto mb-3 h-8 w-8" />
                <div class="text-3xl font-bold">{{ $timeSpent }}</div>
                <div class="text-sm">Time Spent</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col justify-center gap-2">
            <a
                href="{{ route('user.classroom.quiz.review', [$classroom, $quiz]) }}"
                class="rounded-lg bg-secondary px-6 py-3 text-center"
            >
                Review Answers
            </a>
            <a
                href="{{ route('user.classroom.show', $classroom->id) }}"
                class="rounded-lg bg-white px-6 py-3 text-center transition-colors"
            >
                Back to Classroom
            </a>
        </div>
    </div>
</div>
