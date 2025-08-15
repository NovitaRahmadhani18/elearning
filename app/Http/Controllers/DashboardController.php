<?php

namespace App\Http\Controllers;

use App\Models\ContentUser;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __construct(protected AchievementService $achievementService) {}


    public function index()
    {
        return match (Auth::user()->getRoleNames()->first()) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'user' => redirect()->route('user.dashboard'),
        };
    }

    public function dashboard()
    {
        return match (Auth::user()->getRoleNames()->first()) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'user' => redirect()->route('user.dashboard'),
        };
    }

    public function adminDashboard()
    {
        $totalUsers = \App\Models\User::count();
        $totalClassrooms = \App\Models\Classroom::count();
        $totalCompletedContents = ContentUser::count();

        $activities = Activity::query()
            ->with('causer', 'subject')
            ->latest()
            ->take(10)
            ->get();

        return view('pages.admin.dashboard', compact('totalUsers', 'totalClassrooms', 'totalCompletedContents', 'activities'));
    }

    public function teacherDashboard()
    {
        return redirect()->route('teacher.classroom.index');
    }

    public function userDashboard()
    {
        $classrooms = auth()->user()->classrooms()
            ->with([
                'teacher',
                'contents',
                'quizzes',
                'materials'
            ])
            ->withCount(['contents', 'quizzes', 'materials'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($classrooms as $classroom) {
            $realProgress = auth()->user()->getClassroomProgress($classroom->id);

            // Update pivot table with real progress
            auth()->user()->classrooms()->updateExistingPivot($classroom->id, [
                'progress' => $realProgress
            ]);

            // Update the current object to reflect the new progress
            $classroom->pivot->progress = $realProgress;
        }

        $classroomInProgress = $classrooms->filter(function ($classroom) {
            return $classroom->pivot->progress < 100;
        });

        $classroomCompleted = $classrooms->filter(function ($classroom) {
            return $classroom->pivot->progress >= 100;
        });

        $upcomingQuizzes = auth()->user()->upcomingQuizzes();

        $achievements = $this->achievementService->getUserAchievements(auth()->user());

        return view('pages.user.dashboard', compact(
            'classrooms',
            'classroomInProgress',
            'classroomCompleted',
            'upcomingQuizzes',
            'achievements'
        ));
    }
}
