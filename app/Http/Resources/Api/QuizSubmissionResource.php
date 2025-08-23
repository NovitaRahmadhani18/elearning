<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizSubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'score' => $this->score,
            'correct_answers_count' => $this->whenNotNull($this->correct_answers_count),
            'total_questions' => $this->whenNotNull($this->total_questions),
            'accuracy' => $this->whenNotNull($this->accuracy),
            'incorrect_answers_count' => $this->whenNotNull($this->incorrect_answers_count),
            'quiz' => [
                'id' => $this->quiz->id,
                'title' => $this->quiz->content->title,
                'description' => $this->quiz->content->description,
                'duration_minutes' => $this->quiz->duration_minutes,
                'questions' => QuestionResource::collection($this->whenLoaded('quiz.questions.answers')),
            ],
            'submitted_answers' => $this->whenLoaded('answers', function () {
                return AnswerResource::collection($this->answers);
            }),
        ];
    }
}

