<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentStudentResourc extends JsonResource
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
            'content_id' => $this->content_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'score' => $this->score,
            'completed_at' => $this->completed_at,
            'content' => new ContentResource($this->whenLoaded('content')),
            'user' => new UserResource($this->whenLoaded('user')),

        ];
    }
}
