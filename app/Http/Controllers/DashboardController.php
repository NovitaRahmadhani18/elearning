<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
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
        return inertia('admin/dashboard-admin');
    }

    public function teacher()
    {
        return to_route('teacher.classrooms.index');
    }

    public function student()
    {
        return inertia('student/dashboard-student', []);
    }
}
