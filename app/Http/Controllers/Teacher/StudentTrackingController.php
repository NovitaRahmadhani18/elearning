<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassroomStudentResource;
use App\Models\Classroom;
use App\Models\ClassroomStudent;
use Illuminate\Http\Request;

class StudentTrackingController extends Controller
{
    public function index()
    {
        // ambil semua siswa yang diajar oleh guru
        $students = ClassroomStudent::with(['student', 'classroom'])
            ->whereHas('classroom', function ($query) {
                $query->where('teacher_id', auth()->id());
            })
            ->get();

        return inertia('teacher/student-tracking/index', [
            'studentClassrooms' => ClassroomStudentResource::collection($students),
        ]);
    }
}
