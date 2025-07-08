@php
    $timeRemaining = $quiz->time_limit > 0 ? $quiz->time_limit * 60 - $timeElapsed : 0;
    $progressPercentage = $totalQuestions > 0 ? round(($currentQuestionIndex / $totalQuestions) * 100) : 0;
    $questionNumber = $currentQuestionIndex + 1;
@endphp

<div
    class="min-h-screen w-full bg-gradient-to-br from-purple-600 via-blue-600 to-indigo-700"
    x-data="quizInteraction()"
    x-init="initQuiz()"
>
    @if ($hasCompletedBefore)
        <!-- Quiz Already Completed Notice -->
        <x-quiz.completed-notice
            :quiz="$quiz"
            :classroom="$classroom"
            :score="$score"
            :correct-answers="$correctAnswers"
            :total-questions="$totalQuestions"
            :time-spent="$this->getPreviousSubmissionFormattedTimeSpent()"
            :performance-level="$this->getPreviousSubmissionPerformanceLevel()"
            :completed-at="$previousSubmission->completed_at"
        />
    @elseif (! $isCompleted)
        <div class="mx-auto flex h-full max-w-4xl flex-col">
            <!-- Quiz Header Component -->
            <div class="flex-shrink-0">
                <x-quiz.header
                    :quiz="$quiz"
                    :classroom="$classroom"
                    :progress-percentage="$progressPercentage"
                    :question-number="$questionNumber"
                    :total-questions="$totalQuestions"
                    :time-remaining="$timeRemaining"
                />
            </div>

            @if ($currentQuestion)
                <!-- Question Card Component -->
                <div class="flex flex-1 flex-col justify-center">
                    <x-quiz.question-card
                        :current-question="$currentQuestion"
                        :question-number="$questionNumber"
                        :selected-answer="$selectedAnswer"
                        :show-feedback="$showFeedback"
                        :is-correct="$isCorrect"
                        :is-answer-selected="$isAnswerSelected"
                        :current-question-index="$currentQuestionIndex"
                        :total-questions="$totalQuestions"
                    />
                </div>
            @endif

            <!-- Auto-save indicator -->
            <div class="flex-shrink-0 text-center">
                <div wire:loading wire:target="autoSave" class="text-white/80">
                    <x-gmdi-save class="mr-2 inline h-4 w-4 animate-pulse" />
                    Saving...
                </div>
            </div>
        </div>
    @else
        <!-- Results Card Component -->
        <div class="flex items-center justify-center">
            <x-quiz.results-card
                :score="$score"
                :total-questions="$totalQuestions"
                :correct-answers="$correctAnswers"
                :time-spent="$this->getFormattedTimeSpent()"
                :performance-level="$this->getPerformanceLevel()"
                :encouragement-message="$this->getEncouragementMessage()"
                :classroom="$classroom"
                :quiz="$quiz"
                :quiz-answers="$answers"
                :quiz-questions="$questions"
            />
        </div>
    @endif
</div>

@push('scripts')
    <script>
        function quizInteraction() {
            return {
                initQuiz() {
                    // Initialize keyboard navigation
                    document.addEventListener('keydown', (e) => {
                        if (e.key >= '1' && e.key <= '4') {
                            const optionIndex = parseInt(e.key) - 1;
                            const options = document.querySelectorAll('[wire\\:click^="selectAnswer"]');
                            if (options[optionIndex]) {
                                options[optionIndex].click();
                            }
                        }
                    });

                    // Set quiz as completed flag for page refresh prevention
                    window.addEventListener('beforeunload', (e) => {
                        if (!@js($isCompleted)) {
                            e.preventDefault();
                            e.returnValue = 'Are you sure you want to leave? Your quiz progress will be lost.';
                        }
                    });

                    // Listen for auto-advance-question event
                    Livewire.on('auto-advance-question', () => {
                        setTimeout(() => {
                            @this.call('nextQuestion');
                        }, 1500); // Auto-advance after 1.5 seconds
                    });
                }
            }
        }

        function timer(initialTime) {
            return {
                timeLeft: initialTime,
                startTimer() {
                    if (this.timeLeft > 0) {
                        this.interval = setInterval(() => {
                            this.timeLeft--;
                            if (this.timeLeft <= 0) {
                                clearInterval(this.interval);
                                @this.call('timeUp');
                            }
                        }, 1000);
                    }
                },
                formatTime(seconds) {
                    const hours = Math.floor(seconds / 3600);
                    const minutes = Math.floor((seconds % 3600) / 60);
                    const remainingSeconds = seconds % 60;

                    if (hours > 0) {
                        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
                    } else {
                        return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
                    }
                }
            }
        }

        // Auto-save functionality
        setInterval(() => {
            if (!@js($isCompleted)) {
                @this.call('autoSave');
            }
        }, 30000); // Auto-save every 30 seconds
    </script>
@endpush
