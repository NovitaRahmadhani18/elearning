<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $isOpen = false;
    public $notifications;
    public $unreadCount = 0;

    protected $listeners = ['refreshNotifications' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();

        if (!$user) {
            $this->notifications = collect();
            $this->unreadCount = 0;
            return;
        }

        // Get latest 10 notifications
        $this->notifications = $user->notifications()
            ->latest()
            ->limit(10)
            ->get();

        // Count unread notifications
        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications(); // Refresh the data
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    public function getTimeAgo($timestamp)
    {
        return $timestamp->diffForHumans();
    }

    public function getNotificationIcon($type)
    {
        return match ($type) {
            'quiz_completed' => 'gmdi-quiz',
            'achievement_unlocked' => 'gmdi-stars',
            'classroom_joined' => 'gmdi-school',
            'system_alert' => 'gmdi-warning',
            default => 'gmdi-notifications'
        };
    }

    public function getNotificationColor($type)
    {
        return match ($type) {
            'quiz_completed' => 'text-blue-500',
            'achievement_unlocked' => 'text-yellow-500',
            'classroom_joined' => 'text-green-500',
            'system_alert' => 'text-red-500',
            default => 'text-gray-500'
        };
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
