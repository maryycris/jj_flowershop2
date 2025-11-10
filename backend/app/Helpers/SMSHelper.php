<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SMSHelper
{
    public static function sendSMS($mobile, $code)
    {
        $apiKey = env('SEMAPHORE_API_KEY');
        $message = "Your JJ Flower Shop verification code is: $code";
        try {
            $response = Http::post('https://api.semaphore.co/api/v4/messages', [
                'apikey' => $apiKey,
                'number' => $mobile,
                'message' => $message,
                'sendername' => 'JJFLOWERS'
            ]);
            $result = $response->json();
            return isset($result[0]['status']) && $result[0]['status'] == 'Queued';
        } catch (\Exception $e) {
            return false;
        }
    }
}
