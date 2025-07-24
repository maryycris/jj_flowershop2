<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index()
    {
        // Fetch all notifications from the database, eager loading the notifiable (e.g., User)
        $notifications = DatabaseNotification::latest()->paginate(10);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
} 