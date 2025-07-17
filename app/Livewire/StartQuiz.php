<?php

namespace App\Livewire;

use App\Models\Classroom;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizSubmission;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.index')]
class StartQuiz extends Component
{

    public QuizSubmission $submission;
    public $questionTimeSpent = 0; // Time spent on the current question in seconds

    public Classroom $classroom;
    public Quiz $quiz;

    public array $questions = [];
    public $currentQuestionIndex = 0;
    public $currentQuestion;
    public $questionStartTime = null;

    public $answers = [];
    public $selectedAnswer = null;
    public $isCorrect = false;
    public $correctAnswers = 0;

    public $totalTimeSpent = 0;

    public $isCompleted = false;

    // Server-side timer properties
    public $serverStartTime = null;
    public $timeRemaining = 0;
    public $timerWarningShown = false;
    public $autoSubmitWarning = false;
    public $isTimerExpired = false;

    public function mount(Classroom $classroom, Quiz $quiz)
    {
        if (!auth()->user()->classrooms->contains($classroom)) {
            abort(403, 'You are not enrolled in this classroom.');
        }

        // Check if quiz exists
        if ($classroom->contents->where('contentable_type', Quiz::class)->where('contentable_id', $quiz->id)->isEmpty()) {
            abort(404, 'Quiz not found in this classroom.');
        }

        /* // Check if quiz is available */
        if ($quiz->start_time && $quiz->start_time->isFuture()) {
            return redirect()->route('user.classroom.quiz.show', [$classroom, $quiz])
                ->with('error', 'Quiz has not started yet.');
        }
        /**/
        /* // Check if quiz is completed */
        if ($quiz->due_time && $quiz->due_time->isPast()) {
            // Auto-complete the expired quiz
            $content = $quiz->contents()->where('classroom_id', $classroom->id)->first();
            if ($content) {
                \App\Services\ExpiredQuizService::handleExpiredQuizContent($content, auth()->id());
            }

            return redirect()->route('user.classroom.quiz.show', [$classroom, $quiz])
                ->with('info', 'Quiz deadline has passed. The quiz has been automatically completed.');
        }

        // Load quiz with questions and options
        $quiz->load(['questions.options']);
        $this->classroom = $classroom;
        $this->quiz = $quiz;
        $this->questions = $quiz->questions()->with('options')->get()->toArray();
        $this->currentQuestion = $this->getCurrentQuestion();

        $this->initializeSubmission();
        $this->initializeTimer();

        // Additional validation after initialization
        if ($this->quiz->time_limit > 0 && !$this->isCompleted) {
            // Check if there's enough time left to continue
            if ($this->timeRemaining <= 0) {
                $this->handleTimerExpired();
            }
        }
    }

    public function initializeTimer()
    {
        if ($this->quiz->time_limit > 0) {
            $this->serverStartTime = $this->submission->started_at;
            $this->calculateTimeRemaining();

            // If timer has expired, handle it immediately
            if ($this->timeRemaining <= 0) {
                $this->handleTimerExpired();
            }
        }
    }

    public function calculateTimeRemaining()
    {
        if ($this->quiz->time_limit > 0 && $this->serverStartTime) {
            $elapsedSeconds = now()->diffInSeconds($this->serverStartTime);
            $this->timeRemaining = max(0, (int)($this->quiz->time_limit - $elapsedSeconds));

            if ($this->timeRemaining <= 0) {
                $this->handleTimerExpired();
            }
        }
    }

    public function refreshTimer()
    {
        $this->calculateTimeRemaining();
        return $this->timeRemaining;
    }

    // Auto-submit when user leaves page
    public function autoSubmitOnLeave()
    {
        if (!$this->isCompleted && !$this->isTimerExpired) {
            $this->completeQuiz();
        }
    }
    public function handleTimerExpired()
    {
        if (!$this->isTimerExpired && !$this->isCompleted) {
            $this->isTimerExpired = true;
            $this->dispatch('timer-expired');
            $this->completeQuiz();
        }
    }

    public function initializeSubmission()
    {
        try {
            $this->submission = QuizSubmission::firstOrCreate([
                'quiz_id' => $this->quiz->id,
                'user_id' => auth()->id(),
            ], [
                'started_at' => now(),
                'total_questions' => count($this->questions),
                'answers' => []
            ]);

            // check if the submission is already isCompleted
            if ($this->submission->is_completed || $this->submission->completed_at || count($this->submission->answers) >= $this->submission->total_questions) {
                $this->isCompleted = true;
                return;
            }

            // Validate submission started_at time to prevent timer manipulation
            if ($this->submission->started_at && $this->submission->started_at->isFuture()) {
                // If start time is in the future, reset it to now
                $this->submission->update(['started_at' => now()]);
            }

            // If continuing an existing submission, load progress
            if ($this->submission->answers) {
                $this->answers = $this->submission->answers;
                $this->currentQuestionIndex = count($this->answers);
                $this->currentQuestion = $this->getCurrentQuestion();
                $this->totalTimeSpent = $this->submission->time_spent;

                // Set question start time for continued quiz
                if (!$this->isCompleted && $this->currentQuestion) {
                    $this->questionStartTime = now();
                }
            } else {
                // For new quiz, initialize question start time
                if (!$this->isCompleted) {
                    $this->questionStartTime = now();
                }
            }
        } catch (Exception $e) {
            $this->dispatch('quiz-error', [
                'message' => 'Error starting quiz. Please try again.'
            ]);
        }
    }

    public function selectAnswer($optionId)
    {
        if ($this->isCompleted || $this->isTimerExpired) {
            return; // Prevent further actions if the quiz is already completed or timer expired
        }

        // Simple timer check
        $this->calculateTimeRemaining();
        if ($this->timeRemaining <= 0) {
            $this->handleTimerExpired();
            return;
        }

        $this->selectedAnswer = $optionId;

        $this->saveAnswer($optionId);

        if ($this->isCorrect) {
            $this->correctAnswers++;
        }

        $this->nextQuestion();
    }

