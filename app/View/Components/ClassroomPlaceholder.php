<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ClassroomPlaceholder extends Component
{
    public $color;
    public $icon;
    public $title;
    public $category;

    public function __construct($color, $icon, $title, $category = 'Classroom')
    {
        $this->color = $color;
        $this->icon = $icon;
        $this->title = $title;
        $this->category = $category;
    }

    public function render()
    {
        return view('components.classroom-placeholder');
    }
}
