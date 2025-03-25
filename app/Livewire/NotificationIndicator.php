<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationIndicator extends Component
{
    public $unreadCount = 0;
    public $notifications = [];
    public $showDropdown = false;
    
    protected $listeners = ['refresh-notifications' => 'refreshNotifications'];
    
    public function mount()
    {
        $this->refreshNotifications();
    }
    
    public function refreshNotifications()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->unreadCount = $user->unreadNotifications->count();
            $this->notifications = $user->notifications()
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->data['type'] ?? 'notification',
                        'message' => $notification->data['message'] ?? '',
                        'read' => !is_null($notification->read_at),
                        'time' => $notification->created_at->diffForHumans(),
                    ];
                });
        }
    }
    
    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }
    
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        $this->refreshNotifications();
    }
    
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        $this->refreshNotifications();
    }
    
    public function render()
    {
        return view('livewire.notification-indicator');
    }
}
