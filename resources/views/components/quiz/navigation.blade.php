@props(['currentQuestionIndex', 'totalQuestions', 'canNavigateBack', 'canNavigateForward', 'isAnswerSelected'])

<div class="flex items-center justify-between rounded-2xl bg-white/10 p-4 text-white backdrop-blur-sm">
    <button
        wire:click="previousQuestion"
        @class([
            'flex items-center space-x-2 rounded-lg px-4 py-2 transition-all duration-300',
            'cursor-not-allowed opacity-50' => !$canNavigateBack,
            'hover:bg-white/20' => $canNavigateBack,
        ])
        @disabled(!$canNavigateBack)
    >
        <x-gmdi-arrow-back class="h-5 w-5" />
        <span>Previous</span>
    </button>

    <div class="flex space-x-2">
        @for ($i = 0; $i < $totalQuestions; $i++)
            <div
                @class([
                    'h-3 w-3 rounded-full transition-all duration-300',
                    'bg-white' => $i === $currentQuestionIndex,
                    'bg-white/40' => $i < $currentQuestionIndex,
                    'bg-white/20' => $i > $currentQuestionIndex,
                ])
            ></div>
        @endfor
    </div>

    <button
        wire:click="nextQuestion"
        @class([
            'flex items-center space-x-2 rounded-lg px-4 py-2 transition-all duration-300',
            'cursor-not-allowed opacity-50' => !$canNavigateForward || !$isAnswerSelected,
            'hover:bg-white/20' => $canNavigateForward && $isAnswerSelected,
        ])
        @disabled(!$canNavigateForward || !$isAnswerSelected)
    >
        <span>Next</span>
        <x-gmdi-arrow-forward class="h-5 w-5" />
    </button>
</div> 