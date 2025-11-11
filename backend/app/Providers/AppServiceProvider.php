<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure view paths to point to frontend/resources/views
        $frontendViewsPath = base_path('../frontend/resources/views');
        if (file_exists($frontendViewsPath)) {
            // Replace the view paths entirely with the frontend path
            $viewFinder = View::getFinder();
            $viewFinder->setPaths([$frontendViewsPath]);
        }
        
        // Configure public path to point to root public directory
        $this->app->bind('path.public', function () {
            return base_path('../public');
        });
        
        // Configure filesystem to use Cloudinary if credentials are available
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        
        // Always ensure local storage is the default first
        config(['filesystems.disks.public.driver' => 'local']);
        config(['filesystems.default' => 'local']);
        
        if ($cloudName && $apiKey && $apiSecret) {
            // Update Cloudinary disk config with correct key names
            // DO NOT use CLOUDINARY_URL if it's set to the app URL (common mistake)
            $cloudinaryUrl = env('CLOUDINARY_URL');
            $isValidUrl = false;
            
            if ($cloudinaryUrl) {
                // Check if it's a valid Cloudinary URL (must start with cloudinary://)
                if (str_starts_with($cloudinaryUrl, 'cloudinary://')) {
                    $isValidUrl = true;
                    \Log::info('Using CLOUDINARY_URL from environment');
                } else {
                    // If CLOUDINARY_URL is set but not a valid Cloudinary URL, ignore it
                    \Log::warning('CLOUDINARY_URL is set but invalid, ignoring it', [
                        'url_preview' => substr($cloudinaryUrl, 0, 50) . '...',
                        'note' => 'CLOUDINARY_URL must start with cloudinary://'
                    ]);
                    $cloudinaryUrl = null;
                }
            }
            
            // Always set individual credentials (more reliable)
            // Start with a fresh config array to avoid any URL contamination
            $cloudinaryConfig = [
                'driver' => 'cloudinary',
                'cloud' => $cloudName,
                'key' => $apiKey,
                'secret' => $apiSecret,
                'secure' => true,
            ];
            
            // Only set URL if it's a valid Cloudinary URL
            // If URL is invalid or not set, DO NOT include it at all
            if ($isValidUrl && $cloudinaryUrl) {
                $cloudinaryConfig['url'] = $cloudinaryUrl;
                \Log::info('Cloudinary config: Using CLOUDINARY_URL', ['url_preview' => substr($cloudinaryUrl, 0, 30) . '...']);
            } else {
                // DO NOT set URL key at all - this forces use of individual credentials
                \Log::info('Cloudinary config: Using individual credentials (cloud, key, secret)', [
                    'cloud' => $cloudName,
                    'key_set' => !empty($apiKey),
                    'secret_set' => !empty($apiSecret),
                    'note' => 'URL key not set - will use individual credentials'
                ]);
            }
            
            // Set the complete config at once - this overwrites any previous config
            config(['filesystems.disks.cloudinary' => $cloudinaryConfig]);
            
            // Double-check: ensure URL is not set if invalid
            $finalConfig = config('filesystems.disks.cloudinary');
            if (isset($finalConfig['url']) && !$isValidUrl) {
                unset($finalConfig['url']);
                config(['filesystems.disks.cloudinary' => $finalConfig]);
                \Log::warning('Removed invalid URL from Cloudinary config after setting', [
                    'had_url' => true
                ]);
            }
            
            // Enable Cloudinary as the default driver for permanent image storage
            try {
                // IMPORTANT: Clear the 'url' field from public disk config
                // The public disk originally has 'url' => env('APP_URL').'/storage'
                // This URL will be passed to Cloudinary constructor if not cleared!
                $publicDiskConfig = config('filesystems.disks.public', []);
                unset($publicDiskConfig['url']); // Remove APP_URL from public disk config
                $publicDiskConfig['driver'] = 'cloudinary';
                // Copy Cloudinary config to public disk
                $publicDiskConfig['cloud'] = $cloudName;
                $publicDiskConfig['key'] = $apiKey;
                $publicDiskConfig['secret'] = $apiSecret;
                $publicDiskConfig['secure'] = true;
                
                config(['filesystems.disks.public' => $publicDiskConfig]);
                config(['filesystems.default' => 'cloudinary']);
                
                \Log::info('Cloudinary storage ENABLED - Images will be PERMANENT', [
                    'cloud_name' => $cloudName,
                    'api_key_set' => !empty($apiKey),
                    'api_secret_set' => !empty($apiSecret),
                    'using_url' => !empty($cloudinaryUrl),
                    'public_disk_url_cleared' => true,
                    'note' => 'All new uploads will go to Cloudinary and persist forever'
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to enable Cloudinary, falling back to local storage', [
                    'error' => $e->getMessage(),
                    'cloud_name' => $cloudName,
                    'trace' => $e->getTraceAsString()
                ]);
                // Fall back to local storage if Cloudinary configuration fails
                config(['filesystems.disks.public.driver' => 'local']);
                config(['filesystems.default' => 'local']);
            }
        } else {
            \Log::warning('Cloudinary not configured - using local storage (ephemeral)', [
                'cloud_name_set' => !empty($cloudName),
                'api_key_set' => !empty($apiKey),
                'api_secret_set' => !empty($apiSecret),
                'note' => 'Images will be lost on deployment. Configure Cloudinary to persist images.'
            ]);
        }
        
        // Force HTTPS in production or if FORCE_HTTPS is set (Railway provides HTTPS)
        if (config('app.env') === 'production' || 
            config('app.env') === 'staging' || 
            env('FORCE_HTTPS', false)) {
            \URL::forceScheme('https');
        }
    }
} 