@props([
    'quiz',
    'classroom',
    'score',
    'correctAnswers',
    'totalQuestions',
    'timeSpent',
    'performanceLevel',
    'completedAt',
])

<div class="flex items-center justify-center">
    <div class="mx-auto transform rounded-3xl bg-white p-8 shadow-2xl">
        <div class="mb-8 text-center">
            <div
                class="mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-purple-500 text-2xl font-bold text-white"
            >
                <x-gmdi-check-circle class="h-10 w-10" />
            </div>
            <h2 class="mb-2 text-3xl font-bold text-gray-800">Quiz Already Completed</h2>
            <p class="text-lg text-gray-600">You have already completed this quiz.</p>
        </div>

        <!-- Quiz Info -->
        <div class="mb-8 text-center">
            <h3 class="mb-2 text-xl font-semibold text-gray-700">{{ $quiz->title }}</h3>
            <p class="text-gray-500">{{ $classroom->title }}</p>
        </div>

        <!-- Previous Score Display -->
        <div class="mb-8 text-center">
            <div
                class="mb-4 bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-6xl font-bold text-transparent"
            >
                {{ $score }}%
            </div>
            <div class="text-xl text-gray-600">{{ $correctAnswers }} out of {{ $totalQuestions }} correct</div>
        </div>

        <!-- Performance Stats -->
        <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-gradient-to-r from-blue-50 to-purple-50 p-6 text-center">
                <div class="mb-2 flex items-center justify-center">
                    <x-gmdi-star class="h-8 w-8 text-yellow-500" />
                </div>
                <div class="text-2xl font-bold text-gray-800">{{ $score }}%</div>
                <div class="text-sm text-gray-600">Score</div>
            </div>
            <div class="rounded-2xl bg-gradient-to-r from-green-50 to-blue-50 p-6 text-center">
                <div class="mb-2 flex items-center justify-center">
                    <x-gmdi-schedule class="h-8 w-8 text-blue-500" />
                </div>
                <div class="text-2xl font-bold text-gray-800">{{ $timeSpent }}</div>
                <div class="text-sm text-gray-600">Time Spent</div>
            </div>
            <div class="rounded-2xl bg-gradient-to-r from-purple-50 to-pink-50 p-6 text-center">
                <div class="mb-2 flex items-center justify-center">
                    @if ($performanceLevel === 'excellent')
                        <x-gmdi-emoji-events class="h-8 w-8 text-yellow-500" />
                    @elseif ($performanceLevel === 'good')
                        <x-gmdi-thumb-up class="h-8 w-8 text-green-500" />
                    @elseif ($performanceLevel === 'average')
                        <x-gmdi-trending-up class="h-8 w-8 text-orange-500" />
                    @else
                        <x-gmdi-school class="h-8 w-8 text-red-500" />
                    @endif
                </div>
                <div class="text-2xl font-bold capitalize text-gray-800">
                    {{ str_replace('_', ' ', $performanceLevel) }}
                </div>
                <div class="text-sm text-gray-600">Performance</div>
            </div>
        </div>

        <!-- Completion Date -->
        <div class="mb-8 text-center">
            <div class="text-sm text-gray-500">Completed on {{ $completedAt->format('l, F j, Y \a\t g:i A') }}</div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
            <button
                wire:click="retakeQuiz"
                class="transform rounded-full bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-3 font-semibold text-white transition-all duration-300 hover:scale-105 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-4 focus:ring-purple-500/50"
            >
                <x-gmdi-refresh class="mr-2 inline h-5 w-5" />
                Retake Quiz
            </button>
            <button
                wire:click="backToClassroom"
                class="transform rounded-full border-2 border-gray-300 bg-white px-8 py-3 font-semibold text-gray-700 transition-all duration-300 hover:scale-105 hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-gray-500/50"
            >
                <x-gmdi-arrow-back class="mr-2 inline h-5 w-5" />
                Back to Classroom
            </button>
        </div>

        <!-- Warning Message -->
        <div class="mt-6 rounded-lg bg-yellow-50 p-4">
            <div class="flex items-start">
                <x-gmdi-warning class="mr-3 mt-0.5 h-5 w-5 text-yellow-600" />
                <div class="text-sm text-yellow-700">
                    <strong>Note:</strong>
                    If you retake this quiz, your previous score and answers will be permanently deleted and replaced
                    with your new attempt.
                </div>
            </div>
        </div>
    </div>
</div>
