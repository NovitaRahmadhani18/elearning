<?php

namespace App\Http\Requests;

use App\Models\Content;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $quizId = $this->route('content')->contentable_id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'points' => ['required', 'integer', 'min:0'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'duration_minutes' => ['required', 'integer', 'min:1'],

            'questions' => ['required', 'array', 'min:1'],
            'questions.*.id' => ['nullable', 'integer', Rule::exists('questions', 'id')->where('quiz_id', $quizId)],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.image' => ['nullable', 'image', 'max:1024'],

            'questions.*.answers' => ['required', 'array', 'min:2', 'max:5'],
            'questions.*.answers.*.id' => ['nullable', 'integer', 'exists:answers,id'],
            'questions.*.answers.*.answer_text' => ['required_without:questions.*.answers.*.image', 'nullable', 'string', 'max:255'],
            'questions.*.answers.*.image' => ['nullable', 'image', 'max:1024'],
            'questions.*.answers.*.is_correct' => ['required', 'boolean'],
        ];
    }
}
