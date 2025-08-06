<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\AchievementService;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.user')]
class UserAchievements extends Component
{
    public $achievements = [];
    public $stats = [
        'total_achievements' => 0,
        'unlocked_achievements' => 0,
        'total_experience' => 0,
    ];

    protected AchievementService $achievementService;

    public function boot()
    {
        $this->achievementService = app(AchievementService::class);
    }

    public function mount()
    {
        $this->loadAchievements();
        $this->loadStats();
    }

    public function loadAchievements()
    {
        $this->achievements = $this->achievementService->getUserAchievements(auth()->user());
    }

    public function loadStats()
    {
        $user = auth()->user();
        
        $this->stats = [
            'total_achievements' => count($this->achievements),
            'unlocked_achievements' => count(array_filter($this->achievements, fn($a) => $a['unlocked'])),
            'total_experience' => $user->getPoints() ?? 0,
        ];
    }

    public function render()
    {
        return view('livewire.user-achievements');
    }
}