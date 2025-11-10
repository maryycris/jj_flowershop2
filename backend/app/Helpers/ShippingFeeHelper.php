<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class ShippingFeeHelper
{
    public static function calculateShippingFee($originAddress, $destinationAddress)
    {
        $baseFee = 30; // P30.00 within Cordova
        $additionalRatePerKm = 10; // P10.00 per kilometer beyond Cordova

        \Log::info('Shipping calculation debug', [
            'origin' => $originAddress,
            'destination' => $destinationAddress,
            'contains_cordova' => self::containsCordova($destinationAddress)
        ]);

        // If destination is within Cordova, always return base fee
        if (self::containsCordova($destinationAddress)) {
            \Log::info('Address contains Cordova, returning base fee', ['fee' => $baseFee]);
            return $baseFee;
        }

        // Try to get coordinates and calculate distance using OSRM
        try {
            $originCoords = self::geocodeAddress($originAddress);
            $destCoords = self::geocodeAddress($destinationAddress);
            
            if ($originCoords && $destCoords) {
                $distance = self::getDistanceFromOSRM($originCoords, $destCoords);
                if ($distance > 0) {
                    $distanceInKm = $distance / 1000.0;
                    // Add P10.00 for every kilometer beyond Cordova
                    $additionalFee = $distanceInKm * $additionalRatePerKm;
                    $totalFee = $baseFee + $additionalFee;
                    \Log::info('OSRM calculation result', [
                        'distance_km' => $distanceInKm,
                        'additional_fee' => $additionalFee,
                        'total_fee' => $totalFee
                    ]);
                    return $totalFee;
                }
            }
        } catch (\Throwable $e) {
            \Log::error('OSRM Distance calculation error: '.$e->getMessage());
        }

        // Fallback: if OSRM fails, use fallback calculation
        $fallbackFee = self::calculateFallbackFee($destinationAddress, $baseFee, $additionalRatePerKm);
        \Log::info('Using fallback calculation', ['fee' => $fallbackFee]);
        return $fallbackFee;
    }

    /**
     * Geocode address to coordinates using Nominatim (OpenStreetMap)
     */
    private static function geocodeAddress($address)
    {
        try {
            $response = Http::get('https://nominatim.openstreetmap.org/search', [
                'q' => $address . ', Philippines',
                'format' => 'json',
                'limit' => 1,
                'addressdetails' => 1
            ]);

            $data = $response->json();

            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                return [
                    'lat' => (float) $data[0]['lat'],
                    'lon' => (float) $data[0]['lon']
                ];
            }
        } catch (\Throwable $e) {
            \Log::error('Geocoding error: '.$e->getMessage());
        }
        
        return null;
    }

    /**
     * Get driving distance using OSRM API
     */
    private static function getDistanceFromOSRM($origin, $destination)
    {
        try {
            $url = sprintf(
                'https://router.project-osrm.org/route/v1/driving/%f,%f;%f,%f?overview=false',
                $origin['lon'],
                $origin['lat'],
                $destination['lon'],
                $destination['lat']
            );

            $response = Http::get($url);
            $data = $response->json();

            if (isset($data['routes'][0]['distance'])) {
                return (float) $data['routes'][0]['distance'];
            }
        } catch (\Throwable $e) {
            \Log::error('OSRM API error: '.$e->getMessage());
        }
        
        return 0;
    }

    /**
     * Check if address contains Cordova (including common typo "corfova")
     */
    private static function containsCordova($address)
    {
        $address = strtolower($address);
        \Log::info('Checking if address contains Cordova', [
            'address' => $address,
            'contains_cordova' => strpos($address, 'cordova') !== false,
            'contains_corfova' => strpos($address, 'corfova') !== false
        ]);
        return strpos($address, 'cordova') !== false || strpos($address, 'corfova') !== false;
    }

    /**
     * Calculate fallback shipping fee based on address keywords
     * Using estimated distances from Cordova to different areas
     */
    private static function calculateFallbackFee($destinationAddress, $baseFee, $additionalRatePerKm)
    {
        $address = strtolower($destinationAddress);
        
        // Specific areas with more accurate distances from Bangbang, Cordova
        
        // Minglanilla - approximately 25-30km from Cordova
        if (strpos($address, 'minglanilla') !== false) {
            $estimatedKm = 28; // Distance to Minglanilla
            return $baseFee + ($estimatedKm * $additionalRatePerKm);
        }
        
        // Kalawisan, Lapu-Lapu - approximately 12-15km from Cordova
        if (strpos($address, 'kalawisan') !== false) {
            $estimatedKm = 13; // Distance to Kalawisan
            return $baseFee + ($estimatedKm * $additionalRatePerKm);
        }
        
        // Lapu-Lapu City - approximately 8-12km from Cordova
        if (strpos($address, 'lapu-lapu') !== false || 
            strpos($address, 'lapulapu') !== false) {
            $estimatedKm = 10; // Average distance to Lapu-Lapu
            return $baseFee + ($estimatedKm * $additionalRatePerKm);
        }
        
        // Mandaue City - approximately 12-15km from Cordova
        if (strpos($address, 'mandaue') !== false) {
            $estimatedKm = 14; // Average distance to Mandaue
            return $baseFee + ($estimatedKm * $additionalRatePerKm);
        }
        
        // Talisay City - approximately 20-25km from Cordova
        if (strpos($address, 'talisay') !== false) {
            $estimatedKm = 22; // Average distance to Talisay
            return $baseFee + ($estimatedKm * $additionalRatePerKm);
        }
        
        // Cebu City (general) - approximately 15-20km from Cordova
        if (strpos($address, 'cebu city') !== false) {
            $estimatedKm = 18; // Average distance to Cebu City
            return $baseFee + ($estimatedKm * $additionalRatePerKm);
        }
        
        // Other Cebu areas - use a moderate estimated distance
        if (strpos($address, 'cebu') !== false) {
            $estimatedKm = 20; // Conservative estimate for other Cebu areas
            return $baseFee + ($estimatedKm * $additionalRatePerKm);
        }
        
        // Other areas - use a higher estimated distance
        $estimatedKm = 25; // Conservative estimate for other areas
        return $baseFee + ($estimatedKm * $additionalRatePerKm);
    }
}