    public function nextQuestion()
    {
        if ($this->isCompleted) {
            return; // Prevent further actions if the quiz is already completed
        }

        $this->currentQuestionIndex++;

        if ($this->currentQuestionIndex >= count($this->questions)) {
            $this->completeQuiz();
        } else {
            $this->currentQuestion = $this->getCurrentQuestion();
            $this->questionStartTime = now();
            $this->questionTimeSpent = 0; // Reset time spent for the new question
            $this->selectedAnswer = null; // Reset selected answer for the new question
            $this->isCorrect = false; // Reset correctness for the new question
        }
    }

    public function completeQuiz()
    {
        DB::beginTransaction();
        try {
            // Calculate final time spent for current question if not already saved
            if ($this->questionStartTime && !$this->isTimerExpired) {
                $currentQuestionTime = now()->diffInSeconds($this->questionStartTime);
                $finalTimeSpent = $this->totalTimeSpent + $currentQuestionTime;
            } else {
                $finalTimeSpent = $this->totalTimeSpent;
            }

            // Calculate final score
            $scorePercentage = $this->submission->total_questions > 0 ?
                ($this->correctAnswers / $this->submission->total_questions) * 100 : 0;

            $finalScore = round($scorePercentage * ($this->quiz->points / 100), 2);

            $this->submission->update([
                'is_completed' => true,
                'completed_at' => now(),
                'score' => $finalScore,
                'correct_answers' => $this->correctAnswers,
                'time_spent' => $finalTimeSpent
            ]);

            $this->isCompleted = true;

            $currentContent = $this->quiz->contents()
                ->where('classroom_id', $this->classroom->id)
                ->firstOrFail();

            auth()->user()->completedContents()->syncWithoutDetaching($currentContent->id);
            auth()->user()->addPoints($finalScore);

            $this->dispatch('quiz-completed', [
                'message' => 'Quiz completed successfully!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error completing quiz: ' . $e->getMessage());
            $this->dispatch('quiz-error', [
                'message' => 'Error completing quiz. Please try again.'
            ]);
        }
        DB::commit();
    }

    public function saveAnswer($optionId)
    {
        DB::beginTransaction();
        try {
            // Calculate time spent on current question
            if ($this->questionStartTime) {
                $this->questionTimeSpent = now()->diffInSeconds($this->questionStartTime);
            } else {
                $this->questionTimeSpent = 0;
            }

            $correctOption = collect($this->currentQuestion['options'])->firstWhere('is_correct', true);
            $isCorrect = $correctOption && $correctOption['id'] == $optionId;
            $this->isCorrect = $isCorrect;

            // Save to quiz_answers table
            QuizAnswer::create([
                'quiz_submission_id' => $this->submission->id,
                'question_id' => $this->currentQuestion['id'],
                'selected_option_id' => $optionId,
                'is_correct' => $isCorrect,
                'time_spent' => $this->questionTimeSpent
            ]);

            // Update answers array
            $this->answers[] = [
                'question_id' => $this->currentQuestion['id'],
                'selected_option_id' => $optionId,
                'is_correct' => $isCorrect,
                'time_spent' => $this->questionTimeSpent
            ];

            // Accumulate total time spent
            $this->totalTimeSpent += $this->questionTimeSpent;

            // Update submission with accumulated time
            $this->submission->update([
                'answers' => $this->answers,
                'correct_answers' => $this->correctAnswers,
                'time_spent' => $this->totalTimeSpent
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            dump($e->getMessage());
        }
        DB::commit();
    }

    public function retakeQuiz()
    {
        DB::beginTransaction();
        try {
            // Reset submission
            $this->submission->update([
                'is_completed' => false,
                'completed_at' => null,
                'correct_answers' => 0,
                'time_spent' => 0,
                'answers' => []
            ]);

            // Reset quiz state
            $this->isCompleted = false;
            $this->currentQuestionIndex = 0;
            $this->currentQuestion = $this->getCurrentQuestion();
            $this->answers = [];
            $this->correctAnswers = 0;
            $this->totalTimeSpent = 0;
            $this->questionStartTime = now();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error retaking quiz: ' . $e->getMessage());
            $this->dispatch('quiz-error', [
                'message' => 'Error retaking quiz. Please try again.'
            ]);
        }
        DB::commit();
    }

    public function getPerformanceLevel()
    {
        $percentage = $this->submission->total_questions > 0 ?
            ($this->correctAnswers / $this->submission->total_questions) * 100 : 0;

        if ($percentage >= 90) return 'Excellent';
        if ($percentage >= 80) return 'Very Good';
        if ($percentage >= 70) return 'Good';
        if ($percentage >= 60) return 'Fair';
        return 'Needs Improvement';
    }

    public function getEncouragementMessage()
    {
        $percentage = $this->submission->total_questions > 0 ?
            ($this->correctAnswers / $this->submission->total_questions) * 100 : 0;

        if ($percentage >= 90) return 'Outstanding performance! You\'ve mastered this topic!';
        if ($percentage >= 80) return 'Great job! You have a solid understanding of the material.';
        if ($percentage >= 70) return 'Good work! Keep practicing to improve further.';
        if ($percentage >= 60) return 'Not bad! Review the materials and try again.';
        return 'Don\'t give up! Every expert was once a beginner.';
    }

    public function getCurrentQuestion()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function render()
    {
        // Update timer in real-time
        if ($this->quiz->time_limit > 0 && !$this->isCompleted) {
            $this->calculateTimeRemaining();
        }

        return view('livewire.start-quiz');
    }
}
