<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentTrackingController extends Controller
{
    public function index()
    {
        return inertia('teacher/student-tracking/index', [
            // You can pass any data needed for the view here
        ]);
    }
}
