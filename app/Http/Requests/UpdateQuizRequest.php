<?php

namespace App\Http\Requests;

use App\Models\Content;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class UpdateQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $quizId = $this->route('content')->contentable_id;
        $content = $this->route('content');

        // Ambil start_time asli quiz
        $originalStartTime = Carbon::parse($content->contentable->start_time);
        $now = Carbon::now();

        // Logika: jika start_time asli > sekarang (belum lewat), minimal adalah now
        // Jika start_time asli <= sekarang (sudah lewat), minimal adalah start_time asli
        $minStartTime = $originalStartTime->greaterThan($now) ? $now : $originalStartTime;

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'points' => ['required', 'integer', 'min:0'],
            'start_time' => ['required', 'date', 'after_or_equal:' . $minStartTime->toDateTimeString()],
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

    public function messages(): array
    {
        $content = $this->route('content');
        $originalStartTime = Carbon::parse($content->contentable->start_time);
        $now = Carbon::now();
        $minStartTime = $originalStartTime->greaterThan($now) ? $now : $originalStartTime;

        return [
            'start_time.after_or_equal' => $originalStartTime->greaterThan($now)
                ? 'Start time cannot be in the past. Please select today or a future date.'
                : 'Start time cannot be earlier than the original start time (' . $originalStartTime->format('d M Y, H:i') . ').',
            'end_time.after_or_equal' => 'End time must be equal to or after the start time.',
        ];
    }
}
