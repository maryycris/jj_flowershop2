<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerNotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->get();
        return view('customer.notifications.index', compact('notifications'));
    }

    public function list()
    {
        $notifications = auth()->user()->notifications()->latest()->get();
        return response()->json($notifications);
    }

    public function markAllAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'All notifications marked as read.']);
        }
        
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroyAll()
    {
        auth()->user()->notifications()->delete();
        return back()->with('success', 'All notifications deleted.');
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Notification marked as read.']);
        }
        
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAsUnread($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsUnread();
        return back()->with('success', 'Notification marked as unread.');
    }

    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }
}
