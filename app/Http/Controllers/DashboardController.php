<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Resources\AchievementResource;
use App\Http\Resources\ContentResource;
use App\Models\Achievement;
use App\Models\Classroom;
use App\Models\StudentPoint;
use App\Models\User;
use App\Services\ClassroomService;
use App\Services\ContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function __construct(protected ClassroomService $classroomService) {}


    public function index()
    {
        $role = request()->user()->role;

        if ($role === RoleEnum::ADMIN) {
            return to_route('admin.dashboard');
        } elseif ($role === RoleEnum::TEACHER) {
            return $this->teacher($role);
        } elseif ($role === RoleEnum::STUDENT) {
            return to_route('student.dashboard');
        }
    }

    public function dashboard()
    {
        return $this->index();
    }

    public function admin()
    {

        $classroomCount = Classroom::count();
        $totalUserCount = User::count();
        $completionCount = StudentPoint::count();

        return inertia(
            'admin/dashboard-admin',
            [
                'classroomCount' => $classroomCount,
                'totalUserCount' => $totalUserCount,
                'completionCount' => $completionCount,
            ]
        );
    }

    public function teacher()
    {
        return to_route('teacher.classrooms.index');
    }

    public function student()
    {
        $upcomingContents = app(ContentService::class)->getUpcommingContents(auth()->user());

        $user = Auth::user();

        $achievements = Achievement::with(['users' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();


        return inertia('student/dashboard-student', [
            'classrooms' => $this->classroomService->index(),
            'achievements' => AchievementResource::collection($achievements),
            'upcomingContents' => ContentResource::collection($upcomingContents),
        ]);
    }
}
