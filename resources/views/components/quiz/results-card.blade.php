@props([
    'score',
    'totalQuestions',
    'correctAnswers',
    'timeSpent',
    'performanceLevel',
    'encouragementMessage',
    'classroom',
    'quiz',
    'quizAnswers',
    'quizQuestions',
])

<div class="min-h-screen w-full max-w-2xl transform rounded-lg bg-slate-900 p-6 transition-all duration-300 shadow-2xl">
    <div class="text-center">
        <!-- Performance Badge -->
        @php
            $badgeColor = match (true) {
                $score >= 90 => 'from-green-400 to-emerald-500',
                $score >= 80 => 'from-blue-400 to-cyan-500',
                $score >= 70 => 'from-yellow-400 to-orange-500',
                $score >= 60 => 'from-orange-400 to-red-500',
                default => 'from-gray-400 to-gray-600',
            };
        @endphp

        <div
            class="mb-6 inline-flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-r {{ $badgeColor }} text-4xl text-white shadow-lg">
            <x-gmdi-emoji-events class="h-12 w-12" />
        </div>

        <!-- Results Title -->
        <h2 class="mb-2 text-4xl font-bold text-white">Quiz Completed!</h2>
        <p class="mb-6 text-lg text-blue-200">{{ $performanceLevel }}</p>

        <!-- User Info Card -->
        <div class="mb-8 rounded-xl bg-slate-800/50 p-4 border border-slate-700">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-600 text-center text-2xl font-bold text-white ring-2 ring-purple-400/50">
                    {{ auth()->user()->initials() }}
                </div>
                <div class="flex-1 text-left">
                    <h3 class="font-semibold text-white">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-slate-300">{{ $classroom->title }} • {{ $quiz->title }}</p>
                </div>
            </div>
        </div>

        <!-- Score Display -->
        <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
            <!-- Score Card -->
            @php
                $scoreCardColor = match (true) {
                    $score >= 90 => 'bg-green-600',
                    $score >= 80 => 'bg-blue-600',
                    $score >= 70 => 'bg-yellow-600',
                    $score >= 60 => 'bg-orange-600',
                    default => 'bg-gray-600',
                };
            @endphp
            <div class="rounded-xl {{ $scoreCardColor }} p-6 text-white shadow-lg">
                <x-gmdi-star class="mx-auto mb-3 h-8 w-8" />
                <div class="text-3xl font-bold">{{ number_format($score, 1) }}%</div>
                <div class="text-sm opacity-90">Final Score</div>
            </div>

            <!-- Correct Answers Card -->
            <div class="rounded-xl bg-green-600 p-6 text-white shadow-lg">
                <x-gmdi-check-circle class="mx-auto mb-3 h-8 w-8" />
                <div class="text-3xl font-bold">{{ $correctAnswers }}/{{ $totalQuestions }}</div>
                <div class="text-sm opacity-90">Correct Answers</div>
            </div>

            <!-- Time Spent Card -->
            <div class="rounded-xl bg-indigo-600 p-6 text-white shadow-lg">
                <x-gmdi-schedule class="mx-auto mb-3 h-8 w-8" />
                <div class="text-3xl font-bold">{{ $timeSpent }}</div>
                <div class="text-sm opacity-90">Time Spent</div>
            </div>
        </div>

        <!-- Performance Breakdown -->
        <div class="mb-8 rounded-xl bg-slate-800/50 p-6 border border-slate-700">
            <h3 class="mb-4 text-xl font-semibold text-white">Performance Breakdown</h3>

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="flex justify-between text-sm text-slate-300 mb-2">
                    <span>Accuracy</span>
                    <span>{{ number_format($score, 1) }}%</span>
                </div>
                <div class="h-3 w-full rounded-full bg-slate-700">
                    <div class="h-3 rounded-full bg-gradient-to-r {{ $badgeColor }} transition-all duration-500"
                        style="width: {{ $score }}%"></div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="rounded-lg bg-slate-700/50 p-3">
                    <div class="text-2xl font-bold text-white">{{ $totalQuestions - $correctAnswers }}</div>
                    <div class="text-sm text-slate-300">Incorrect</div>
                </div>
                <div class="rounded-lg bg-slate-700/50 p-3">
                    <div class="text-2xl font-bold text-white">
                        {{ number_format(($correctAnswers / $totalQuestions) * 100, 1) }}%</div>
                    <div class="text-sm text-slate-300">Accuracy</div>
                </div>
            </div>
        </div>

        <!-- Encouragement Message -->
        <div class="mb-8 rounded-xl bg-blue-600/20 p-6 border border-blue-500/30">
            <x-gmdi-lightbulb class="mx-auto mb-3 h-8 w-8 text-yellow-400" />
            <p class="text-blue-100 text-lg leading-relaxed">{{ $encouragementMessage }}</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col gap-3">
            <a href="{{ route('user.classroom.quiz.review', [$classroom, $quiz]) }}"
                class="inline-flex items-center justify-center rounded-xl bg-secondary px-6 py-3 text-center font-semibold text-white transition-all duration-300 hover:bg-secondary/90 hover:shadow-lg transform hover:scale-105">
                <x-gmdi-visibility class="mr-2 h-5 w-5" />
                Review Answers
            </a>

            <a href="{{ route('user.classroom.show', $classroom->id) }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-700 px-6 py-3 text-center font-semibold text-white transition-all duration-300 hover:bg-slate-600 border border-slate-600">
                <x-gmdi-arrow-back class="mr-2 h-5 w-5" />
                Back to Classroom
            </a>
        </div>
    </div>
</div>
