<x-layouts.index>
    <div class="flex min-h-screen flex-col bg-primary p-3 sm:p-6" x-data="quizReview()">
        <!-- Header -->
        <div
            class="mobile-header mb-4 flex items-center justify-between rounded-xl bg-white/10 p-3 backdrop-blur-sm sm:mb-6 sm:rounded-2xl sm:p-4"
        >
            <div class="flex items-center space-x-2 sm:space-x-4">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 sm:h-12 sm:w-12">
                    <x-gmdi-library-books class="h-6 w-6 text-white" />
                </div>
                <div class="min-w-0 flex-1 text-white">
                    <h1 class="truncate text-sm font-bold sm:text-xl">{{ $quiz->title }}</h1>
                    <p class="truncate text-xs text-white/80 sm:text-sm">{{ $classroom->name }} - Quiz Review</p>
                </div>
            </div>
            <div class="flex items-center space-x-2 sm:space-x-4">
                <div
                    class="rounded-full bg-white/20 px-2 py-1 text-xs font-medium text-white sm:px-4 sm:py-2 sm:text-sm"
                >
                    {{ $submission->score }}%
                </div>
                <a
                    href="{{ route("user.classroom.show", $classroom) }}"
                    class="rounded-full bg-white/20 px-2 py-1 text-xs font-medium text-white transition-colors hover:bg-white/30 sm:px-4 sm:py-2 sm:text-sm"
                >
                    <span class="hidden sm:inline">Back to Classroom</span>
                    <span class="sm:hidden">Back</span>
                </a>
            </div>
        </div>

        <!-- Quiz Review Content -->
        <div class="flex-1 overflow-hidden">
            <div class="mx-auto h-full max-w-4xl">
                <!-- Question Navigation -->
                <div
                    class="mb-4 flex items-center justify-between rounded-xl bg-white/10 p-3 backdrop-blur-sm sm:mb-6 sm:rounded-2xl sm:p-4"
                >
                    <button
                        @click="previousQuestion()"
                        :disabled="currentQuestion === 0"
                        class="flex items-center space-x-1 rounded-full bg-white/20 px-3 py-2 text-xs font-medium text-white transition-colors hover:bg-white/30 disabled:cursor-not-allowed disabled:opacity-50 sm:space-x-2 sm:px-4 sm:py-2 sm:text-sm"
                    >
                        <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            ></path>
                        </svg>
                        <span class="hidden sm:inline">Previous</span>
                    </button>

                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <span class="text-xs text-white/80 sm:text-sm">Question</span>
                        <span class="text-lg font-bold text-white sm:text-xl" x-text="currentQuestion + 1"></span>
                        <span class="text-xs text-white/80 sm:text-sm">of {{ $quiz->questions->count() }}</span>
                    </div>

                    <button
                        @click="nextQuestion()"
                        :disabled="currentQuestion === {{ $quiz->questions->count() - 1 }}"
                        class="flex items-center space-x-1 rounded-full bg-white/20 px-3 py-2 text-xs font-medium text-white transition-colors hover:bg-white/30 disabled:cursor-not-allowed disabled:opacity-50 sm:space-x-2 sm:px-4 sm:py-2 sm:text-sm"
                    >
                        <span class="hidden sm:inline">Next</span>
                        <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            ></path>
                        </svg>
                    </button>
                </div>

                <!-- Question Card -->
                <div
                    class="mobile-content custom-scrollbar h-full overflow-y-auto rounded-xl bg-white p-4 shadow-2xl sm:rounded-2xl sm:p-8"
                >
                    @foreach ($quiz->questions as $index => $question)
                        @php
                            $userAnswer = $userAnswers->where("question_id", $question->id)->first();
                            $selectedOption = $userAnswer ? $userAnswer->selectedOption : null;
                            $correctOption = $question->options->where("is_correct", true)->first();
                            $isCorrect = $selectedOption && $selectedOption->is_correct;
                        @endphp

                        <div x-show="currentQuestion === {{ $index }}" class="space-y-4 sm:space-y-6">
                            <!-- Question Header -->
                            <div class="text-center">
                                <h2 class="mb-3 text-lg font-bold leading-tight text-gray-800 sm:mb-4 sm:text-2xl">
                                    {{ $question->question_text }}
                                </h2>

                                @if ($question->image_path)
                                    <div class="mb-4 sm:mb-6">
                                        <img
                                            src="{{ Storage::url($question->image_path) }}"
                                            alt="Question Image"
                                            class="mx-auto max-h-48 max-w-full rounded-lg object-contain shadow-lg sm:max-h-64"
                                        />
                                    </div>
                                @endif
                            </div>

                            <!-- Result Badge -->
                            <div class="text-center">
                                <span
                                    class="{{ $isCorrect ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800" }} inline-flex items-center rounded-full px-4 py-2 text-sm font-bold sm:px-6 sm:py-3 sm:text-lg"
                                >
                                    @if ($isCorrect)
                                        <svg
                                            class="mr-1 h-4 w-4 sm:mr-2 sm:h-5 sm:w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 13l4 4L19 7"
                                            ></path>
                                        </svg>
                                        <span class="hidden sm:inline">Correct Answer!</span>
                                        <span class="sm:hidden">Correct!</span>
                                    @else
                                        <svg
                                            class="mr-1 h-4 w-4 sm:mr-2 sm:h-5 sm:w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            ></path>
                                        </svg>
                                        <span class="hidden sm:inline">Incorrect Answer</span>
                                        <span class="sm:hidden">Incorrect</span>
                                    @endif
                                </span>
                            </div>

                            <!-- Answer Options -->
                            <div class="grid grid-cols-1 gap-3 sm:gap-4">
                                @foreach ($question->options as $optionIndex => $option)
                                    <div
                                        class="@if ($option->is_correct)
                                            border-green-500
                                            bg-green-100
                                        @elseif ($selectedOption && $selectedOption->id == $option->id && ! $option->is_correct)
                                            border-red-500
                                            bg-red-100
                                        @else
                                            border-gray-200
                                            bg-gray-50
                                        @endif transform rounded-xl border-2 p-4 transition-all duration-300 sm:rounded-2xl sm:p-6"
                                    >
                                        <div class="flex items-start space-x-3 sm:space-x-4">
                                            <div
                                                class="@if ($option->is_correct)
                                                    bg-green-500
                                                    text-white
                                                @elseif ($selectedOption && $selectedOption->id == $option->id && ! $option->is_correct)
                                                    bg-red-500
                                                    text-white
                                                @else
                                                    bg-gray-300
                                                    text-gray-600
                                                @endif mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full text-sm font-bold sm:mt-0 sm:h-8 sm:w-8"
                                            >
                                                {{ chr(65 + $optionIndex) }}
                                            </div>
                                            <span class="flex-1 text-sm font-medium leading-relaxed sm:text-lg">
                                                {{ $option->option_text }}
                                            </span>

                                            <div class="flex-shrink-0">
                                                @if ($option->is_correct)
                                                    <svg
                                                        class="h-5 w-5 text-green-600 sm:h-6 sm:w-6"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M5 13l4 4L19 7"
                                                        ></path>
                                                    </svg>
                                                @elseif ($selectedOption && $selectedOption->id == $option->id && ! $option->is_correct)
                                                    <svg
                                                        class="h-5 w-5 text-red-600 sm:h-6 sm:w-6"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12"
                                                        ></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Question Indicators -->
        <div class="mt-4 flex justify-center sm:mt-6">
            <div
                class="flex max-w-full flex-wrap gap-1 overflow-hidden rounded-xl bg-white/10 p-3 backdrop-blur-sm sm:gap-2 sm:rounded-2xl sm:p-4"
            >
                @foreach ($quiz->questions as $index => $question)
                    @php
                        $userAnswer = $userAnswers->where("question_id", $question->id)->first();
                        $isCorrect = $userAnswer && $userAnswer->is_correct;
                    @endphp

                    <button
                        @click="goToQuestion({{ $index }})"
                        class="{{ $isCorrect ? "bg-green-500 text-white" : "bg-red-500 text-white" }} h-7 w-7 flex-shrink-0 rounded-full text-xs font-bold transition-all duration-300 sm:h-8 sm:w-8"
                        :class="currentQuestion === {{ $index }} ? 'ring-2 sm:ring-4 ring-white/50 scale-110' : ''"
                    >
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
    @push("scripts")
        <script>
            function quizReview() {
                return {
                    currentQuestion: 0,
                    totalQuestions: {{ $quiz->questions->count() }},

                    nextQuestion() {
                        if (this.currentQuestion < this.totalQuestions - 1) {
                            this.currentQuestion++;
                        }
                    },

                    previousQuestion() {
                        if (this.currentQuestion > 0) {
                            this.currentQuestion--;
                        }
                    },

                    goToQuestion(index) {
                        this.currentQuestion = index;
                    },

                    // Mobile swipe support
                    init() {
                        let startX = 0;
                        let startY = 0;
                        let endX = 0;
                        let endY = 0;

                        const questionCard = document.querySelector('.custom-scrollbar');

                        questionCard.addEventListener('touchstart', (e) => {
                            startX = e.touches[0].clientX;
                            startY = e.touches[0].clientY;
                        });

                        questionCard.addEventListener('touchend', (e) => {
                            endX = e.changedTouches[0].clientX;
                            endY = e.changedTouches[0].clientY;

                            const deltaX = endX - startX;
                            const deltaY = endY - startY;

                            // Only trigger swipe if horizontal movement is greater than vertical
                            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
                                if (deltaX > 0) {
                                    // Swipe right - go to previous question
                                    this.previousQuestion();
                                } else {
                                    // Swipe left - go to next question
                                    this.nextQuestion();
                                }
                            }
                        });

                        // Keyboard navigation
                        document.addEventListener('keydown', (e) => {
                            if (e.key === 'ArrowLeft') {
                                this.previousQuestion();
                            } else if (e.key === 'ArrowRight') {
                                this.nextQuestion();
                            }
                        });
                    },
                };
            }

            // Disable right-click
            document.addEventListener('contextmenu', function (e) {
                e.preventDefault();
            });

            // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
            document.addEventListener('keydown', function (e) {
                if (
                    e.key === 'F12' ||
                    (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) ||
                    (e.ctrlKey && e.key === 'U')
                ) {
                    e.preventDefault();
                }
            });
        </script>
    @endpush
</x-layouts.index>
