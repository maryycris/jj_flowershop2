<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = DatabaseNotification::query();
        
        // Simple search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                // Search in notification data (message, type)
                $q->where('data->message', 'like', "%{$searchTerm}%")
                  ->orWhere('data->type', 'like', "%{$searchTerm}%")
                  // Search by date
                  ->orWhereDate('created_at', 'like', "%{$searchTerm}%");
            });
        }
        
        // Fetch notifications with pagination
        $notifications = $query->latest()->paginate(15);
        
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
} 