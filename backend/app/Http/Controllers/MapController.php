<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Helpers\ShippingFeeHelper;

class MapController extends Controller
{
    /**
     * Geocode address to coordinates
     */
    public function geocode(Request $request)
    {
        \Log::info('Geocoding method called with method: ' . $request->method());
        \Log::info('Request data: ' . json_encode($request->all()));
        
        $request->validate([
            'address' => 'required|string|max:500'
        ]);

        try {
            \Log::info('Geocoding request for address: ' . $request->address);
            
            // Improve address formatting for better geocoding
            $address = $request->address;
            $lowerAddress = strtolower(trim($address));
            
            // Add timeout to prevent long waits
            $timeout = 5; // 5 seconds timeout
            
            // Handle common variations with better formatting
            if ($lowerAddress === 'cebu city' || $lowerAddress === 'cebu') {
                $address = 'Cebu City, Cebu, Philippines';
            } elseif (str_contains($lowerAddress, 'cordova') || str_contains($lowerAddress, 'corfova')) {
                $address = 'Cordova, Cebu, Philippines';
            } elseif (str_contains($lowerAddress, 'minglanilla')) {
                $address = 'Minglanilla, Cebu, Philippines';
            } elseif (str_contains($lowerAddress, 'kalawisan')) {
                $address = 'Kalawisan, Lapu-Lapu City, Cebu, Philippines';
            } elseif (str_contains($lowerAddress, 'lapu-lapu') || str_contains($lowerAddress, 'lapulapu')) {
                $address = 'Lapu-Lapu City, Cebu, Philippines';
            } elseif (str_contains($lowerAddress, 'mandaue')) {
                $address = 'Mandaue City, Cebu, Philippines';
            } elseif (str_contains($lowerAddress, 'talisay')) {
                $address = 'Talisay City, Cebu, Philippines';
            } elseif (!str_contains($lowerAddress, 'philippines')) {
                if (!str_contains($lowerAddress, 'cebu')) {
                    $address = $address . ', Cebu, Philippines';
                } else {
                    $address = $address . ', Philippines';
                }
            }
            
            $response = Http::timeout($timeout)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
                'addressdetails' => 1,
                'countrycodes' => 'ph'
            ]);

            $data = $response->json();
            \Log::info('Nominatim response: ' . json_encode($data));

            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                return response()->json([
                    'success' => true,
                    'latitude' => (float) $data[0]['lat'],
                    'longitude' => (float) $data[0]['lon'],
                    'address' => $data[0]['display_name'] ?? $request->address
                ]);
            }

            // Fallback for common Cebu locations
            $fallbackLocations = [
                'cebu city' => ['lat' => 10.3157, 'lon' => 123.8854, 'name' => 'Cebu City, Philippines'],
                'cebu' => ['lat' => 10.3157, 'lon' => 123.8854, 'name' => 'Cebu City, Philippines'],
                'mandaue' => ['lat' => 10.3333, 'lon' => 123.9333, 'name' => 'Mandaue City, Philippines'],
                'lapu-lapu' => ['lat' => 10.3103, 'lon' => 123.9494, 'name' => 'Lapu-Lapu City, Philippines'],
                'lapulapu' => ['lat' => 10.3103, 'lon' => 123.9494, 'name' => 'Lapu-Lapu City, Philippines'],
                'talisay' => ['lat' => 10.2447, 'lon' => 123.8425, 'name' => 'Talisay City, Philippines']
            ];

            $searchTerm = strtolower(trim($request->address));
            foreach ($fallbackLocations as $key => $location) {
                if (str_contains($searchTerm, $key)) {
                    \Log::info('Using fallback location for: ' . $searchTerm);
                    return response()->json([
                        'success' => true,
                        'latitude' => $location['lat'],
                        'longitude' => $location['lon'],
                        'address' => $location['name']
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error('Geocoding timeout: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Geocoding request timed out. Please try again.'
            ], 408);
        } catch (\Exception $e) {
            \Log::error('Geocoding error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Geocoding failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get route between two points
     */
    public function getRoute(Request $request)
    {
        $request->validate([
            'origin_lat' => 'required|numeric|between:-90,90',
            'origin_lng' => 'required|numeric|between:-180,180',
            'dest_lat' => 'required|numeric|between:-90,90',
            'dest_lng' => 'required|numeric|between:-180,180'
        ]);

        try {
            $url = sprintf(
                'https://router.project-osrm.org/route/v1/driving/%f,%f;%f,%f?overview=full&geometries=geojson',
                $request->origin_lng,
                $request->origin_lat,
                $request->dest_lng,
                $request->dest_lat
            );

            $timeout = 10; // 10 seconds timeout
            $response = Http::timeout($timeout)->get($url);
            $data = $response->json();

            if (isset($data['routes'][0])) {
                $route = $data['routes'][0];
                return response()->json([
                    'success' => true,
                    'distance' => $route['distance'],
                    'duration' => $route['duration'],
                    'geometry' => $route['geometry']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No route found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Routing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate shipping fee with distance
     */
    public function calculateShippingWithDistance(Request $request)
    {
        $request->validate([
            'origin_address' => 'required|string',
            'destination_address' => 'required|string'
        ]);

        try {
            \Log::info('Shipping calculation request', [
                'origin' => $request->origin_address,
                'destination' => $request->destination_address
            ]);
            
            // Use Bangbang, Cordova as the origin
            $originAddress = 'Bangbang, Cordova, Cebu';
            $shippingFee = ShippingFeeHelper::calculateShippingFee(
                $originAddress,
                $request->destination_address
            );
            
            \Log::info('Shipping fee calculated', ['shipping_fee' => $shippingFee]);

            // Calculate estimated distance for display
            $estimatedDistance = 0;
            if ($shippingFee > 30) {
                $estimatedDistance = ($shippingFee - 30) / 10; // Reverse calculate from fee
            }

            return response()->json([
                'success' => true,
                'shipping_fee' => $shippingFee,
                'distance' => round($estimatedDistance, 1),
                'origin' => $request->origin_address,
                'destination' => $request->destination_address
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping calculation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
