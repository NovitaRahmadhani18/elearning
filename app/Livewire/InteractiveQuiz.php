<?php

namespace App\Livewire;

use App\Models\Classroom;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\QuizAnswer;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.index')]
class InteractiveQuiz extends Component
{
    public Classroom $classroom;
    public Quiz $quiz;
    public $questions = [];
    public $currentQuestionIndex = 0;
    public $selectedAnswer = null;
    public $answers = [];
    public $timeRemaining = 0;
    public $timeElapsed = 0;
    public $questionTimeSpent = 0;
    public $totalTimeSpent = 0;
    public $isCompleted = false;
    public $showResults = false;
    public $score = 0;
    public $correctAnswers = 0;
    public $submission = null;
    public $questionStartTime = null;
    public $quizStartTime = null;
    public $isAnswerSelected = false;
    public $showFeedback = false;
    public $feedbackMessage = '';
    public $isCorrect = false;
    public $totalQuestions = 0;
    public $hasCompletedBefore = false;
    public $previousSubmission = null;

    protected $listeners = [
        'timerExpired' => 'handleTimerExpired',
        'answerSelected' => 'handleAnswerSelected',
        'nextQuestion' => 'handleNextQuestion'
    ];

    public function mount(Classroom $classroom, Quiz $quiz)
    {
        if (!auth()->user()->classrooms()->where('classroom_id', $classroom->id)->exists()) {
            abort(403, 'You are not enrolled in this classroom.');
        }

        // Check if quiz is available
        if ($quiz->start_time && $quiz->start_time->isFuture()) {
            return redirect()->route('user.classroom.quiz.show', [$classroom, $quiz])
                ->with('error', 'Quiz has not started yet.');
        }

        if ($quiz->due_time && $quiz->due_time->isPast()) {
            return redirect()->route('user.classroom.quiz.show', [$classroom, $quiz])
                ->with('error', 'Quiz deadline has passed.');
        }

        /* // Check if user has already submitted */
        /* if ($quiz->hasUserSubmitted(auth()->id())) { */
        /*     $submission = $quiz->getUserSubmission(auth()->id()); */
        /*     if ($submission->is_completed) { */
        /*         return redirect()->route('user.classroom.quiz.show', [$classroom, $quiz]) */
        /*             ->with('info', 'You have already completed this quiz.'); */
        /*     } */
        /* } */

        // Load quiz with questions and options
        $quiz->load(['questions.options']);
        $this->classroom = $classroom;
        $this->quiz = $quiz;


        $this->questions = $quiz->questions()->with('options')->get()->toArray();
        $this->timeRemaining = $quiz->time_limit_in_seconds;
        $this->quizStartTime = now();
        $this->questionStartTime = now();
        $this->totalQuestions = count($this->questions);

        // Check if user has completed this quiz before
        $this->checkPreviousSubmission();

        // Initialize or get existing submission
        if (!$this->hasCompletedBefore) {
            $this->initializeSubmission();
        }
    }

    public function checkPreviousSubmission()
    {
        $this->previousSubmission = QuizSubmission::where('quiz_id', $this->quiz->id)
            ->where('user_id', auth()->id())
            ->where('is_completed', true)
            ->first();

        if ($this->previousSubmission) {
            $this->hasCompletedBefore = true;
            $this->score = $this->previousSubmission->score;
            $this->correctAnswers = $this->previousSubmission->correct_answers;
            $this->totalTimeSpent = $this->previousSubmission->time_spent;
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

            // If continuing an existing submission, load progress
            if ($this->submission->answers) {
                $this->answers = $this->submission->answers;
                $this->currentQuestionIndex = count($this->answers);
                $this->totalTimeSpent = $this->submission->time_spent;
            }
        } catch (Exception $e) {

            $this->dispatch('quiz-error', [
                'message' => 'Error starting quiz. Please try again.'
            ]);
        }
    }

