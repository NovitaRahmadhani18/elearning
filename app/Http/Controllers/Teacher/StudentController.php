<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\ClassroomStudent;
use App\Models\ContentUser;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // count all student in the classroom that the teacher is teaching
        $studentCount = auth()->user()->classrooms()
            ->withCount('students')
            ->get()
            ->sum('students_count');

        // get all count contents in the classroom that the teacher is teaching
        $contentCount = auth()->user()->classrooms()
            ->withCount('contents')
            ->get()
            ->sum('contents_count');

        // get all completed contents by students in the classroom that the teacher is teaching
        $contentUserCount = ContentUser::query()
            ->whereHas('content', function ($query) {
                $query->whereIn('classroom_id', auth()->user()->classrooms()->pluck('id'));
            })
            ->count();

        // then get rate of completed contents by students
        $completionRate = $contentCount > 0 ? ($contentUserCount / $contentCount) * 100 : 0;

        $completionRate = $completionRate > 0 ? round($completionRate, 2) : 0;

        // classrooms ids that the teacher is teaching
        $classroomIds = auth()->user()->classrooms()->pluck('id');

        $query = ClassroomStudent::query()
            ->with(['user', 'classroom'])
            ->whereIn('classroom_id', $classroomIds)
            ->select('classroom_user.*')
            ->latest();

        $tableData = \App\CustomClasses\TableData::make(
            $query,
            [
                \App\CustomClasses\Column::make('user.name', 'student name'),
                \App\CustomClasses\Column::make('classroom.title', 'classroom'),
                \App\CustomClasses\Column::make('progress', 'progress')
                    ->setView('reusable-table.column.progress'),
                \App\CustomClasses\Column::make('progress', 'completion')
                    ->setView('reusable-table.column.completion'),
            ],
            perPage: request('perPage', 10),
            id: 'student-content-table',
        );

        return view('pages.teacher.student.index', compact('studentCount', 'contentCount', 'contentUserCount', 'completionRate', 'tableData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
