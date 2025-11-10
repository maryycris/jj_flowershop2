<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Get only notifications for the current admin user
        $query = auth()->user()->notifications();
        
        // Exclude notifications that are not for admin
        // - driver_assigned_order: Only for drivers
        // - order_approved: Only for customers
        // Filter by both notification class type and data->type
        $query->where(function($q) {
            $q->where(function($subQ) {
                // Exclude by data->type
                $subQ->where(function($sq) {
                    $sq->whereJsonDoesntContain('data->type', 'driver_assigned_order')
                       ->whereJsonDoesntContain('data->type', 'order_approved');
                });
            })
            // Also exclude by notification class type
            ->where('type', '!=', 'App\\Notifications\\DriverAssignedOrderNotification')
            ->where('type', '!=', 'App\\Notifications\\OrderApprovedNotification');
        });
        
        // Simple search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                // Search in notification data (message, type, title)
                $q->where('data->message', 'like', "%{$searchTerm}%")
                  ->orWhere('data->type', 'like', "%{$searchTerm}%")
                  ->orWhere('data->title', 'like', "%{$searchTerm}%")
                  // Search by date
                  ->orWhereDate('created_at', 'like', "%{$searchTerm}%");
            });
        }
        
        // Fetch notifications with pagination
        $notifications = $query->latest()->paginate(15);
        
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $notification)
    {
        // Find the notification for the current user
        $notification = auth()->user()->notifications()->findOrFail($notification);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
} 