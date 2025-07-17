<div class="min-h-screen w-full bg-slate-900 p-2">
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
                        }"
                        x-data="{ timeRemaining: {{ $timeRemaining }} }" x-init="// Simple countdown timer
                        setInterval(() => {
                            if (timeRemaining > 0) {
                                timeRemaining--;
                                $wire.timeRemaining = timeRemaining;
                                if (timeRemaining <= 0) {
                                    $wire.handleTimerExpired();
                                }
                            }
                        }, 1000);">">
                        <div class="flex items-center justify-center gap-2">
                            <x-gmdi-schedule class="h-5 w-5 text-white" />
                            <div class="text-xl font-bold text-white">
                                <span x-text="formatTime(timeRemaining)"></span>
                            </div>
                        </div>

                        <!-- Warning Messages -->
                        <div x-show="timeRemaining <= 60 && timeRemaining > 0"
                            class="mt-2 animate-pulse text-center text-sm text-white">
                            Auto-submit in
                            <span x-text="Math.floor(timeRemaining)"></span>
                            seconds!
                        </div>
                        <div x-show="timeRemaining <= 300 && timeRemaining > 60"
                            class="mt-2 text-center text-sm text-white">
                            <span x-text="Math.floor(timeRemaining / 60)"></span>
                            minutes remaining
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

        {{-- quiz content --}}
        <div class="mb-8 text-center">
            <h2 class="mb-4 text-2xl font-bold text-white">{{ $currentQuestion['question_text'] }}</h2>

            @if ($currentQuestion['image_path'])
                <div class="mb-6">
                    <img src="{{ Storage::url($currentQuestion['image_path']) }}" alt="Question Image"
                        class="mx-auto h-auto max-h-64 max-w-full rounded-lg object-contain shadow-lg" />
                </div>
            @endif
        </div>

        <!-- Answer Options -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:px-16">
            @foreach ($currentQuestion['options'] as $index => $option)
                <button wire:click="selectAnswer({{ $option['id'] }})" wire:loading.class="cursor-wait opacity-75"
                    wire:loading.attr="disabled" @class([
                        'transform rounded-2xl border-2 p-6 text-left transition-all duration-300 hover:bg-slate-600/30 focus:outline-none focus:ring-4 focus:ring-slate-500/50',
                        'border-slate-400 bg-slate-600/50' => $selectedAnswer == $option['id'],
                        'border-slate-600 bg-slate-700/20 hover:border-slate-500 hover:bg-slate-600/30' =>
                            $selectedAnswer != $option['id'],
                    ])>
                    <div class="flex items-center space-x-4">
                        <div @class([
                            'flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold',
                            'bg-slate-500 text-white' => $selectedAnswer == $option['id'],
                            'bg-slate-600/50 text-slate-300' => $selectedAnswer != $option['id'],
                        ])>
                            {{ chr(65 + $index) }}
                        </div>
                        <span class="flex-1 text-lg font-medium text-white">{{ $option['option_text'] }}</span>
                    </div>
                </button>
            @endforeach
        </div>
        @push('scripts')
            <script>
                function formatTime(seconds) {
                    // Ensure we're working with integers only
                    const totalSeconds = Math.floor(seconds);
                    const minutes = Math.floor(totalSeconds / 60);
                    const secs = totalSeconds % 60;
                    return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                }

                // Listen for timer events
                window.addEventListener('timer-warning', (event) => {
                    const minutes = Math.floor(event.detail.minutes);
                    if (minutes <= 5) {
                        showNotification(`⚠️ Warning: ${minutes} minutes remaining!`, 'warning');
                    }
                });

                window.addEventListener('auto-submit-warning', (event) => {
                    showNotification('🚨 Quiz will auto-submit in 1 minute!', 'error');
                });

                window.addEventListener('timer-expired', (event) => {
                    showNotification('⏰ Time expired! Quiz submitted automatically.', 'info');
                });

                function showNotification(message, type) {
                    // Create notification element
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${
                        type === 'warning' ? 'bg-yellow-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                    }`;
                    notification.textContent = message;

                    document.body.appendChild(notification);

                    // Remove after 5 seconds
                    setTimeout(() => {
                        notification.remove();
                    }, 5000);
                }
            </script>
        @endpush
    @else
        {{-- completed notice --}}
        <div class="flex items-center justify-center">
            <x-quiz.results-card :score="$submission->score_percentage" :total-questions="count($questions)" :correct-answers="$correctAnswers" :time-spent="$submission->time_spent_formatted"
                :performance-level="$this->getPerformanceLevel()" :encouragement-message="$this->getEncouragementMessage()" :classroom="$classroom" :quiz="$quiz" :quiz-answers="$answers"
                :quiz-questions="$questions" />
        </div>
    @endif
</div>
