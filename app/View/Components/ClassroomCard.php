<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Classroom;

class ClassroomCard extends Component
{
    public $classroom;

    public function __construct(Classroom $classroom)
    {
        $this->classroom = $classroom;
    }

    public function render()
    {
        return view('components.classroom-card');
    }
}
