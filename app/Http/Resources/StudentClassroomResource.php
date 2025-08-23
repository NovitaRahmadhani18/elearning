<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StudentClassroomResource extends JsonResource
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
            'name' => $this->name,
            'fullName' => $this->fullName,
            'category' => $this->whenLoaded('category', function () {
                return ClassroomCategoryResource::make($this->category);
            }),
            'status' => $this->whenLoaded('status', function () {
                return StatusResource::make($this->status);
            }),
            'teacher' => $this->whenLoaded('teacher', function () {
                return UserResource::make($this->teacher);
            }),
            'description' => $this->description,
            'thumbnail' => $this->when($this->thumbnail, function () {
                return Storage::disk('public')->url($this->thumbnail);
            }, null),
            'contents' => $this->whenLoaded('contents', function () {
                return ContentResource::collection($this->contents);
            }),
        ];
    }
}
