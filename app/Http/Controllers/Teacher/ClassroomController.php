<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassroomResource;
use App\Models\Classroom;
use App\Services\ClassroomService;
use App\Http\Resources\ContentResource;
use App\Http\Resources\UserResource;
use App\Models\Material;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function __construct(protected ClassroomService $classroomService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('teacher/classroom/index', [
            'classrooms' => $this->classroomService->index(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('teacher/classroom/create', [
            'categories' => $this->classroomService->getClassroomCategories(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'category_id' => ['required', 'integer', 'exists:classrooms_categories,id'],
                'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB max
            ]
        );

        try {
            $classroom = $this->classroomService->createClassroom($data);
            if ($classroom) {
                return to_route('teacher.classrooms.index')->with('success', 'Classroom created successfully!');
            } else {
                return to_route('teacher.classrooms.create')->withErrors(['error' => 'Failed to create classroom.'])->withInput();
            }
        } catch (\Throwable $th) {
            return to_route('teacher.classrooms.create')->withErrors(['error' => 'Failed to create classroom: ' . $th->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        $classroom->load([
            'students',
            'contents',
            'studentUsers'
        ]);

        return inertia('teacher/classroom/show', [
            'classroom' => ClassroomResource::make($classroom),
        ]);
    }

    public function showStudent(Classroom $classroom, $studentId)
    {
        $student = $classroom->studentUsers()->where('users.id', $studentId)->firstOrFail();

        return inertia('teacher/classroom/show-student', [
            'classroom' => ClassroomResource::make($classroom),
            'student' => UserResource::make($student),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {

        return inertia('teacher/classroom/edit', [
            'classroom' => ClassroomResource::make($classroom),
            'categories' => $this->classroomService->getClassroomCategories(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        $data = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'category_id' => ['required', 'integer', 'exists:classrooms_categories,id'],
                'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB max
            ]
        );

        try {
            $updated = $this->classroomService->updateClassroom($classroom, $data);
            if ($updated) {
                return to_route('teacher.classrooms.index')->with('success', 'Classroom updated successfully!');
            } else {
                return to_route('teacher.classrooms.edit', $classroom)->withErrors('error', 'Failed to update classroom.')->withInput();
            }
        } catch (\Throwable $th) {
            return to_route('teacher.classrooms.edit', $classroom)->withErrors('error', 'Failed to update classroom: ' . $th->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        // cek apakah classroom memiliki teacher_id yang sama dengan login
        if ($classroom->teacher_id != auth()->id()) {
            return to_route('teacher.classrooms.index')->withErrors('error', 'Failed to delete classroom');
        }

        try {
            $this->classroomService->deleteClassroom($classroom);
            return to_route('teacher.classrooms.index')->with('success', 'Classroom deleted successfully!');
        } catch (\Throwable $th) {
            return to_route('teacher.classrooms.index')->withErrors('error', 'Failed to delete classroom: ' . $th->getMessage());
        }
    }

    public function generateInviteCode(Classroom $classroom)
    {
        try {
            $inviteCode = $this->classroomService->regenerateInviteCode($classroom);
            return back()->with('success', 'Invite code generated successfully: ' . $inviteCode);
        } catch (\Throwable $th) {
            return back()->withErrors('error', 'Failed to generate invite code: ' . $th->getMessage());
        }
    }

    public function generateCode(Classroom $classroom)
    {
        try {
            $code = $this->classroomService->regenerateCode($classroom);
            return  back()->with('success', 'Classroom code regenerated successfully: ' . $code);
        } catch (\Throwable $th) {
            return back()->withErrors('error', 'Failed to regenerate classroom code: ' . $th->getMessage());
        }
    }
}
