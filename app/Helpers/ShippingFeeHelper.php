<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class ShippingFeeHelper
{
    public static function calculateShippingFee($originAddress, $destinationAddress)
    {
        $baseFee = 30; // Flat within Cordova
        $additionalRatePerKm = 5; // Outside Cordova, add ₱5 per 2 km

        // If destination is within Cordova, always return base fee
        if (self::containsCordova($destinationAddress)) {
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
                    // Charge per started 2 km block
                    $blocksOfTwoKm = (int) ceil($distanceInKm / 2);
                    return $baseFee + ($blocksOfTwoKm * $additionalRatePerKm);
                }
            }
        } catch (\Throwable $e) {
            \Log::error('OSRM Distance calculation error: '.$e->getMessage());
        }

        // Fallback: if OSRM fails, use fallback calculation
            return self::calculateFallbackFee($destinationAddress, $baseFee, $additionalRatePerKm);
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

    private static function calculateFallbackFee($destinationAddress, $baseFee, $additionalRatePerKm)
    {
        $normalized = strtolower($destinationAddress);
        
        // Enhanced area detection with specific puroks, barangays, and streets
        $areaDistances = [
            // Cordova areas (0km - within service area)
            'cordova' => 0, 'bang-bang' => 0, 'poblacion' => 0, 'catarman' => 0, 'gabi' => 0, 
            'pilipog' => 0, 'day-as' => 0, 'buagsong' => 0, 'san miguel' => 0,
            
            // Lapu-Lapu City areas (12km from Cordova)
            'lapu-lapu' => 12, 'mactan' => 12, 'basak' => 12, 'agus' => 12, 'babag' => 12,
            'buaya' => 12, 'calawisan' => 12, 'canjulao' => 12, 'gun-ob' => 12, 'ibabao' => 12,
            'looc' => 12, 'maribago' => 12, 'marigondon' => 12, 'pajac' => 12, 'pajo' => 12,
            'punta engano' => 12, 'pusok' => 12, 'subabasbas' => 12, 'tigbao' => 12, 'tungasan' => 12,
            
            // Mandaue City areas (20km from Cordova)
            'mandaue' => 20, 'canduman' => 20, 'casili' => 20, 'casuntingan' => 20, 'centro' => 20,
            'cubacub' => 20, 'guizo' => 20, 'jagobiao' => 20, 'labogon' => 20, 'maguikay' => 20,
            'mantuyong' => 20, 'paknaan' => 20, 'pagsabungan' => 20, 'subangdaku' => 20, 'tabok' => 20,
            'tawason' => 20, 'tingub' => 20, 'tipolo' => 20, 'ubajo' => 20, 'umapad' => 20,
            
            // Cebu City areas (25km from Cordova)
            'cebu city' => 25, 'downtown' => 25, 'colon' => 25, 'ayala' => 25, 'it park' => 25,
            'as fortuna' => 25, 'banilad' => 25, 'lahug' => 25, 'capitol' => 25, 'jones' => 25,
            'fuente' => 25, 'mabolo' => 25, 'kalubihan' => 25, 'sambag' => 25, 'tejero' => 25,
            't. padilla' => 25, 'carreta' => 25, 'ermita' => 25, 'san nicolas' => 25, 'parian' => 25,
            'sto. niño' => 25, 'san roque' => 25, 'sawang calero' => 25, 'suba' => 25, 'pasil' => 25,
            'tisa' => 25, 'labangon' => 25, 'punta princesa' => 25, 'guadalupe' => 25, 'kalunasan' => 25,
            'busay' => 25, 'adlaon' => 25, 'sirao' => 25, 'pamutan' => 25, 'budlaan' => 25,
            'tabunan' => 25, 'pung-ol' => 25, 'sapangdaku' => 25, 'talamban' => 25, 'pit-os' => 25,
            'apas' => 25, 'luz' => 25, 'cambaro' => 25, 'hipodromo' => 25, 'camputhaw' => 25,
            'cogon ramos' => 25, 'cogon pardo' => 25, 'bulacao' => 25, 'inayawan' => 25,
            'poblacion pardo' => 25, 'quiot' => 25, 'kinasang-an' => 25, 'san jose' => 25,
            'basak pardo' => 25, 'mambaling' => 25, 'punta' => 25,
            
            // Talisay City areas (30km from Cordova)
            'talisay' => 30, 'biasong' => 30, 'cansojong' => 30, 'camp 4' => 30, 'candulawan' => 30,
            'carmen' => 30, 'dumlog' => 30, 'jaclupan' => 30, 'lagtang' => 30, 'lawaan' => 30,
            'linao' => 30, 'maghaway' => 30, 'manunggal' => 30, 'mohon' => 30, 'pooc' => 30,
            'san isidro' => 30, 'santander' => 30, 'tangke' => 30, 'tapul' => 30, 'tinaan' => 30,
            'tomog' => 30,
            
            // Consolacion areas (22km from Cordova)
            'consolacion' => 22, 'cabuyao' => 22, 'garing' => 22, 'pitogo' => 22, 'polo' => 22,
            'pulangbato' => 22, 'tayud' => 22, 'tilhaong' => 22, 'tugbongan' => 22, 'panoypoy' => 22
        ];
        
        foreach ($areaDistances as $area => $km) {
            if (strpos($normalized, $area) !== false) {
                $blocks = (int) ceil($km / 2);
                return $baseFee + ($blocks * $additionalRatePerKm);
            }
        }
        
        // Default for unknown areas within service area - assume 20km
        $defaultBlocks = (int) ceil(20 / 2);
        return $baseFee + ($defaultBlocks * $additionalRatePerKm);
    }

    private static function containsCordova(string $address): bool
    {
        $normalized = strtolower($address);
        // match common variants
        return strpos($normalized, 'cordova') !== false;
    }
} 