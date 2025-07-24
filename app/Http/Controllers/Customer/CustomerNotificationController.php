<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerNotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(10);
        return view('customer.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    public function destroyAll()
    {
        $user = Auth::user();
        $user->notifications()->delete();
        return response()->json(['success' => true]);
    }

    public function markAsUnread($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification && $notification->read_at) {
            $notification->update(['read_at' => null]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
}
