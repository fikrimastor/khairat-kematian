<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationList extends Component
{
    use WithPagination;

    public $unreadOnly = false;
    public $limit = 5;
    public $showAll = false;

    public function mount($limit = 5, $unreadOnly = false)
    {
        $this->limit = $limit;
        $this->unreadOnly = $unreadOnly;
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications->where('id', $notificationId)->first();
        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notification-read');
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->dispatch('all-notifications-read');
    }

    public function toggleShowAll()
    {
        $this->showAll = !$this->showAll;
    }

    public function render()
    {
        if ($this->unreadOnly) {
            $notifications = Auth::user()->unreadNotifications;
        } else {
            $notifications = Auth::user()->notifications;
        }
        
        if (!$this->showAll) {
            $notifications = $notifications->take($this->limit);
        }

        $unreadCount = Auth::user()->unreadNotifications->count();
        
        return view('livewire.notifications.notification-list', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
} 