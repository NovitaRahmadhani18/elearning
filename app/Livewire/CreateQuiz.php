<?php

namespace App\Livewire;

use App\Models\Classroom;
use App\Models\Content;
use App\Models\Quiz;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.teacher-layout')]
class CreateQuiz extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('required|string')]
    public $description = '';

    #[Validate('required|exists:classrooms,id')]
    public $classroom_id = '';

    #[Validate('required|integer|min:0')]
    public $time_limit = 300;

    public $custom_time_limit = false;

    #[Validate('required|date')]
    public $start_time = '';

    #[Validate('required|date|after_or_equal:start_time')]
    public $due_time = '';

    #[Validate('required|numeric|min:1')]
    public $point = 100;

    #[Validate('required|array|min:1')]
    public $questions = [];

    public $classrooms = [];

    // Temporary storage for uploaded images
    public $questionImages = [];
    public $optionImages = [];

    public function mount()
    {
        $this->classrooms = Classroom::pluck('title', 'id')->toArray();
        $this->start_time = now()->format('Y-m-d\TH:i');

        // Initialize with one default question
        $this->questions = [
            [
                'id' => null,
                'tempId' => 'new_' . uniqid(),
                'text' => '',
                'image_path' => null,
                'options' => [
                    ['id' => null, 'tempId' => 'new_opt_' . uniqid(), 'text' => '', 'image_path' => null],
                    ['id' => null, 'tempId' => 'new_opt_' . uniqid(), 'text' => '', 'image_path' => null]
                ],
                'correctOptionIndex' => 0
            ]
        ];
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
            $quiz = new Quiz();
            $quiz->title = $this->title;
            $quiz->description = $this->description;
            $quiz->start_time = $this->start_time;
            $quiz->due_time = $this->due_time;
            $quiz->points = $this->point;


            // Handle time limit
            if ($this->custom_time_limit) {
                $quiz->time_limit = $this->time_limit * 60; // Convert minutes to seconds
            } else {
                $quiz->time_limit = $this->time_limit; // Already in seconds
            }
            $quiz->save();

            $content = Content::create([
                'classroom_id' => $this->classroom_id,
                'contentable_type' => Quiz::class,
                'contentable_id' => $quiz->id,
            ]);
            $content->save();

            // Save questions and options
            foreach ($this->questions as $qData) {
                // Move question image from temp to permanent location
                $questionImagePath = null;
                if ($qData['image_path']) {
                    $questionImagePath = $this->moveImageToPermanentLocation($qData['image_path'], 'questions');
                }

                $question = $quiz->questions()->create([
                    'question_text' => $qData['text'],
                    'image_path' => $questionImagePath
                ]);

                foreach ($qData['options'] as $oIndex => $optionData) {
                    // Move option image from temp to permanent location
                    $optionImagePath = null;
                    if ($optionData['image_path']) {
                        $optionImagePath = $this->moveImageToPermanentLocation($optionData['image_path'], 'options');
                    }

                    $question->options()->create([
                        'option_text' => $optionData['text'],
                        'image_path' => $optionImagePath,
                        'is_correct' => ($oIndex == $qData['correctOptionIndex'])
                    ]);
                }
            }
        });

        session()->flash('success', 'Quiz created successfully.');
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
            if ($question['image_path'] && Storage::disk('public')->exists($question['image_path'])) {
                Storage::disk('public')->delete($question['image_path']);
            }

            foreach ($question['options'] as $option) {
                if ($option['image_path'] && Storage::disk('public')->exists($option['image_path'])) {
                    Storage::disk('public')->delete($option['image_path']);
                }
            }
        }

        return $this->redirect(route('teacher.quizes.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.create-quiz');
    }
}
