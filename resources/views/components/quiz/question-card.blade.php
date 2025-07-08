@props([
    'currentQuestion',
    'questionNumber',
    'selectedAnswer',
    'showFeedback',
    'isCorrect',
    'isAnswerSelected',
    'currentQuestionIndex',
    'totalQuestions',
])

<div class="transform rounded-3xl bg-white p-8 shadow-2xl transition-all duration-300">
    <div class="mb-8 text-center">
        <div
            class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-purple-500 to-blue-500 text-xl font-bold text-white"
        >
            {{ $questionNumber }}
        </div>
        <h2 class="mb-4 text-2xl font-bold text-gray-800">{{ $currentQuestion['question_text'] }}</h2>

        @if ($currentQuestion['image_path'])
            <div class="mb-6">
                <img
                    src="{{ Storage::url($currentQuestion['image_path']) }}"
                    alt="Question Image"
                    class="mx-auto h-auto max-h-64 max-w-full rounded-lg object-contain shadow-lg"
                />
            </div>
        @endif
    </div>

    <!-- Answer Options -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @foreach ($currentQuestion['options'] as $index => $option)
            <button
                wire:click="selectAnswer({{ $option['id'] }})"
                wire:loading.class="cursor-wait opacity-75"
                @class([
                    'transform rounded-2xl border-2 p-6 text-left transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-purple-500/50',
                    'border-purple-400 bg-purple-100' => $selectedAnswer == $option['id'],
                    'border-gray-200 bg-gray-50 hover:border-purple-400 hover:bg-purple-50' => $selectedAnswer != $option['id'],
                ])
                @disabled($isAnswerSelected)
            >
                <div class="flex items-center space-x-4">
                    <div
                        @class([
                            'flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold',
                            'bg-purple-500 text-white' => $selectedAnswer == $option['id'],
                            'bg-purple-100 text-purple-600' => $selectedAnswer != $option['id'],
                        ])
                    >
                        {{ chr(65 + $index) }}
                    </div>
                    <span class="flex-1 text-lg font-medium">{{ $option['option_text'] }}</span>
                </div>
            </button>
        @endforeach
    </div>
</div>
