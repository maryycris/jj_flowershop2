<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class ShippingFeeHelper
{
    public static function calculateShippingFee($originAddress, $destinationAddress)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $baseFee = 30;
        $additionalRatePerKm = 12;

        // Call Google Maps Distance Matrix API
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $originAddress,
            'destinations' => $destinationAddress,
            'key' => $apiKey,
        ]);

        $data = $response->json();

        if (
            isset($data['rows'][0]['elements'][0]['distance']['value']) &&
            $data['rows'][0]['elements'][0]['status'] === 'OK'
        ) {
            $distanceInMeters = $data['rows'][0]['elements'][0]['distance']['value'];
            $distanceInKm = $distanceInMeters / 1000;

            // Check if within same municipality (simple string match, you can improve this)
            if (stripos($originAddress, $destinationAddress) !== false) {
                return $baseFee;
            } else {
                return $baseFee + ($distanceInKm * $additionalRatePerKm);
            }
        } else {
            // Default/fallback fee if API fails
            return $baseFee;
        }
    }
} 