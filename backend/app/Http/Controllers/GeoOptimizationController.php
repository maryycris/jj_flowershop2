<?php

namespace App\Http\Controllers;

use App\Helpers\GeoOptimizationHelper;
use Illuminate\Http\Request;

class GeoOptimizationController extends Controller
{
    /**
     * Get location-based recommendations
     */
    public function getLocationBasedContent(Request $request)
    {
        $location = GeoOptimizationHelper::detectUserLocation($request);
        
        $products = GeoOptimizationHelper::getLocationBasedProducts(
            $location['latitude'],
            $location['longitude'],
            $location['city'],
            8
        );
        
        $content = GeoOptimizationHelper::getLocationBasedContent(
            $location['latitude'],
            $location['longitude'],
            $location['city']
        );
        
        return response()->json([
            'success' => true,
            'location' => $location,
            'products' => $products,
            'content' => $content
        ]);
    }
    
    /**
     * Update user location
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'city' => 'nullable|string|max:255'
        ]);
        
        $location = GeoOptimizationHelper::detectUserLocation($request);
        
        // Store location in session
        session([
            'user_location' => $location,
            'geo_optimized' => true
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'location' => $location
        ]);
    }
    
    /**
     * Get location-based products for homepage
     */
    public function getHomepageProducts(Request $request)
    {
        $location = session('user_location', GeoOptimizationHelper::detectUserLocation($request));
        
        $products = GeoOptimizationHelper::getLocationBasedProducts(
            $location['latitude'] ?? null,
            $location['longitude'] ?? null,
            $location['city'] ?? null,
            6
        );
        
        return response()->json([
            'success' => true,
            'products' => $products,
            'location' => $location
        ]);
    }
}
