<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassroomResource;
use App\Http\Resources\ContentResource;
use App\Http\Resources\StudentClassroomResource;
use App\Models\Classroom;
use App\Services\ClassroomService;
use App\Services\ContentStatusService;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{

    public function __construct(protected ClassroomService $classroomService) {}


    public function index()
    {
        $classrooms = $this->classroomService->index();

        return inertia('student/classrooms/index', [
            'classrooms' => $classrooms
        ]);
    }

    public function joinForm(Classroom $classroom)
    {
        return inertia('student/classrooms/join', [
            'classroom' => ClassroomResource::make($classroom),
        ]);
    }

    public function join(Request $request, Classroom $classroom)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
        ]);

        if ($classroom->code !== $data['code']) {
            return to_route('student.classrooms.join.form', ['classroom' => $classroom->invite_code])
                ->withErrors(['code' => 'The provided code does not match the classroom code.'])
                ->withInput();
        }

        if ($this->classroomService->isMember($classroom, auth()->user())) {
            return to_route('student.classrooms.show', $classroom->id)
                ->withErrors(['error' => 'You are already a member of this classroom.']);
        }

        try {
            $this->classroomService->joinClassroom($classroom, auth()->user());
            return to_route('student.classrooms.show', $classroom->id)->with('success', 'Successfully joined the classroom!');
        } catch (\Throwable $th) {
            return to_route('student.classrooms.join.form', ['classroom' => $classroom->id])
                ->withErrors(['error' => 'Failed to join classroom: ' . $th->getMessage()])
                ->withInput();
        }
    }

    public function show(Classroom $classroom)
    {
        if (! $this->classroomService->isMember($classroom, auth()->user())) {
            return to_route('student.classrooms.index')
                ->withErrors(['error' => 'You are not a member of this classroom.']);
        }

        $classroom->load(['teacher', 'category', 'status']);
        $contents = $classroom->contents()->orderBy('order', 'asc')->get();
        $contentStatuses = (new ContentStatusService(auth()->user(), $classroom->id))->getStatuses($contents);

        // Add the status to each content model before passing to the resource
        $contents->each(function ($content) use ($contentStatuses) {
            $content->status = $contentStatuses->get($content->id, 'locked');
        });

        return inertia('student/classrooms/show', [
            'classroom' => StudentClassroomResource::make($classroom),
            'contents' => ContentResource::collection($contents),
        ]);
    }


    public function joinDirectClassroom(Request $request)
    {
        $validated = $request->validate([
            'classroom_code' => ['required', 'string', 'exists:classrooms,code'],
        ]);

        $classroom = Classroom::query()->where('code', $validated['classroom_code'])->firstOrFail();

        if ($this->classroomService->isMember($classroom, auth()->user())) {
            return back()->withErrors(['error' => 'You are already a member of this classroom.']);
        }

        try {
            $this->classroomService->joinClassroom($classroom, auth()->user());
            return to_route('student.classrooms.show', $classroom->id)->with('success', 'Successfully joined the classroom!');
        } catch (\Throwable $th) {
            return back()->withErrors(['error' => 'Failed to join classroom: ' . $th->getMessage()])->withInput();
        }
    }
}