    public function selectAnswer($optionId)
    {
        if ($this->isAnswerSelected || $this->isCompleted) {
            return;
        }

        $this->selectedAnswer = $optionId;
        $this->isAnswerSelected = true;
        $this->questionTimeSpent = now()->diffInSeconds($this->questionStartTime);

        // Check if answer is correct
        $currentQuestion = $this->questions[$this->currentQuestionIndex];
        $correctOption = collect($currentQuestion['options'])->firstWhere('is_correct', true);
        $this->isCorrect = $correctOption && $correctOption['id'] == $optionId;

        // Save answer
        $this->saveAnswer($optionId);

        if ($this->isCorrect) {
            $this->correctAnswers++;
        }

        // Auto-advance to next question after 1.5 seconds
        $this->dispatch('auto-advance-question');
    }

    public function saveAnswer($optionId)
    {
        try {
            $currentQuestion = $this->questions[$this->currentQuestionIndex];
            $correctOption = collect($currentQuestion['options'])->firstWhere('is_correct', true);
            $isCorrect = $correctOption && $correctOption['id'] == $optionId;

            // Save to quiz_answers table
            QuizAnswer::create([
                'quiz_submission_id' => $this->submission->id,
                'question_id' => $currentQuestion['id'],
                'selected_option_id' => $optionId,
                'is_correct' => $isCorrect,
                'time_spent' => $this->questionTimeSpent
            ]);

            // Update answers array
            $this->answers[] = [
                'question_id' => $currentQuestion['id'],
                'selected_option_id' => $optionId,
                'is_correct' => $isCorrect,
                'time_spent' => $this->questionTimeSpent
            ];

            // Update submission
            $this->submission->update([
                'answers' => $this->answers,
                'correct_answers' => $this->correctAnswers,
                'time_spent' => $this->totalTimeSpent + $this->questionTimeSpent
            ]);
        } catch (Exception $e) {
            Log::error('Error saving quiz answer', [
                'error' => $e->getMessage(),
                'question_id' => $currentQuestion['id'],
                'option_id' => $optionId
            ]);
        }
    }

    public function nextQuestion()
    {
        if (!$this->isAnswerSelected) {
            return;
        }

        $this->totalTimeSpent += $this->questionTimeSpent;
        $this->currentQuestionIndex++;

        if ($this->currentQuestionIndex >= count($this->questions)) {
            $this->completeQuiz();
        } else {
            $this->resetQuestionState();
        }
    }

    public function resetQuestionState()
    {
        $this->selectedAnswer = null;
        $this->isAnswerSelected = false;
        $this->showFeedback = false;
        $this->questionTimeSpent = 0;
        $this->questionStartTime = now();
    }

    public function completeQuiz()
    {
        try {
            $this->totalTimeSpent += $this->questionTimeSpent;
            $this->score = round(($this->correctAnswers / count($this->questions)) * 100, 2);

            // Update final submission
            $this->submission->update([
                'completed_at' => now(),
                'is_completed' => true,
                'score' => $this->score,
                'correct_answers' => $this->correctAnswers,
                'time_spent' => $this->totalTimeSpent
            ]);

            $this->isCompleted = true;
            $this->showResults = true;

            Log::info('Quiz completed', [
                'quiz_id' => $this->quiz->id,
                'user_id' => auth()->id(),
                'score' => $this->score,
                'correct_answers' => $this->correctAnswers,
                'total_time' => $this->totalTimeSpent
            ]);

            $this->dispatch('quiz-completed', [
                'score' => $this->score,
                'correctAnswers' => $this->correctAnswers,
                'totalQuestions' => count($this->questions)
            ]);
        } catch (Exception $e) {
            Log::error('Error completing quiz', [
                'error' => $e->getMessage(),
                'quiz_id' => $this->quiz->id,
                'user_id' => auth()->id()
            ]);
        }
    }

    public function handleTimerExpired()
    {
        if (!$this->isCompleted) {
            $this->completeQuiz();
        }
    }

