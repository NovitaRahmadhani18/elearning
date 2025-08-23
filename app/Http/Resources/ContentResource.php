<?php

namespace App\Http\Resources;

use App\Models\Material;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $contentableResource = null;
        if ($this->contentable_type === Material::class) {
            $contentableResource = new MaterialResource($this->whenLoaded('contentable'));
        } elseif ($this->contentable_type === Quiz::class) {
            $contentableResource = new QuizResource($this->whenLoaded('contentable'));
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'points' => $this->points,
            'order' => $this->order,
            'type' => $this->contentable_type === Material::class ? 'material' : 'quiz',
            'classroom_id' => $this->classroom_id,
            'classroom' => new ClassroomResource($this->whenLoaded('classroom')),
            'details' => $contentableResource,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
