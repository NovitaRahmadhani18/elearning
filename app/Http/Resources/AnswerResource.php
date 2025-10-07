<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public bool $withCorrectAnswer = false;

    /**
     * Metode "fluent" untuk mengaktifkan flag dari luar.
     *
     * @return self
     */
    public function withCorrectAnswer(): self
    {
        $this->withCorrectAnswer = true;
        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'answer_text' => $this->answer_text,
            'image_path' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,

            'is_correct' => $this->when(
                $request->routeIs(['student.quizzes.review', 'teacher.quizzes.show', 'teacher.quizzes.edit', 'teacher.quizzes.preview']),
                $this->is_correct
            )

        ];
    }
}
