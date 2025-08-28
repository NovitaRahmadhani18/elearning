<?php

namespace App\Http\Controllers\Teacher;

use App\Facades\DataTable;
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
        $query = ClassroomStudent::whereHas('classroom', function ($query) {
            $query->where('teacher_id', auth()->id());
        });

        $averageCompletionRate = 0;

        if ($query->count() > 0) {
            $query->get()->each(function ($classroomStudent) use (&$averageCompletionRate) {
                $averageCompletionRate += $classroomStudent->progress;
            });
            $averageCompletionRate = $averageCompletionRate / $query->count();
            // jika bukan 0 maka dibulatkan ke 2 desimal
            if ($averageCompletionRate != 0) {
                $averageCompletionRate = round($averageCompletionRate, 2);
            } else {
                $averageCompletionRate = 0;
            }
        }



        $result = DataTable::query($query)
            ->with(['student', 'classroom'])
            ->searchable(['student.name', 'classroom.name'])
            ->make();

        return inertia('teacher/student-tracking/index', [
            'studentClassrooms' => ClassroomStudentResource::collection($result),
            'averageCompletionRate' => $averageCompletionRate,
        ]);
    }
}
