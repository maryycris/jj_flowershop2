<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventStatusChanged;

class EventNotificationController extends Controller
{
    /**
     * Send notification when event status changes
     */
    public static function sendStatusChangeNotification(Event $event, $newStatus, $oldStatus = null)
    {
        $customer = $event->user;
        
        // If oldStatus is not provided, use the current status from the event
        if ($oldStatus === null) {
            $oldStatus = $event->getOriginal('status') ?? 'pending';
        }
        
        // Send email notification
        try {
            Mail::to($customer->email)->send(new EventStatusChanged($event, $oldStatus, $newStatus));
        } catch (\Exception $e) {
            \Log::error('Failed to send event notification email: ' . $e->getMessage());
        }
        
        // Store notification in database for in-app notifications
        self::storeNotification($event, $oldStatus, $newStatus);
    }
    
    /**
     * Store notification in database
     */
    private static function storeNotification(Event $event, $oldStatus, $newStatus)
    {
        $customer = $event->user;
        
        // Use Laravel's notification system
        $customer->notify(new \App\Notifications\EventStatusChanged($event, $oldStatus, $newStatus));
    }
    
    /**
     * Get status change message
     */
    private static function getStatusChangeMessage(Event $event, $oldStatus, $newStatus)
    {
        $statusMessages = [
            'pending' => 'is pending approval',
            'confirmed' => 'has been confirmed',
            'completed' => 'has been completed',
            'cancelled' => 'has been cancelled'
        ];
        
        $eventType = $event->event_type;
        $eventDate = \Carbon\Carbon::parse($event->event_date)->format('M d, Y');
        
        return "Your {$eventType} event scheduled for {$eventDate} {$statusMessages[$newStatus]}.";
    }
    
    /**
     * Show notifications index page
     */
    public function index()
    {
        return view('customer.notifications.index');
    }

    /**
     * Get user notifications
     */
    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([]);
        }

        try {
            $notifications = \DB::table('notifications')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
        } catch (\Throwable $e) {
            \Log::error('Failed to load notifications: ' . $e->getMessage());
            return response()->json([]);
        }
        
        return response()->json($notifications);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        
        \DB::table('notifications')
            ->where('id', $notificationId)
            ->where('user_id', $user->id)
            ->update(['read_at' => now()]);
            
        return response()->json(['success' => true]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        \DB::table('notifications')
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return response()->json(['success' => true]);
    }
}
