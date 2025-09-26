<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    /**
     * Geocode address to coordinates using Nominatim
     */
    public function geocode(Request $request)
    {
        try {
            $request->validate([
                'address' => 'required|string|max:500'
            ]);

            \Log::info('Geocoding request', ['address' => $request->input('address')]);

            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'J&J Flower Shop Delivery System/1.0'
                ])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $request->input('address') . ', Philippines',
                    'format' => 'json',
                    'limit' => 1,
                    'addressdetails' => 1
                ]);

            if (!$response->successful()) {
                \Log::error('Nominatim API error', ['status' => $response->status()]);
                
                // Fallback to approximate coordinates for major cities
                $fallbackCoords = $this->getFallbackCoordinates($request->input('address'));
                if ($fallbackCoords) {
                    return response()->json([
                        'success' => true,
                        'coordinates' => $fallbackCoords,
                        'display_name' => $request->input('address'),
                        'fallback' => true
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Geocoding service unavailable'
                ], 500);
            }

            $data = $response->json();
            
            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                return response()->json([
                    'success' => true,
                    'coordinates' => [
                        'lat' => (float) $data[0]['lat'],
                        'lon' => (float) $data[0]['lon']
                    ],
                    'display_name' => $data[0]['display_name'] ?? $request->input('address')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid address format'
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('Geocoding error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Geocoding service unavailable'
            ], 500);
        }
    }

    /**
     * Get route and distance using OSRM
     */
    public function getRoute(Request $request)
    {
        try {
            $request->validate([
                'origin_lat' => 'required|numeric|between:-90,90',
                'origin_lon' => 'required|numeric|between:-180,180',
                'dest_lat' => 'required|numeric|between:-90,90',
                'dest_lon' => 'required|numeric|between:-180,180'
            ]);

            $url = sprintf(
                'https://router.project-osrm.org/route/v1/driving/%f,%f;%f,%f?overview=full&geometries=geojson',
                $request->input('origin_lon'),
                $request->input('origin_lat'),
                $request->input('dest_lon'),
                $request->input('dest_lat')
            );

            \Log::info('OSRM request', ['url' => $url]);

            $response = Http::timeout(15)->get($url);
            
            if (!$response->successful()) {
                \Log::error('OSRM API error', ['status' => $response->status()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Routing service unavailable'
                ], 500);
            }

            $data = $response->json();

            if (isset($data['routes'][0])) {
                $route = $data['routes'][0];
                return response()->json([
                    'success' => true,
                    'distance' => $route['distance'], // in meters
                    'duration' => $route['duration'], // in seconds
                    'geometry' => $route['geometry'], // GeoJSON LineString
                    'distance_km' => round($route['distance'] / 1000, 2),
                    'duration_minutes' => round($route['duration'] / 60, 1)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Route not found'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coordinates'
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('OSRM routing error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Routing service unavailable'
            ], 500);
        }
    }

    /**
     * Calculate shipping fee with distance
     */
    public function calculateShippingWithDistance(Request $request)
    {
        $request->validate([
            'origin_lat' => 'required|numeric|between:-90,90',
            'origin_lon' => 'required|numeric|between:-180,180',
            'dest_lat' => 'required|numeric|between:-90,90',
            'dest_lon' => 'required|numeric|between:-180,180'
        ]);

        try {
            // Get route first
            $routeResponse = $this->getRoute($request);
            $routeData = $routeResponse->getData(true);

            if (!$routeData['success']) {
                return $routeResponse;
            }

            $distanceKm = $routeData['distance_km'];
            $baseFee = 30; // Base fee for Cordova
            $additionalRatePerKm = 12; // Rate per km outside base area

            // Calculate shipping fee
            $shippingFee = $baseFee;
            if ($distanceKm > 2) { // Free within 2km
                $shippingFee += (($distanceKm - 2) * $additionalRatePerKm);
            }

            return response()->json([
                'success' => true,
                'distance_km' => $distanceKm,
                'duration_minutes' => $routeData['duration_minutes'],
                'shipping_fee' => round($shippingFee, 2),
                'geometry' => $routeData['geometry']
            ]);

        } catch (\Throwable $e) {
            \Log::error('Shipping calculation error: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Calculation failed'
            ], 500);
        }
    }

    /**
     * Get fallback coordinates for major cities when external API fails
     */
    private function getFallbackCoordinates($address)
    {
        $normalized = strtolower($address);
        
        $fallbackCities = [
            'cebu city' => ['lat' => 10.3157, 'lon' => 123.8854],
            'mandaue' => ['lat' => 10.3236, 'lon' => 123.9221],
            'lapu-lapu' => ['lat' => 10.3103, 'lon' => 123.9494],
            'talisay' => ['lat' => 10.2447, 'lon' => 123.9633],
            'consolacion' => ['lat' => 10.3766, 'lon' => 123.9573],
            'cordova' => ['lat' => 10.3157, 'lon' => 123.8854],
            'bang-bang' => ['lat' => 10.3157, 'lon' => 123.8854],
        ];
        
        foreach ($fallbackCities as $city => $coords) {
            if (strpos($normalized, $city) !== false) {
                return $coords;
            }
        }
        
        return null;
    }
}
