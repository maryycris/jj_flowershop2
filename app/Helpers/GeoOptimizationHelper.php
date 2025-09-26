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
            'manila' => ['Wedding', 'Corporate Event', 'Anniversary'],
            'cebu' => ['Birthday', 'Just Because', 'Wedding'],
            'davao' => ['Funeral', 'Anniversary', 'Corporate Event'],
            'baguio' => ['Wedding', 'Birthday', 'Just Because'],
            'iloilo' => ['Anniversary', 'Wedding', 'Birthday'],
        ];
        
        $normalizedCity = strtolower(str_replace(' ', '', $city));
        
        if (isset($cityPreferences[$normalizedCity])) {
            $query->whereIn('category', $cityPreferences[$normalizedCity]);
        }
        
        return $query;
    }
    
    /**
     * Apply seasonal filtering based on location
     */
    private static function applySeasonalFiltering($query, $latitude, $longitude)
    {
        $currentMonth = date('n');
        
        // Philippines seasonal preferences
        $seasonalCategories = [
            1 => ['Wedding', 'Anniversary'], // January - New Year, weddings
            2 => ['Wedding', 'Just Because'], // February - Valentine's
            3 => ['Wedding', 'Birthday'], // March - Graduation season
            4 => ['Wedding', 'Corporate Event'], // April - Summer weddings
            5 => ['Wedding', 'Anniversary'], // May - Mother's Day
            6 => ['Wedding', 'Birthday'], // June - Father's Day
            7 => ['Wedding', 'Just Because'], // July - Mid-year celebrations
            8 => ['Wedding', 'Birthday'], // August - Birthdays
            9 => ['Wedding', 'Anniversary'], // September - Fall weddings
            10 => ['Wedding', 'Birthday'], // October - Halloween
            11 => ['Wedding', 'Anniversary'], // November - Pre-holiday
            12 => ['Wedding', 'Birthday', 'Just Because'], // December - Holidays
        ];
        
        if (isset($seasonalCategories[$currentMonth])) {
            $query->whereIn('category', $seasonalCategories[$currentMonth]);
        }
        
        return $query;
    }
    
    /**
     * Get location-based services
     */
    private static function getLocationBasedServices($city)
    {
        $services = [
            'manila' => [
                'Same-day delivery available',
                'Corporate event planning',
                'Wedding consultation services',
                'Custom bouquet design'
            ],
            'cebu' => [
                'Island-wide delivery',
                'Beach wedding arrangements',
                'Tropical flower selection',
                'Event coordination'
            ],
            'davao' => [
                'Mindanao delivery network',
                'Cultural event arrangements',
                'Local flower varieties',
                'Bulk order discounts'
            ],
            'baguio' => [
                'Mountain delivery service',
                'Cool climate flower varieties',
                'Wedding venue partnerships',
                'Seasonal specials'
            ],
            'default' => [
                'Standard delivery service',
                'Custom arrangements',
                'Event consultation',
                'Quality guarantee'
            ]
        ];
        
        $normalizedCity = strtolower(str_replace(' ', '', $city));
        return $services[$normalizedCity] ?? $services['default'];
    }
    
    /**
     * Get location-based reviews
     */
    private static function getLocationBasedReviews($city)
    {
        $reviews = [
            'manila' => [
                'Excellent service in Metro Manila!',
                'Fast delivery to Makati area',
                'Beautiful arrangements for our wedding',
                'Professional team, highly recommended'
            ],
            'cebu' => [
                'Great service in Cebu City',
                'Perfect for our beach wedding',
                'Love the tropical flower selection',
                'Reliable delivery across the island'
            ],
            'davao' => [
                'Outstanding service in Davao',
                'Great for corporate events',
                'Beautiful local flower varieties',
                'Consistent quality and service'
            ],
            'default' => [
                'Excellent service and quality',
                'Beautiful flower arrangements',
                'Professional and reliable',
                'Highly recommended'
            ]
        ];
        
        $normalizedCity = strtolower(str_replace(' ', '', $city));
        return $reviews[$normalizedCity] ?? $reviews['default'];
    }
    
    /**
     * Get location-based blogs
     */
    private static function getLocationBasedBlogs($city)
    {
        $blogs = [
            'manila' => [
                'Best Wedding Venues in Metro Manila',
                'Corporate Event Flower Trends 2024',
                'Seasonal Flowers for Manila Climate',
                'Event Planning Tips for Busy Professionals'
            ],
            'cebu' => [
                'Beach Wedding Flower Guide',
                'Tropical Flower Arrangements',
                'Cebu Event Venue Recommendations',
                'Island Wedding Planning Tips'
            ],
            'davao' => [
                'Mindanao Cultural Event Flowers',
                'Local Flower Varieties Guide',
                'Davao Wedding Traditions',
                'Corporate Event Success Stories'
            ],
            'default' => [
                'Flower Care Tips',
                'Event Planning Guide',
                'Seasonal Arrangement Ideas',
                'Wedding Flower Trends'
            ]
        ];
        
        $normalizedCity = strtolower(str_replace(' ', '', $city));
        return $blogs[$normalizedCity] ?? $blogs['default'];
    }
    
    /**
     * Get location-based delivery information
     */
    private static function getLocationBasedDeliveryInfo($city)
    {
        $deliveryInfo = [
            'manila' => [
                'delivery_time' => '2-4 hours',
                'delivery_fee' => '₱150',
                'coverage' => 'Metro Manila and nearby areas',
                'special_services' => 'Same-day delivery, Rush orders'
            ],
            'cebu' => [
                'delivery_time' => '4-6 hours',
                'delivery_fee' => '₱200',
                'coverage' => 'Cebu City and surrounding areas',
                'special_services' => 'Island delivery, Beach weddings'
            ],
            'davao' => [
                'delivery_time' => '6-8 hours',
                'delivery_fee' => '₱250',
                'coverage' => 'Davao City and Mindanao',
                'special_services' => 'Regional delivery, Cultural events'
            ],
            'default' => [
                'delivery_time' => '4-6 hours',
                'delivery_fee' => '₱150',
                'coverage' => 'City-wide delivery',
                'special_services' => 'Standard delivery service'
            ]
        ];
        
        $normalizedCity = strtolower(str_replace(' ', '', $city));
        return $deliveryInfo[$normalizedCity] ?? $deliveryInfo['default'];
    }
    
    /**
     * Detect user location from IP or provided coordinates
     */
    public static function detectUserLocation($request)
    {
        $latitude = $request->get('lat');
        $longitude = $request->get('lng');
        $city = $request->get('city');
        
        // If coordinates are provided, try to get city name
        if ($latitude && $longitude && !$city) {
            $city = self::getCityFromCoordinates($latitude, $longitude);
        }
        
        // If no city provided, try to detect from IP
        if (!$city) {
            $city = self::getCityFromIP($request->ip());
        }
        
        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'city' => $city ?: 'Cebu', // Default to Cebu
            'detected' => true
        ];
    }
    
    /**
     * Get city name from coordinates (simplified version)
     */
    private static function getCityFromCoordinates($latitude, $longitude)
    {
        // This is a simplified version - in production, use Google Geocoding API
        $cities = [
            ['lat' => 14.5995, 'lng' => 120.9842, 'city' => 'Manila'],
            ['lat' => 10.3157, 'lng' => 123.8854, 'city' => 'Cebu'],
            ['lat' => 7.1907, 'lng' => 125.4553, 'city' => 'Davao'],
            ['lat' => 16.4023, 'lng' => 120.5960, 'city' => 'Baguio'],
            ['lat' => 10.7202, 'lng' => 122.5621, 'city' => 'Iloilo'],
        ];
        
        $minDistance = PHP_FLOAT_MAX;
        $closestCity = 'Cebu';
        
        foreach ($cities as $city) {
            $distance = sqrt(pow($latitude - $city['lat'], 2) + pow($longitude - $city['lng'], 2));
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestCity = $city['city'];
            }
        }
        
        return $closestCity;
    }
    
    /**
     * Get city from IP address (simplified version)
     */
    private static function getCityFromIP($ip)
    {
        // This is a simplified version - in production, use a proper IP geolocation service
        // For now, return Cebu as default
        return 'Cebu';
    }
}
