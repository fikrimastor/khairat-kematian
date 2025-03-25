<?php

namespace App\Http\Controllers;

use App\Enums\NotificationType;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationPreferenceController extends Controller
{
    /**
     * Display the notification preferences page.
     */
    public function index(): View
    {
        return view('notifications.preferences');
    }

    /**
     * Display the notification history page.
     */
    public function history(): View
    {
        $notifications = Auth::user()->notifications()->paginate(10);
        
        return view('notifications.history', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return back()->with('status', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return back()->with('status', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function delete(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        
        return back()->with('status', 'Notification deleted.');
    }

    /**
     * Delete all notifications.
     */
    public function deleteAll()
    {
        Auth::user()->notifications()->delete();
        
        return back()->with('status', 'All notifications deleted.');
    }
}
