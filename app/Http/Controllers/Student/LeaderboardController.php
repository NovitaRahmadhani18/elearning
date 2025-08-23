<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    public function __construct(protected LeaderboardService $leaderboardService) {}



    public function index()
    {

        return inertia('student/leaderboard/index', []);
    }
}

