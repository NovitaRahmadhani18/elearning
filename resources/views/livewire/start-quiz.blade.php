<div class="min-h-screen w-full bg-primary p-2">
    @if (! $isCompleted)
        {{-- header --}}
        <div class="mb-8 bg-black/10 p-6 text-white backdrop-blur-sm lg:rounded-2xl">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="rounded-full bg-white/20 p-3">
                        <x-gmdi-library-books class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $quiz->title }}</h1>
                        <p class="text-white/80">{{ $classroom->title }}</p>
                    </div>
                </div>

                @if ($quiz->time_limit > 0)
                    <div class="rounded-lg bg-white/20 px-4 py-2">
                        <div class="flex items-center justify-center gap-2">
                            <x-gmdi-schedule class="h-5 w-5 text-white/80" />
                            <div class="text-xl font-bold" x-data="timer(500)" x-init="startTimer()">
                                <span x-text="formatTime(timeLeft)"></span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Progress Bar -->
            <div class="h-3 w-full rounded-full bg-white/20">
                @php
                    $progressPercentage = count($questions) > 0 ? round(($currentQuestionIndex / count($questions)) * 100) : 0;
                @endphp

                <div
                    class="h-3 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 transition-all duration-300 ease-out"
                    style="width: {{ $progressPercentage }}%"
                ></div>
            </div>
            <div class="mt-2 flex justify-between text-sm text-white/80">
                <span>Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}</span>
                <span>{{ $progressPercentage }}% Complete</span>
            </div>
        </div>

        {{-- quiz content --}}
        <div class="mb-8 text-center">
            <h2 class="mb-4 text-2xl font-bold text-white">{{ $currentQuestion['question_text'] }}</h2>

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
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:px-16">
            @foreach ($currentQuestion['options'] as $index => $option)
                <button
                    wire:click="selectAnswer({{ $option['id'] }})"
                    wire:loading.class="cursor-wait opacity-75"
                    wire:loading.attr="disabled"
                    @class([
                        'transform rounded-2xl border-2 p-6 text-left transition-all duration-300 hover:bg-purple-50 focus:outline-none focus:ring-4 focus:ring-purple-500/50',
                        'border-purple-400 bg-purple-100' => $selectedAnswer == $option['id'],
                        'border-gray-200 bg-gray-50 hover:border-purple-400 hover:bg-purple-50' => $selectedAnswer != $option['id'],
                    ])
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
        @push('scripts')
            <script>
                function timer(initialTime) {
                    return {
                        timeLeft: initialTime,
                        startTimer() {
                            setInterval(() => {
                                if (this.timeLeft > 0) {
                                    this.timeLeft--;
                                }
                            }, 1000);
                        },
                        formatTime(seconds) {
                            const minutes = Math.floor(seconds / 60);
                            const secs = seconds % 60;
                            return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                        },
                    };
                }
            </script>
        @endpush
    @else
        {{-- completed notice --}}
        <div class="flex items-center justify-center">
            <x-quiz.results-card
                score="0"
                :total-questions="count($questions)"
                :correct-answers="$correctAnswers"
                time-spent="0"
                performance-level="unknown"
                encouragement-message="unknown"
                :classroom="$classroom"
                :quiz="$quiz"
                :quiz-answers="$answers"
                :quiz-questions="$questions"
            />
        </div>
    @endif
</div>
