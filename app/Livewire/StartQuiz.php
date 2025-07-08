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
            return redirect()->route('user.classroom.quiz.show', [$classroom, $quiz])
                ->with('error', 'Quiz deadline has passed.');
        }

        // Load quiz with questions and options
        $quiz->load(['questions.options']);
        $this->classroom = $classroom;
        $this->quiz = $quiz;
        $this->questions = $quiz->questions()->with('options')->get()->toArray();
        $this->currentQuestion = $this->getCurrentQuestion();

        $this->initializeSubmission();
    }

    public function initializeSubmission()
    {
        DB::beginTransaction();
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

            // If continuing an existing submission, load progress
            if ($this->submission->answers) {
                $this->answers = $this->submission->answers;
                $this->currentQuestionIndex = count($this->answers);
                $this->currentQuestion = $this->getCurrentQuestion();
                $this->totalTimeSpent = $this->submission->time_spent;
            }
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('quiz-error', [
                'message' => 'Error starting quiz. Please try again.'
            ]);
        }
        DB::commit();
    }

    public function selectAnswer($optionId)
    {
        if ($this->isCompleted) {
            return; // Prevent further actions if the quiz is already completed
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
            $this->submission->update([
                'is_completed' => true,
                'completed_at' => now(),
                'correct_answers' => $this->correctAnswers,
                'time_spent' => $this->totalTimeSpent + $this->questionTimeSpent
            ]);

            $this->isCompleted = true;

            $currentContent = $this->quiz->contents()
                ->where('classroom_id', $this->classroom->id)
                ->firstOrFail();
            auth()->user()->completedContents()->syncWithoutDetaching($currentContent->id);
            auth()->user()->addPoints(10);

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
            $this->questionTimeSpent = now()->diffInSeconds($this->questionStartTime);
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

            // Update submission
            $this->submission->update([
                'answers' => $this->answers,
                'correct_answers' => $this->correctAnswers,
                'time_spent' => $this->totalTimeSpent + $this->questionTimeSpent
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

    public function getCurrentQuestion()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function render()
    {
        return view('livewire.start-quiz');
    }
}
