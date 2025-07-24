<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Cache;

class PhoneAuthController extends Controller
{
    // Send SMS code to phone number
    public function sendCode(Request $request) {
        $request->validate(['phone' => 'required']);
        $code = rand(100000, 999999);
        Cache::put('phone_verification_' . $request->phone, $code, now()->addMinutes(10));

        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = env('TWILIO_FROM');
        $client = new Client($sid, $token);
        $client->messages->create($request->phone, [
            'from' => $from,
            'body' => "Your verification code is: $code"
        ]);
        return response()->json(['message' => 'Code sent!']);
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
