<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Facades\DataTable;
use App\Http\Resources\ClassroomResource;
use App\Http\Resources\UserResource;
use App\Models\Classroom;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClassroomService
{
    /**
     * Get the list of classroom categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    static public function getClassroomCategories()
    {
        return \App\Models\ClassroomCategory::all();
    }

    /**
     * Get the list of statuses.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    static public function getStatuses()
    {
        return \App\Models\Status::all();
    }

    static public function getTeachers()
    {
        return UserResource::collection(
            \App\Models\User::where('role', 'teacher')->get()
        );
    }

    public function index()
    {
        $query = \App\Models\Classroom::query()
            ->when(auth()->user()->role === RoleEnum::TEACHER, function ($query) {
                return $query->where('teacher_id', auth()->id());
            })
            ->when(auth()->user()->role === RoleEnum::STUDENT, function ($query) {
                return $query->whereHas('students', function ($q) {
                    $q->where('student_id', auth()->id());
                });
            });

        $result =  DataTable::query($query)
            ->searchable(['name', 'description'])
            ->make();

        return ClassroomResource::collection($result);
    }

    public function createClassroom(array $data): Classroom
    {
        return DB::transaction(function () use ($data) {

            $thumbnailPath = null;
            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
                $thumbnailPath = $data['thumbnail']->store('class-thumbnails', 'public');
            }

            $code = $this->generateUniqueCode();
            $inviteCode = $this->generateInviteCode();

            $status = Status::where('value', 'active')->firstOrFail();

            $classroom = Classroom::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'category_id' => $data['category_id'],
                'thumbnail' => $thumbnailPath,
                'teacher_id' => $data['teacher_id'] ?? auth()->id(),
                'code' => $code,
                'invite_code' => $inviteCode,
                'status_id' => $status->id,
            ]);


            $classroom->save();

            return $classroom;
        });
    }

    /**
     * Generate a unique 8-character code for the classroom.
     *
     * @return string
     */
    private function generateUniqueCode(): string
    {
        $code = Str::upper(Str::random(8));

        return $code;
    }

    private function generateInviteCode(): string
    {
        $code = Str::upper(Str::random(8));

        // Ensure the code is unique
        while (Classroom::where('invite_code', $code)->exists()) {
            $code = Str::upper(Str::random(8));
        }

        return $code;
    }

    public function updateClassroom(Classroom $classroom, array $data): Classroom
    {
        return DB::transaction(function () use ($classroom, $data) {

            // Handle thumbnail update
            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
                // Hapus thumbnail lama jika ada
                if ($classroom->thumbnail) {
                    Storage::disk('public')->delete($classroom->thumbnail);
                }
                // Simpan thumbnail baru
                $data['thumbnail'] = $data['thumbnail']->store('class-thumbnails', 'public');

                $classroom->thumbnail = $data['thumbnail'];
            }

            $classroom->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'teacher_id' => $data['teacher_id'] ?? auth()->id(),
                'category_id' => $data['category_id'],
            ]);

            $classroom->save();

            return $classroom;
        });
    }

    public function deleteClassroom(Classroom $classroom): bool
    {
        return DB::transaction(function () use ($classroom) {
            // Hapus thumbnail jika ada
            if ($classroom->thumbnail) {
                Storage::disk('public')->delete($classroom->thumbnail);
            }

            return $classroom->delete();
        });
    }

    public function regenerateInviteCode(Classroom $classroom): string
    {
        $inviteCode = $this->generateInviteCode();

        // Update the classroom with the new invite code
        $classroom->invite_code = $inviteCode;
        $classroom->save();

        return $inviteCode;
    }

    public function regenerateCode(Classroom $classroom): string
    {
        $code = $this->generateUniqueCode();

        // Update the classroom with the new code
        $classroom->code = $code;
        $classroom->save();

        return $code;
    }

    public function joinClassroom(Classroom $classroom, User $user): Classroom
    {
        $user->classrooms()->attach($classroom);

        return $classroom;
    }

    static public function isMember(Classroom $classroom, User $user): bool
    {
        return $user->classrooms()->where('classroom_id', $classroom->id)->exists();
    }
}
