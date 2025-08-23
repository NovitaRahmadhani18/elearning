<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\QuizResource;
use App\Http\Resources\SubmissionAnswerResource;
use App\Services\ContentService;
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
        $stats = $this->when(
            $this->relationLoaded('answers') && $this->relationLoaded('quiz') && $this->quiz->relationLoaded('questions'),
            function () {
                return resolve(ContentService::class)->calculateSubmissionStats($this->resource);
            },
            []
        );

        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'started_at' => $this->started_at,
            'duration_seconds' => $this->duration_seconds,
            'completed_at' => $this->completed_at,

            $this->merge($stats),

            'quiz' => $this->whenLoaded('quiz', function () {
                return new QuizResource($this->quiz);
            }),

            'submitted_answers' => SubmissionAnswerResource::collection($this->whenLoaded('answers')),
        ];
    }
}
