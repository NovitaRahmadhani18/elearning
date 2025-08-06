<?php

namespace App\Livewire;

use App\Models\Classroom;
use App\Models\Quiz;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.teacher-layout')]
class EditQuiz extends Component
{
    use WithFileUploads;

    public Quiz $quiz;

    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('required|string')]
    public $description = '';

    #[Validate('required|integer|min:0')]
    public $time_limit = 300;

    public $custom_time_limit = false;

    #[Validate('required|date')]
    public $start_time = '';

    #[Validate('required|date|after_or_equal:start_time')]
    public $due_time = '';

    #[Validate('required|array|min:1')]
    public $questions = [];

    // Temporary storage for uploaded images
    public $questionImages = [];
    public $optionImages = [];

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz;

        if ($quiz->start_time && $quiz->start_time->isPast()) {
            session()->flash('error', 'Quiz cannot be edited as the start time is in the past.');
            return $this->redirect(route('teacher.quizes.index'));
        }

        if ($quiz->due_time && $quiz->due_time->isPast()) {
            session()->flash('error', 'Quiz cannot be edited as the due time is in the past.');
            return $this->redirect(route('teacher.quizes.index'));
        }

        // Load quiz data
        $this->title = $quiz->title;
        $this->description = $quiz->description;
        $this->start_time = $quiz->start_time->format('Y-m-d\TH:i');
        $this->due_time = $quiz->due_time->format('Y-m-d\TH:i');

        // Handle time limit
        $predefinedLimits = [5, 10, 30, 60];
        if (in_array($quiz->time_limit, $predefinedLimits)) {
            $this->time_limit = $quiz->time_limit;
            $this->custom_time_limit = false;
        } else {
            $this->time_limit = $quiz->time_limit; // Convert seconds to minutes
            $this->custom_time_limit = true;
        }

        // Load questions
        $quiz->load('questions.options');
        $this->questions = $quiz->questions->map(function ($q) {
            return [
                'id' => $q->id,
                'tempId' => 'existing_' . $q->id,
                'text' => $q->question_text,
                'image_path' => $q->image_path,
                'options' => $q->options->map(function ($o) {
                    return [
                        'id' => $o->id,
                        'tempId' => 'existing_opt_' . $o->id,
                        'text' => $o->option_text,
                        'image_path' => $o->image_path
                    ];
                })->toArray(),
                'correctOptionIndex' => $q->options->search(fn($o) => $o->is_correct)
            ];
        })->toArray();
    }

    public function addQuestion()
    {
        $this->questions[] = [
            'id' => null,
            'tempId' => 'new_' . uniqid(),
            'text' => '',
            'image_path' => null,
            'options' => [
                ['id' => null, 'tempId' => 'new_opt_' . uniqid(), 'text' => '', 'image_path' => null],
                ['id' => null, 'tempId' => 'new_opt_' . uniqid(), 'text' => '', 'image_path' => null]
            ],
            'correctOptionIndex' => 0
        ];
    }

    public function removeQuestion($questionIndex)
    {
        if (count($this->questions) > 1) {
            // Remove associated images
            $this->removeQuestionImage($questionIndex);
            foreach ($this->questions[$questionIndex]['options'] as $oIndex => $option) {
                $this->removeOptionImage($questionIndex, $oIndex);
            }

            unset($this->questions[$questionIndex]);
            $this->questions = array_values($this->questions);
        }
    }

    public function addOption($questionIndex)
    {
        if (isset($this->questions[$questionIndex])) {
            $this->questions[$questionIndex]['options'][] = [
                'id' => null,
                'tempId' => 'new_opt_' . uniqid(),
                'text' => '',
                'image_path' => null
            ];
        }
    }

    public function removeOption($questionIndex, $optionIndex)
    {
        if (
            isset($this->questions[$questionIndex]) &&
            isset($this->questions[$questionIndex]['options']) &&
            count($this->questions[$questionIndex]['options']) > 2
        ) {
            // Remove associated image
            $this->removeOptionImage($questionIndex, $optionIndex);

            unset($this->questions[$questionIndex]['options'][$optionIndex]);
            $this->questions[$questionIndex]['options'] = array_values($this->questions[$questionIndex]['options']);

            // Adjust correct option index if needed
            $currentCorrectIndex = $this->questions[$questionIndex]['correctOptionIndex'] ?? 0;
            if ($currentCorrectIndex >= count($this->questions[$questionIndex]['options'])) {
                $this->questions[$questionIndex]['correctOptionIndex'] = count($this->questions[$questionIndex]['options']) - 1;
            }
        }
    }

    public function setCorrectOption($questionIndex, $optionIndex)
    {
        if (isset($this->questions[$questionIndex])) {
            $this->questions[$questionIndex]['correctOptionIndex'] = $optionIndex;
        }
    }

    public function updatedCustomTimeLimit()
    {
        if (!$this->custom_time_limit) {
            $this->time_limit = 300; // Reset to default 5 minutes
        }
    }

    public function updatedQuestionImages($value, $key)
    {
        $this->validate([
            "questionImages.$key" => 'image|max:2048', // 2MB max
        ]);

        if ($value) {
            // Remove old image if exists
            if (isset($this->questions[$key]['image_path']) && $this->questions[$key]['image_path']) {
                $oldPath = $this->questions[$key]['image_path'];
                if (strpos($oldPath, 'temp/') === false && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Store the uploaded file temporarily
            $path = $value->store('temp/questions', 'public');
            $this->questions[$key]['image_path'] = $path;
        }
    }

    public function updatedOptionImages($value, $key)
    {
        $keys = explode('.', $key);
        $questionIndex = $keys[0];
        $optionIndex = $keys[1];

        $this->validate([
            "optionImages.$key" => 'image|max:2048', // 2MB max
        ]);

        if ($value) {
            // Remove old image if exists
            if (
                isset($this->questions[$questionIndex]['options'][$optionIndex]['image_path']) &&
                $this->questions[$questionIndex]['options'][$optionIndex]['image_path']
            ) {
                $oldPath = $this->questions[$questionIndex]['options'][$optionIndex]['image_path'];
                if (strpos($oldPath, 'temp/') === false && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Store the uploaded file temporarily
            $path = $value->store('temp/options', 'public');
            $this->questions[$questionIndex]['options'][$optionIndex]['image_path'] = $path;
        }
    }

    public function removeQuestionImage($questionIndex)
    {
        if (isset($this->questions[$questionIndex]['image_path'])) {
            $imagePath = $this->questions[$questionIndex]['image_path'];
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $this->questions[$questionIndex]['image_path'] = null;
        }

        // Clear the file input
        if (isset($this->questionImages[$questionIndex])) {
            unset($this->questionImages[$questionIndex]);
        }
    }

    public function removeOptionImage($questionIndex, $optionIndex)
    {
        if (isset($this->questions[$questionIndex]['options'][$optionIndex]['image_path'])) {
            $imagePath = $this->questions[$questionIndex]['options'][$optionIndex]['image_path'];
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $this->questions[$questionIndex]['options'][$optionIndex]['image_path'] = null;
        }

        // Clear the file input
        $key = "$questionIndex.$optionIndex";
        if (isset($this->optionImages[$key])) {
            unset($this->optionImages[$key]);
        }
    }

    public function save()
    {
        $this->validate();
        // Custom validation for questions
        $this->validate([
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|string',
            'questions.*.correctOptionIndex' => 'required|integer',
        ]);

        DB::transaction(function () {
            // Update quiz data
            $this->quiz->title = $this->title;
            $this->quiz->description = $this->description;
            $this->quiz->start_time = $this->start_time;
            $this->quiz->due_time = $this->due_time;

            // Handle time limit
            if ($this->custom_time_limit) {
                $this->quiz->time_limit = $this->time_limit * 60; // Convert minutes to seconds
            } else {
                $this->quiz->time_limit = $this->time_limit; // Already in seconds
            }

            $this->quiz->save();

            // Sync questions
            $submittedQuestionIds = [];
            foreach ($this->questions as $qData) {
                // Handle question image
                $questionImagePath = $qData['image_path'];
                if ($questionImagePath && strpos($questionImagePath, 'temp/') !== false) {
                    $questionImagePath = $this->moveImageToPermanentLocation($questionImagePath, 'questions');
                }

                $questionData = [
                    'question_text' => $qData['text'],
                    'image_path' => $questionImagePath
                ];

                // Update or create question
                $question = $this->quiz->questions()->updateOrCreate(
                    ['id' => $qData['id'] ?? null],
                    $questionData
                );

                $submittedOptionIds = [];
                // Sync options for each question
                foreach ($qData['options'] as $oIndex => $optionData) {
                    // Handle option image
                    $optionImagePath = $optionData['image_path'];
                    if ($optionImagePath && strpos($optionImagePath, 'temp/') !== false) {
                        $optionImagePath = $this->moveImageToPermanentLocation($optionImagePath, 'options');
                    }

                    $option = $question->options()->updateOrCreate(
                        ['id' => $optionData['id'] ?? null],
                        [
                            'option_text' => $optionData['text'],
                            'image_path' => $optionImagePath,
                            'is_correct' => ($oIndex == $qData['correctOptionIndex'])
                        ]
                    );
                    $submittedOptionIds[] = $option->id;
                }

                // Delete options that are no longer submitted
                $deletedOptions = $question->options()->whereNotIn('id', $submittedOptionIds)->get();
                foreach ($deletedOptions as $deletedOption) {
                    if ($deletedOption->image_path && Storage::disk('public')->exists($deletedOption->image_path)) {
                        Storage::disk('public')->delete($deletedOption->image_path);
                    }
                }
                $question->options()->whereNotIn('id', $submittedOptionIds)->delete();

                $submittedQuestionIds[] = $question->id;
            }

            // Delete questions that are no longer submitted
            $deletedQuestions = $this->quiz->questions()->whereNotIn('id', $submittedQuestionIds)->get();
            foreach ($deletedQuestions as $deletedQuestion) {
                if ($deletedQuestion->image_path && Storage::disk('public')->exists($deletedQuestion->image_path)) {
                    Storage::disk('public')->delete($deletedQuestion->image_path);
                }
                // Delete associated option images too
                foreach ($deletedQuestion->options as $option) {
                    if ($option->image_path && Storage::disk('public')->exists($option->image_path)) {
                        Storage::disk('public')->delete($option->image_path);
                    }
                }
            }
            $this->quiz->questions()->whereNotIn('id', $submittedQuestionIds)->delete();
        });

        session()->flash('success', 'Quiz updated successfully.');
        return $this->redirect(route('teacher.quizes.index'));
    }

    private function moveImageToPermanentLocation($tempPath, $type)
    {
        if (!$tempPath || !Storage::disk('public')->exists($tempPath)) {
            return null;
        }

        $filename = basename($tempPath);
        $permanentPath = "quiz-images/$type/" . $filename;

        Storage::disk('public')->move($tempPath, $permanentPath);

        return $permanentPath;
    }

    public function cancel()
    {
        // Clean up temporary images
        foreach ($this->questions as $question) {
            if (
                $question['image_path'] && strpos($question['image_path'], 'temp/') !== false &&
                Storage::disk('public')->exists($question['image_path'])
            ) {
                Storage::disk('public')->delete($question['image_path']);
            }

            foreach ($question['options'] as $option) {
                if (
                    $option['image_path'] && strpos($option['image_path'], 'temp/') !== false &&
                    Storage::disk('public')->exists($option['image_path'])
                ) {
                    Storage::disk('public')->delete($option['image_path']);
                }
            }
        }

        return $this->redirect(route('teacher.quizes.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.edit-quiz');
    }
}
