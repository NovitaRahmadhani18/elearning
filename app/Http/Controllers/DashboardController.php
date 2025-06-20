<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return match (Auth::user()->getRoleNames()->first()) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'user' => redirect()->route('user.dashboard'),
        };
    }

    public function adminDashboard()
    {
        return view('pages.admin.dashboard');
    }

    public function teacherDashboard()
    {
        return redirect()->route('teacher.material.index');
    }

    public function userDashboard()
    {
        return view('pages.user.dashboard');
    }
}
