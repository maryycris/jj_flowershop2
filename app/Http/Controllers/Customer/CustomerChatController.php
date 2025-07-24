<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;

class CustomerChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $customer = Auth::user();
        $messageText = $request->input('message');

        // Get all admins and clerks
        $recipients = User::whereIn('role', ['admin', 'clerk'])->get();
        foreach ($recipients as $recipient) {
            Message::create([
                'sender_id' => $customer->id,
                'receiver_id' => $recipient->id,
                'message' => $messageText,
            ]);
        }

        return response()->json(['success' => true]);
    }
} 