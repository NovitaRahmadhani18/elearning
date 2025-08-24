<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\AchievementResource;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AchievementController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $achievements = Achievement::with(['users' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();

        return inertia('student/achievement/index', [
            'achievements' => AchievementResource::collection($achievements),
        ]);
    }
}
