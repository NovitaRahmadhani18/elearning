<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentResource;
use App\Http\Resources\LeaderboardResource;
use App\Models\Content;
use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    public function __construct(protected LeaderboardService $leaderboardService) {}


    public function index()
    {

        $leaderboards = $this->leaderboardService->getLeaderboardsForStudent(auth()->user());

        // sort content yang paling baru dibuat
        $leaderboards = $leaderboards->sortByDesc('created_at')->values();

        return inertia('student/leaderboard/index', [
            'contentLeaderboards' => ContentResource::collection($leaderboards)
        ]);
    }
}
