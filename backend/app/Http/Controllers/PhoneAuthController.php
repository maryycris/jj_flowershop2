<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class PhoneAuthController extends Controller
{
    // Phone OTP via SMS is disabled (no Twilio). Optionally email the code instead.
    public function sendCode(Request $request) {
        $request->validate(['phone' => 'required']);
        $code = rand(100000, 999999);
        Cache::put('phone_verification_' . $request->phone, $code, now()->addMinutes(10));

        // If the request includes an email, send the code to email as a fallback
        if ($request->filled('email')) {
            try {
                Mail::raw("Your JJ Flowershop verification code is: $code", function ($message) use ($request) {
                    $message->to($request->email)->subject('Verification Code');
                });
                return response()->json(['message' => 'Code sent to email (SMS disabled).']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to send code via email.'], 500);
            }
        }
        return response()->json(['message' => 'SMS sending is disabled. Provide email to receive the code.'], 400);
    }

    // Verify the code and log in or register the user
    public function verifyCode(Request $request) {
        $request->validate([
            'phone' => 'required',
            'code' => 'required'
        ]);
        $cachedCode = Cache::get('phone_verification_' . $request->phone);
        if ($cachedCode == $request->code) {
            $user = User::firstOrCreate(
                ['phone' => $request->phone],
                ['name' => 'User ' . $request->phone]
            );
            Auth::login($user);
            Cache::forget('phone_verification_' . $request->phone);
            return response()->json(['message' => 'Logged in!']);
        }
        return response()->json(['message' => 'Invalid code!'], 401);
    }
}
