<?php

namespace App\Http\Resources;

use App\Enums\RoleEnum;
use App\Facades\DataTable;
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
            'students_count' => $this->when(
                isset($this->students_count),
                $this->students_count
            ),

            'studentUsers' => $this->whenLoaded('studentUsers', function () use ($request) {

                if ($request->routeIs('teacher.classrooms.show') || $request->routeIs('admin.classrooms.show')) {
                    $result = $this->studentUsers()
                        ->where('role', RoleEnum::STUDENT)
                        ->when($request->has('search'), function ($query) use ($request) {
                            $search = $request->input('search');
                            $query->where(function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                        });

                    return UserResource::collection($result->get());
                }
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
