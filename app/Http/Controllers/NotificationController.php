<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('pages.notifications', compact('notifications'));
    }

    /** AJAX endpoint for the navbar bell dropdown — latest 8, unread count. */
    public function recent()
    {
        $user = Auth::user();

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $user->notifications()->limit(8)->get(),
        ]);
    }

    public function markRead(\App\Models\Notification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);
        $notification->markAsRead();

        return $notification->action_url
            ? redirect($notification->action_url)
            : back();
    }

    public function markAllRead()
    {
        $count = Auth::user()->markAllNotificationsRead();
        return response()->json(['marked_read' => $count]);
    }
}
