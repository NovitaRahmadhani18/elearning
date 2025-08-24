<?php

namespace App\Http\Resources;

use App\Enums\RoleEnum;
use App\Services\ContentStatusService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ClassroomResource extends JsonResource
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
            'code' => $this->code,
            'teacher' => $this->whenLoaded('teacher', function () {
                return UserResource::make($this->teacher);
            }),
            'description' => $this->description,
            'thumbnail' => $this->when($this->thumbnail, function () {
                return Storage::disk('public')->url($this->thumbnail);
            }, null),
            'invite_code' => $this->invite_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'contents' => $this->whenLoaded('contents', function () {
                return ContentResource::collection($this->contents);
            }),
            'students' => $this->whenLoaded('students', function () {
                return ClassroomStudentResource::collection($this->students);
            }),
            'studentUsers' => $this->whenLoaded('studentUsers', function () {
                return UserResource::collection($this->studentUsers);
            }),

            'progress' => $this->when(
                auth()->user()?->role === RoleEnum::STUDENT,
                function () {
                    $contents = $this->contents;

                    if ($contents->isEmpty()) {
                        return 0;
                    }

                    $contentStatuses = (new ContentStatusService(auth()->user(), $this->id))->getStatuses($contents);

                    $completedCount = $contentStatuses->filter(function ($status) {
                        return $status === 'completed';
                    })->count();

                    if ($completedCount == 0) {
                        return 0;
                    }

                    $totalCount = $contents->count();

                    return $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 2) : 0;
                },
            )
        ];
    }
}
