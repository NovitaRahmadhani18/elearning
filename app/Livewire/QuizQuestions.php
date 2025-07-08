<?php

namespace App\Livewire;

use Livewire\Component;

class QuizQuestions extends Component
{
    public $questions = [];
    public $quiz = null;
    public $isEditMode = false;

    public function mount($questions = null, $quiz = null)
    {
        $this->quiz = $quiz;
        $this->isEditMode = !is_null($quiz);
        
        // Check if questions is a non-empty array with actual data
        if ($questions && is_array($questions) && count($questions) > 0 && !empty(array_filter($questions))) {
            // Normalize the questions data structure
            $this->questions = collect($questions)->map(function ($question) {
                return [
                    'id' => $question['id'] ?? null,
                    'tempId' => $question['tempId'] ?? 'existing_' . ($question['id'] ?? uniqid()),
                    'text' => $question['text'] ?? '',
                    'options' => collect($question['options'] ?? [])->map(function ($option) {
                        return [
                            'id' => $option['id'] ?? null,
                            'tempId' => $option['tempId'] ?? 'existing_opt_' . ($option['id'] ?? uniqid()),
                            'text' => $option['text'] ?? ''
                        ];
                    })->toArray(),
                    'correctOptionIndex' => $question['correctOptionIndex'] ?? $question['correct_option_index'] ?? 0
                ];
            })->toArray();
        } else {
            // Initialize with default question structure
            $this->questions = [
                [
                    'id' => null,
                    'tempId' => 'new_' . uniqid(),
                    'text' => '',
                    'options' => [
                        ['id' => null, 'tempId' => 'new_opt_' . uniqid(), 'text' => ''],
                        ['id' => null, 'tempId' => 'new_opt_' . uniqid(), 'text' => '']
                    ],
                    'correctOptionIndex' => 0
                ]
            ];
        }
    }

    public function updated($propertyName)
    {
        // This will trigger Alpine.js watcher automatically via @entangle
    }

    public function addQuestion()
    {
        $this->questions[] = [
            'id' => null,
            'tempId' => 'new_' . uniqid(),
            'text' => '',
            'options' => [
                ['id' => null, 'tempId' => 'new_opt_' . uniqid(), 'text' => ''],
                ['id' => null, 'tempId' => 'new_opt_' . uniqid(), 'text' => '']
            ],
            'correctOptionIndex' => 0
        ];
    }

    public function removeQuestion($questionIndex)
    {
        if (count($this->questions) > 1) {
            unset($this->questions[$questionIndex]);
            $this->questions = array_values($this->questions); // Re-index array
        }
    }

    public function addOption($questionIndex)
    {
        if (isset($this->questions[$questionIndex])) {
            $this->questions[$questionIndex]['options'][] = [
                'id' => null,
                'tempId' => 'new_opt_' . uniqid(),
                'text' => ''
            ];
        }
    }

    public function removeOption($questionIndex, $optionIndex)
    {
        if (isset($this->questions[$questionIndex]) && 
            isset($this->questions[$questionIndex]['options']) && 
            count($this->questions[$questionIndex]['options']) > 2) {
            
            unset($this->questions[$questionIndex]['options'][$optionIndex]);
            $this->questions[$questionIndex]['options'] = array_values($this->questions[$questionIndex]['options']); // Re-index array
            
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

    public function getQuestionsForSubmission()
    {
        return $this->questions;
    }

    public function getQuestionsProperty()
    {
        return $this->questions;
    }

    public function render()
    {
        return view('livewire.quiz-questions', [
            'questionsJson' => json_encode($this->questions)
        ]);
    }
}
