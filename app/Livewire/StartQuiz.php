<?php

namespace App\Livewire;

use App\Models\Classroom;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use Exception;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.index')]
class StartQuiz extends Component
{
    public QuizSubmission $submission;
    public Classroom $classroom;
    public Quiz $quiz;

    public array $questions = [];
    public int $currentQuestionIndex = 0;
    public array $currentQuestion = [];
    public array $userAnswers = [];

    public int $timeRemaining = 0;
    public bool $isCompleted = false;
    public int $correctAnswers = 0;
    public int $totalTimeSpent = 0;

    public function mount(Classroom $classroom, Quiz $quiz)
    {
        if (!auth()->user()->classrooms->contains($classroom)) {
            abort(403, 'You are not enrolled in this classroom.');
        }

        // Check if quiz is available
        if ($quiz->start_time && $quiz->start_time->isFuture()) {
            return redirect()->route('user.classroom.quiz.show', [$classroom, $quiz])
                ->with('error', 'Quiz has not started yet.');
        }

        // Check if quiz deadline has passed
        if ($quiz->due_time && $quiz->due_time->isPast()) {
            $content = $quiz->contents()->where('classroom_id', $classroom->id)->first();
            if ($content) {
                \App\Services\ExpiredQuizService::handleExpiredQuizContent($content, auth()->id());
            }
        }

        $this->classroom = $classroom;
        $this->quiz = $quiz;
        $this->questions = $quiz->questions()->with('options')->get()->toArray();

        $this->initializeQuiz();
    }

    public function initializeQuiz()
    {
        try {
            // Create or get existing submission
            $this->submission = QuizSubmission::firstOrCreate([
                'quiz_id' => $this->quiz->id,
                'user_id' => auth()->id(),
            ], [
                'started_at' => now(),
                'total_questions' => count($this->questions),
                'answers' => [],
                'is_completed' => false
            ]);

            // Check if already completed
            if ($this->submission->is_completed) {
                $this->isCompleted = true;
                $this->correctAnswers = $this->submission->correct_answers ?? 0;
                $this->totalTimeSpent = $this->submission->time_spent ?? 0;
                return;
            }

            // Initialize quiz state
            $this->userAnswers = $this->submission->answers ?? [];
            $this->currentQuestionIndex = count($this->userAnswers);
            $this->currentQuestion = $this->getCurrentQuestion();
            $this->correctAnswers = collect($this->userAnswers)->where('is_correct', true)->count();
            $this->calculateTimeRemaining();

            // Check if time is up
            if ($this->quiz->time_limit > 0 && $this->timeRemaining <= 0) {
                $this->submitQuiz();
            }
        } catch (Exception $e) {
            Log::error('Error initializing quiz: ' . $e->getMessage());
            $this->dispatch('quiz-error', ['message' => 'Error starting quiz. Please try again.']);
        }
    }

    public function selectAnswer($optionId)
    {
        if ($this->isCompleted) {
            return;
        }

        try {
            $correctOption = collect($this->currentQuestion['options'])->firstWhere('is_correct', true);
            $isCorrect = $correctOption && $correctOption['id'] == $optionId;

            // Save answer
            $this->userAnswers[] = [
                'question_id' => $this->currentQuestion['id'],
                'selected_option_id' => $optionId,
                'is_correct' => $isCorrect
            ];

            if ($isCorrect) {
                $this->correctAnswers++;
            }

            // Update submission
            $this->submission->update([
                'answers' => $this->userAnswers,
                'correct_answers' => $this->correctAnswers
            ]);

            // Auto move to next question or submit
            $this->currentQuestionIndex++;

            if ($this->currentQuestionIndex >= count($this->questions)) {
                $this->submitQuiz();
            } else {
                $this->currentQuestion = $this->getCurrentQuestion();
            }
        } catch (Exception $e) {
            Log::error('Error selecting answer: ' . $e->getMessage());
            $this->dispatch('quiz-error', ['message' => 'Error saving answer. Please try again.']);
        }
    }

