<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $messages = Message::where('sender_id', $user->id)
                          ->orWhere('receiver_id', $user->id)
                          ->orderBy('created_at', 'asc')
                          ->get();

        return view('customer.chat.index', compact('messages'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        
        // Get admin and clerk users to notify
        $adminUsers = User::where('role', 'admin')->get();
        $clerkUsers = User::where('role', 'clerk')->get();
        
        // Create messages for admin and clerk
        foreach ($adminUsers as $admin) {
            Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $admin->id,
                'message' => $request->message,
                'is_read' => false,
            ]);
        }
        
        foreach ($clerkUsers as $clerk) {
            Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $clerk->id,
                'message' => $request->message,
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully'
        ]);
    }

    public function getMessages()
    {
        $user = Auth::user();
        $messages = Message::where('sender_id', $user->id)
                          ->orWhere('receiver_id', $user->id)
                          ->orderBy('created_at', 'asc')
                          ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }
} 