<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class GeoOptimizationHelper
{
    /**
     * Get location-based product recommendations
     */
    public static function getLocationBasedProducts($latitude = null, $longitude = null, $city = null, $limit = 8)
    {
        $cacheKey = "geo_products_{$latitude}_{$longitude}_{$city}_{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($latitude, $longitude, $city, $limit) {
            $query = Product::where('status', true)->where('is_approved', true);
            
            // Location-based filtering
            if ($city) {
                $query = self::applyCityBasedFiltering($query, $city);
            }
            
            // Seasonal recommendations based on location
            $query = self::applySeasonalFiltering($query, $latitude, $longitude);
            
            return $query->inRandomOrder()->limit($limit)->get();
        });
    }
    
    /**
     * Get location-based content (blogs, services, etc.)
     */
    public static function getLocationBasedContent($latitude = null, $longitude = null, $city = null)
    {
        $cacheKey = "geo_content_{$latitude}_{$longitude}_{$city}";
        
        return Cache::remember($cacheKey, 3600, function () use ($latitude, $longitude, $city) {
            $content = [
                'services' => self::getLocationBasedServices($city),
                'reviews' => self::getLocationBasedReviews($city),
                'blogs' => self::getLocationBasedBlogs($city),
                'delivery_info' => self::getLocationBasedDeliveryInfo($city),
            ];
            
            return $content;
        });
    }
    
    /**
     * Apply city-based filtering for products
     */
    private static function applyCityBasedFiltering($query, $city)
    {
        $cityPreferences = [
            'cebu city' => ['roses', 'lilies', 'tulips'],
            'mandaue' => ['sunflowers', 'carnations'],
            'lapu-lapu' => ['orchids', 'roses'],
            'talisay' => ['mixed bouquets', 'seasonal flowers'],
        ];
        
        $preferredCategories = $cityPreferences[strtolower($city)] ?? [];
        
        if (!empty($preferredCategories)) {
            foreach ($preferredCategories as $category) {
                $query->orWhere('name', 'like', "%{$category}%")
                      ->orWhere('description', 'like', "%{$category}%");
            }
        }
        
        return $query;
    }
    
    /**
     * Apply seasonal filtering based on location
     */
    private static function applySeasonalFiltering($query, $latitude, $longitude)
    {
        $month = date('n');
        
        // Tropical climate considerations for Philippines
        if ($latitude && $longitude) {
            // Check if location is in tropical region
            if ($latitude >= 5 && $latitude <= 20) {
                // Tropical flowers for warm climate
                $query->where(function($q) {
                    $q->where('name', 'like', '%hibiscus%')
                      ->orWhere('name', 'like', '%bougainvillea%')
                      ->orWhere('name', 'like', '%frangipani%');
                });
            }
        }
        
        return $query;
    }
    
    /**
     * Get location-based services
     */
    private static function getLocationBasedServices($city)
    {
        $services = [
            'cebu city' => [
                'Same-day delivery',
                'Wedding arrangements',
                'Corporate events'
            ],
            'mandaue' => [
                'Express delivery',
                'Custom bouquets'
            ],
            'lapu-lapu' => [
                'Airport delivery',
                'Hotel arrangements'
            ]
        ];
        
        return $services[strtolower($city)] ?? ['Standard delivery'];
    }
    
    /**
     * Get location-based reviews
     */
    private static function getLocationBasedReviews($city)
    {
        // This would typically query a reviews table
        return [
            'average_rating' => 4.5,
            'total_reviews' => 150,
            'recent_reviews' => [
                'Great service in ' . ucfirst($city),
                'Fast delivery to ' . ucfirst($city),
                'Beautiful flowers for ' . ucfirst($city) . ' events'
            ]
        ];
    }
    
    /**
     * Get location-based blogs
     */
    private static function getLocationBasedBlogs($city)
    {
        return [
            'title' => 'Flower Care Tips for ' . ucfirst($city),
            'content' => 'Learn how to care for your flowers in the ' . ucfirst($city) . ' climate...',
            'image' => 'blog-' . strtolower($city) . '.jpg'
        ];
    }
    
    /**
     * Get location-based delivery information
     */
    private static function getLocationBasedDeliveryInfo($city)
    {
        $deliveryInfo = [
            'cebu city' => [
                'delivery_time' => '2-4 hours',
                'free_delivery_minimum' => 500,
                'special_services' => ['Same-day', 'Express']
            ],
            'mandaue' => [
                'delivery_time' => '3-5 hours',
                'free_delivery_minimum' => 600,
                'special_services' => ['Express']
            ],
            'lapu-lapu' => [
                'delivery_time' => '4-6 hours',
                'free_delivery_minimum' => 700,
                'special_services' => ['Airport delivery']
            ]
        ];
        
        return $deliveryInfo[strtolower($city)] ?? [
            'delivery_time' => '4-6 hours',
            'free_delivery_minimum' => 500,
            'special_services' => ['Standard delivery']
        ];
    }
    
    /**
     * Detect user location from request
     */
    public static function detectUserLocation($request)
    {
        // Try to get from request parameters first
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $city = $request->input('city');
        
        // If not provided, try to get from session
        if (!$latitude || !$longitude) {
            $sessionLocation = session('user_location');
            if ($sessionLocation) {
                $latitude = $sessionLocation['latitude'] ?? null;
                $longitude = $sessionLocation['longitude'] ?? null;
                $city = $sessionLocation['city'] ?? null;
            }
        }
        
        // Fallback to default location (Cebu)
        return [
            'latitude' => $latitude ?? 10.3157,
            'longitude' => $longitude ?? 123.8854,
            'city' => $city ?? 'Cebu City',
            'detected' => !empty($latitude) && !empty($longitude)
        ];
    }
}