    public function getCurrentQuestion()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function getProgressPercentage()
    {
        if (count($this->questions) === 0) {
            return 0;
        }
        return round(($this->currentQuestionIndex / count($this->questions)) * 100, 2);
    }

    public function getTimeSpentFormatted()
    {
        $totalSeconds = $this->totalTimeSpent + $this->questionTimeSpent;
        $minutes = floor($totalSeconds / 60);
        $seconds = $totalSeconds % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getFormattedTimeSpent()
    {
        return $this->getTimeSpentFormatted();
    }

    public function getPerformanceLevel()
    {
        if ($this->score >= 90) {
            return 'Excellent';
        } elseif ($this->score >= 70) {
            return 'Good';
        } elseif ($this->score >= 50) {
            return 'Average';
        } else {
            return 'Needs Improvement';
        }
    }

    public function getEncouragementMessage()
    {
        $level = $this->getPerformanceLevel();

        switch ($level) {
            case 'Excellent':
                return 'Outstanding performance! You\'ve mastered this topic.';
            case 'Good':
                return 'Great job! You have a solid understanding of the material.';
            case 'Average':
                return 'Good effort! Review the material and try again to improve.';
            default:
                return 'Don\'t give up! Review the material and practice more.';
        }
    }

    public function autoSave()
    {
        if ($this->submission && !$this->isCompleted) {
            try {
                $this->submission->update([
                    'answers' => $this->answers,
                    'correct_answers' => $this->correctAnswers,
                    'time_spent' => $this->totalTimeSpent + $this->questionTimeSpent
                ]);
            } catch (Exception $e) {
                Log::error('Auto-save failed', [
                    'error' => $e->getMessage(),
                    'quiz_id' => $this->quiz->id,
                    'user_id' => auth()->id()
                ]);
            }
        }
    }

    public function timeUp()
    {
        if (!$this->isCompleted) {
            $this->completeQuiz();
        }
    }

    public function retakeQuiz()
    {
        // Delete previous completed submission
        if ($this->previousSubmission) {
            $this->previousSubmission->delete();
        }

        // Reset completion status
        $this->hasCompletedBefore = false;
        $this->previousSubmission = null;

        // Reset all properties
        $this->currentQuestionIndex = 0;
        $this->selectedAnswer = null;
        $this->answers = [];
        $this->totalTimeSpent = 0;
        $this->questionTimeSpent = 0;
        $this->isCompleted = false;
        $this->showResults = false;
        $this->score = 0;
        $this->correctAnswers = 0;
        $this->isAnswerSelected = false;
        $this->showFeedback = false;
        $this->isCorrect = false;

        // Reinitialize
        $this->quizStartTime = now();
        $this->questionStartTime = now();
        $this->initializeSubmission();
    }

    public function backToClassroom()
    {
        return redirect()->route('user.classroom.show', $this->classroom);
    }

    public function getPreviousSubmissionFormattedTimeSpent()
    {
        if (!$this->previousSubmission) {
            return '00:00';
        }

        $minutes = floor($this->previousSubmission->time_spent / 60);
        $seconds = $this->previousSubmission->time_spent % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getPreviousSubmissionPerformanceLevel()
    {
        if (!$this->previousSubmission) {
            return 'unknown';
        }

        $score = $this->previousSubmission->score;
        if ($score >= 90) {
            return 'Excellent';
        } elseif ($score >= 70) {
            return 'Good';
        } elseif ($score >= 50) {
            return 'Average';
        } else {
            return 'Needs Improvement';
        }
    }

    public function render()
    {
        return view('livewire.interactive-quiz', [
            'currentQuestion' => $this->getCurrentQuestion(),
            'progressPercentage' => $this->getProgressPercentage(),
            'timeSpentFormatted' => $this->getTimeSpentFormatted(),
            'questionNumber' => $this->currentQuestionIndex + 1,
            'totalQuestions' => count($this->questions)
        ]);
    }
}