    public function submitQuiz()
    {
        if ($this->isCompleted) {
            return;
        }

        try {
            // Calculate final time spent
            $this->totalTimeSpent = $this->submission->started_at ?
                now()->diffInSeconds($this->submission->started_at) : 0;

            // Calculate score using the formula: (quiz_points / total_questions) * correct_answers
            $finalScore = ($this->quiz->points / count($this->questions)) * $this->correctAnswers;

            // Update submission
            $this->submission->update([
                'is_completed' => true,
                'completed_at' => now(),
                'total_questions' => count($this->questions),
                'correct_answers' => $this->correctAnswers,
                'time_spent' => $this->totalTimeSpent,
                'score' => $finalScore,
                'answers' => $this->userAnswers
            ]);

            // Mark content as completed and award points
            $content = $this->quiz->contents()->where('classroom_id', $this->classroom->id)->first();
            if ($content) {
                // Sync with leaderboard data
                auth()->user()->completedContents()->syncWithoutDetaching([
                    $content->id => [
                        'completion_time' => $this->totalTimeSpent,
                        'points_earned' => $finalScore,
                        'score' => $finalScore // Quiz score for leaderboard
                    ]
                ]);

                auth()->user()->addPoints($finalScore);

                // Update classroom progress
                $newProgress = auth()->user()->getClassroomProgress($this->classroom->id);
                auth()->user()->classrooms()->updateExistingPivot($this->classroom->id, [
                    'progress' => $newProgress
                ]);
            }

            $this->isCompleted = true;
            $this->dispatch('quiz-completed');
        } catch (Exception $e) {
            Log::error('Error submitting quiz: ' . $e->getMessage());
            $this->dispatch('quiz-error', ['message' => 'Error submitting quiz. Please try again.']);
        }
    }

    public function autoSubmitOnLeave()
    {
        if (!$this->isCompleted) {
            $this->submitQuiz();
        }
    }

    public function calculateTimeRemaining()
    {
        if ($this->quiz->time_limit > 0 && $this->submission->started_at) {
            $elapsedSeconds = now()->diffInSeconds($this->submission->started_at);
            $this->timeRemaining = max(0, ($this->quiz->time_limit * 60) - $elapsedSeconds);
        } else {
            $this->timeRemaining = 0;
        }
    }

    public function getCurrentQuestion()
    {
        return $this->questions[$this->currentQuestionIndex] ?? [];
    }

    public function getPerformanceLevel()
    {
        if (count($this->questions) == 0) return 'No Questions';

        $percentage = ($this->correctAnswers / count($this->questions)) * 100;

        if ($percentage >= 90) return 'Excellent';
        if ($percentage >= 80) return 'Very Good';
        if ($percentage >= 70) return 'Good';
        if ($percentage >= 60) return 'Fair';
        return 'Needs Improvement';
    }

    public function getEncouragementMessage()
    {
        if (count($this->questions) == 0) return 'No questions available.';

        $percentage = ($this->correctAnswers / count($this->questions)) * 100;

        if ($percentage >= 90) return 'Outstanding performance! You\'ve mastered this topic!';
        if ($percentage >= 80) return 'Great job! You have a solid understanding of the material.';
        if ($percentage >= 70) return 'Good work! Keep practicing to improve further.';
        if ($percentage >= 60) return 'Not bad! Review the materials and try again.';
        return 'Don\'t give up! Every expert was once a beginner.';
    }

    public function render()
    {
        if (!$this->isCompleted) {
            // Only check if time is up during server-side renders, don't recalculate
            if ($this->quiz->time_limit > 0 && $this->timeRemaining <= 0) {
                $this->submitQuiz();
            }
        }

        return view('livewire.start-quiz', [
            'currentQuestion' => $this->getCurrentQuestion(),
            'questions' => $this->questions,
            'submission' => $this->submission,
            'correctAnswers' => $this->correctAnswers,
            'userAnswers' => $this->userAnswers,
            'totalTimeSpent' => $this->totalTimeSpent
        ]);
    }
}
