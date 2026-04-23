<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $isMobile = false;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function getListeners()
    {
        $userId = Auth::id();
        return [
            "echo-private:App.Models.User.{$userId},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'notificationReceived',
        ];
    }

    public function notificationReceived($event)
    {
        $this->loadNotifications();
        
        if (!$this->isMobile) {
            $this->dispatch('play-notification-sound');
            $this->dispatch('show-browser-notification', [
                'title' => 'Brag',
                'message' => $event['message'] ?? 'You have a new notification.',
                'url' => $event['action_url'] ?? '#'
            ]);
        }
    }

    public function loadNotifications()
    {
        if (Auth::check()) {
            $this->notifications = Auth::user()->notifications()->take(10)->get();
            $this->unreadCount = Auth::user()->unreadNotifications()->count();
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    public function markAllAsReadAndRedirect()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->route('notifications.index');
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
