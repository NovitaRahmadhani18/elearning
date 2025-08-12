<div class="min-h-screen w-full bg-slate-900 p-2"
    @if (!$isCompleted) x-data="quizHandler()" x-init="init()" @endif>
    @if (!$isCompleted)
        {{-- header --}}
        <div class="mb-8 border border-slate-700 bg-slate-800/50 p-6 text-white lg:rounded-2xl">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="rounded-full bg-slate-600/50 p-3">
                        <x-gmdi-library-books class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $quiz->title }}</h1>
                        <p class="text-white/80">{{ $classroom->title }}</p>
                    </div>
                </div>

                @if ($quiz->time_limit > 0)
                    <div class="timer-container rounded-lg px-4 py-2 transition-all duration-300"
                        :class="{
                            'bg-red-500/90': timeRemaining <= 60,
                            'bg-yellow-500/90': timeRemaining > 60 && timeRemaining <= 300,
                            'bg-slate-700/40': timeRemaining > 300
                        }">
                        <div class="flex items-center justify-center gap-2">
                            <x-gmdi-schedule class="h-5 w-5 text-white" />
                            <div class="text-xl font-bold text-white">
                                <span x-text="formatTime(timeRemaining)"></span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Progress Bar -->
            <div class="h-3 w-full rounded-full bg-slate-700/50">
                @php
                    $progressPercentage =
                        count($questions) > 0 ? round(($currentQuestionIndex / count($questions)) * 100) : 0;
                @endphp

                <div class="h-3 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 transition-all duration-300 ease-out"
                    style="width: {{ $progressPercentage }}%"></div>
            </div>
            <div class="mt-2 flex justify-between text-sm text-white/80">
                <span>Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}</span>
                <span>{{ $progressPercentage }}% Complete</span>
            </div>
        </div>

        @if (!empty($currentQuestion))
            {{-- quiz content --}}
            <div class="mb-8 text-center">
                <h2 class="mb-4 text-2xl font-bold text-white">{{ $currentQuestion['question_text'] }}</h2>

                @if (!empty($currentQuestion['image_path']))
                    <div class="mb-6">
                        <img src="{{ Storage::url($currentQuestion['image_path']) }}" alt="Question Image"
                            class="mx-auto h-auto max-h-64 max-w-full rounded-lg object-contain shadow-lg" />
                    </div>
                @endif
            </div>

            <!-- Answer Options -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:px-16">
                @foreach ($currentQuestion['options'] as $index => $option)
                    <button x-on:click.once="tryEnterFullscreen()" wire:click="selectAnswer({{ $option['id'] }})"
                        wire:loading.class="cursor-wait opacity-75" wire:loading.attr="disabled"
                        class="transform rounded-2xl border-2 border-slate-600 bg-slate-700/20 p-6 text-left transition-all duration-300 hover:border-slate-500 hover:bg-slate-600/30 focus:outline-none focus:ring-4 focus:ring-slate-500/50">
                        <div class="flex items-center space-x-4">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-600/50 text-sm font-bold text-slate-300">
                                {{ chr(65 + $index) }}
                            </div>
                            <span class="flex-1 text-lg font-medium text-white">
                                @if ($option['image_path'])
                                    <img src="{{ Storage::url($option['image_path']) }}" alt="Option Image"
                                        class="mb-2 w-full rounded-lg object-cover shadow-md" />
                                @endif

                                {{ $option['option_text'] }}
                            </span>
                        </div>
                    </button>
                @endforeach
            </div>
        @endif

        @push('scripts')
            <script>
                function quizHandler() {
                    return {
                        timeRemaining: @js($quiz->time_limit > 0 ? $timeRemaining : 0),
                        isFullscreen: false,
                        timerInterval: null,
                        hasUnsavedChanges: true,

                        init() {
                            // Start only the timer and autosubmit hooks; fullscreen must be triggered by a user gesture
                            this.startTimer();
                            this.setupAutoSubmitTriggers();
                        },

                        setupAutoSubmitTriggers() {
                            // Auto-submit on page refresh/reload
                            window.addEventListener('beforeunload', (event) => {
                                if (this.hasUnsavedChanges) {
                                    this.autoSubmitQuiz();
                                }
                            });

                            // Auto-submit on page hide (mobile/tab switch)
                            window.addEventListener('pagehide', (event) => {
                                if (this.hasUnsavedChanges) {
                                    this.autoSubmitQuiz();
                                }
                            });

                            // Auto-submit on back button
                            window.addEventListener('popstate', (event) => {
                                if (this.hasUnsavedChanges) {
                                    this.autoSubmitQuiz();
                                }
                            });

                            // Removed blur-based auto-submit to avoid premature submissions
                        },

                        autoSubmitQuiz() {
                            this.hasUnsavedChanges = false;
                            if (this.timerInterval) {
                                clearInterval(this.timerInterval);
                            }
                            this.$wire.submitQuiz();
                        },

                        startTimer() {
                            if (@js($quiz->time_limit) > 0 && this.timeRemaining > 0) {
                                this.timerInterval = setInterval(() => {
                                    if (this.timeRemaining > 0) {
                                        this.timeRemaining--;
                                        if (this.timeRemaining <= 0) {
                                            this.handleTimerExpired();
                                        }
                                    }
                                }, 1000);
                            }
                        },

                        handleTimerExpired() {
                            if (this.timerInterval) {
                                clearInterval(this.timerInterval);
                            }
                            this.hasUnsavedChanges = false;
                            this.$wire.submitQuiz();
                        },

                        tryEnterFullscreen() {
                            // Feature-detect and require a user gesture
                            if (this.isFullscreen) return;
                            const el = document.documentElement;
                            try {
                                if (document.fullscreenEnabled && el.requestFullscreen && !document.fullscreenElement) {
                                    el.requestFullscreen()
                                        .then(() => {
                                            this.isFullscreen = true;
                                        })
                                        .catch((err) => {
                                            console.warn('Fullscreen denied:', err?.message || err);
                                            this.isFullscreen = false;
                                        });
                                }
                            } catch (err) {
                                console.warn('Fullscreen not available:', err?.message || err);
                            }
                        },

                        exitFullscreen() {
                            if (document.exitFullscreen && document.fullscreenElement) {
                                document
                                    .exitFullscreen()
                                    .then(() => {
                                        this.isFullscreen = false;
                                    })
                                    .catch((err) => {
                                        console.log('Exit fullscreen failed:', err);
                                    });
                            }
                        },

                        handleBeforeUnload(event) {
                            // This is handled by setupAutoSubmitTriggers now
                            return;
                        },

                        handlePageHide(event) {
                            // This is handled by setupAutoSubmitTriggers now
                            return;
                        },

                        formatTime(seconds) {
                            const safe = Math.max(0, Number.isFinite(seconds) ? seconds : 0);
                            const totalSeconds = Math.floor(safe);
                            const minutes = Math.floor(totalSeconds / 60);
                            const secs = totalSeconds % 60;
                            return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                        },
                    };
                }

                // Listen for quiz completion to exit fullscreen
                window.addEventListener('quiz-completed', () => {
                    if (document.exitFullscreen && document.fullscreenElement) {
                        document.exitFullscreen();
                    }
                });

                // Listen for quiz errors
                window.addEventListener('quiz-error', (event) => {
                    console.error('Quiz error:', event.detail.message);
                });
            </script>
        @endpush
    @else
        {{-- completed notice --}}
        <div class="flex items-center justify-center">
            <x-quiz.results-card :score="$submission->score_percentage" :total-questions="count($questions)" :correct-answers="$correctAnswers" :time-spent="$submission->time_spent_formatted"
                :performance-level="$this->getPerformanceLevel()" :encouragement-message="$this->getEncouragementMessage()" :classroom="$classroom" :quiz="$quiz" :quiz-answers="$userAnswers"
                :quiz-questions="$questions" />
        </div>
    @endif
</div>
