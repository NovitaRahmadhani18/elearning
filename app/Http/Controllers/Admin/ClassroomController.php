<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AchievementResource;
use App\Http\Resources\ClassroomResource;
use App\Http\Resources\ClassroomStudentResource;
use App\Http\Resources\ContentStudentResourc;
use App\Http\Resources\UserResource;
use App\Models\Achievement;
use App\Models\Classroom;
use App\Models\ClassroomStudent;
use App\Models\ContentStudent;
use App\Services\ClassroomService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassroomController extends Controller
{
    public function __construct(protected ClassroomService $classroomService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('admin/classroom/index', [
            'classrooms' => $this->classroomService->index()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('admin/classroom/create', [
            'categories' => $this->classroomService->getClassroomCategories(),
            'teachers' => $this->classroomService->getTeachers(),
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
                'teacher_id' => ['required', 'integer', 'exists:users,id'],
                'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB max
            ]
        );

        try {
            $classroom = $this->classroomService->createClassroom($data);
            if ($classroom) {
                return to_route('admin.classrooms.index')->with('success', 'Classroom created successfully!');
            } else {
                return to_route('admin.classrooms.create')->with('error', 'Failed to create classroom.')->withInput();
            }
        } catch (\Throwable $th) {
            return to_route('admin.classrooms.create')->with('error', 'Failed to create classroom: ' . $th->getMessage())->withInput();
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

        return inertia('admin/classroom/show', [
            'classroom' => ClassroomResource::make($classroom),
        ]);
    }


    public function showStudent(Classroom $classroom, $studentId)
    {
        $student = $classroom->studentUsers()
            ->where('users.id', $studentId)->firstOrFail();

        $classroomStudent = ClassroomStudent::query()
            ->with(['classroom', 'student'])
            ->where('classroom_id', $classroom->id)
            ->where('student_id', $student->id)
            ->first();

        $achievements = Achievement::with(['users' => function ($query) use ($student) {
            $query->where('user_id', $student->id);
        }])->get();

        $contentStudents = ContentStudent::whereIn('content_id', $classroom->contents->pluck('id'))
            ->where('user_id', $student->id)
            ->with(['content'])
            ->orderBy('completed_at', 'desc')
            ->get();

        return inertia('admin/classroom/show-student', [
            'classroom' => ClassroomResource::make($classroom),
            'student' => UserResource::make($student),
            'classroomStudent' => $classroomStudent ? ClassroomStudentResource::make($classroomStudent) : null,
            'achievements' => AchievementResource::collection($achievements),
            'contentStudents' => ContentStudentResourc::collection($contentStudents),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom)
    {
        return inertia('admin/classroom/edit', [
            'classroom' => ClassroomResource::make($classroom),
            'categories' => $this->classroomService->getClassroomCategories(),
            'teachers' => $this->classroomService->getTeachers(),
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
                'teacher_id' => ['required', 'integer', 'exists:users,id'],
                'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB max
            ]
        );

        try {
            $updated = $this->classroomService->updateClassroom($classroom, $data);
            if ($updated) {
                return to_route('admin.classrooms.index')->with('success', 'Classroom updated successfully!');
            } else {
                return to_route('admin.classrooms.edit', $classroom)->with('error', 'Failed to update classroom.')->withInput();
            }
        } catch (\Throwable $th) {
            return to_route('admin.classrooms.edit', $classroom)->with('error', 'Failed to update classroom: ' . $th->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        try {
            $this->classroomService->deleteClassroom($classroom);
            return to_route('admin.classrooms.index')->with('success', 'Classroom deleted successfully!');
        } catch (\Throwable $th) {
            return to_route('admin.classrooms.index')->with('error', 'Failed to delete classroom: ' . $th->getMessage());
        }
    }
}
