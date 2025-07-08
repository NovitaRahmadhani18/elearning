<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\ClassroomStudent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $classrooms = \App\Models\Classroom::orderBy('created_at', 'desc')
            ->with(['teacher'])
            ->get();

        return view('pages.admin.classroom.index', compact('classrooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = User::role('teacher')->get();
        return view('pages.admin.classroom.create', compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'max_students' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|max:2048',
            'teacher_id' => 'required|exists:users,id',
        ]);

        DB::transaction(function () use ($request) {
            $data = $request->only(['title', 'description', 'category', 'teacher_id', 'max_students']);

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('classroom_thumbnails', 'public');
                $data['thumbnail_path'] = $path;
            }

            \App\Models\Classroom::create($data);
        });

        return to_route('admin.classroom.index')->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        $query = ClassroomStudent::query()
            ->where('classroom_id', $classroom->id)
            ->with(['user'])
            ->latest();

        $studentsTableData = \App\CustomClasses\TableData::make(
            $query,
            [
                \App\CustomClasses\Column::make('user', 'Student')
                    ->setView('reusable-table.column.user-card'),
                \App\CustomClasses\Column::make('user.email', 'Email'),
                \App\CustomClasses\Column::make('created_at', 'Joined At')
                    ->setView('reusable-table.column.date-yyyy'),
            ],
            perPage: request('perPage', 10),
            id: 'classroom-students-table',
        );

        $contents = $classroom->contents()->with(['contentable'])->get();

        return view('pages.admin.classroom.show', compact('classroom', 'studentsTableData', 'contents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\Classroom $classroom)
    {
        $teachers = User::role('teacher')->get();
        return view('pages.admin.classroom.edit', compact('classroom', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, \App\Models\Classroom $classroom)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'max_students' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|max:2048',
            'teacher_id' => 'required|exists:users,id',
        ]);

        DB::transaction(function () use ($request, $classroom) {
            $data = $request->only(['title', 'description', 'category', 'max_students', 'teacher_id']);

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('classroom_thumbnails', 'public');
                $data['thumbnail_path'] = $path;
            }

            $classroom->update($data);
        });

        return to_route('admin.classroom.index')->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function syncStudents(Request $request, Classroom $classroom)
    {
        $request->validate([
            'students' => 'nullable|array',
            'students.*' => 'exists:users,id',
        ]);

        $classroom->students()->sync($request->input('students', []));

        return back()->with('success', 'Students updated successfully.');
    }
}
