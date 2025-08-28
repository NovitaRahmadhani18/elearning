<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassroomStudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'classroom' => new ClassroomResource($this->classroom),
            'student' => new UserResource($this->student),
            'progress' => $this->progress,
        ];
    }
}
