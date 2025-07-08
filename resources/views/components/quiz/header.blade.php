@props([
    'quiz',
    'classroom',
    'progressPercentage',
    'questionNumber',
    'totalQuestions',
    'timeRemaining',
])

<div class="mb-8 rounded-2xl bg-white/10 p-6 text-white backdrop-blur-sm">
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="rounded-full bg-white/20 p-3">
                <x-gmdi-quiz class="h-6 w-6" />
            </div>
            <div>
                <h1 class="text-2xl font-bold">{{ $quiz->title }}</h1>
                <p class="text-white/80">{{ $classroom->name }}</p>
            </div>
        </div>

        @if ($quiz->time_limit > 0)
            <div class="rounded-lg bg-white/20 px-4 py-2">
                <div class="flex items-center space-x-2">
                    <x-gmdi-schedule class="h-5 w-5 text-white/80" />
                    <div>
                        <div class="text-sm text-white/80">Time Remaining</div>
                        <div class="text-xl font-bold" x-data="timer({{ $timeRemaining }})" x-init="startTimer()">
                            <span x-text="formatTime(timeLeft)"></span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Progress Bar -->
    <div class="h-3 w-full rounded-full bg-white/20">
        <div
            class="h-3 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 transition-all duration-300 ease-out"
            style="width: {{ $progressPercentage }}%"
        ></div>
    </div>
    <div class="mt-2 flex justify-between text-sm text-white/80">
        <span>Question {{ $questionNumber }} of {{ $totalQuestions }}</span>
        <span>{{ $progressPercentage }}% Complete</span>
    </div>
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
                    }
                };
        }
            }
    </script>
@endpush
