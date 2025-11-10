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
            config(['filesystems.disks.cloudinary.cloud' => $cloudName]);
            config(['filesystems.disks.cloudinary.key' => $apiKey]);
            config(['filesystems.disks.cloudinary.secret' => $apiSecret]);
            config(['filesystems.disks.cloudinary.secure' => true]);
            
            // Enable Cloudinary as the default driver for permanent image storage
            try {
                config(['filesystems.disks.public.driver' => 'cloudinary']);
                config(['filesystems.default' => 'cloudinary']);
                
                \Log::info('Cloudinary storage ENABLED - Images will be PERMANENT', [
                    'cloud_name' => $cloudName,
                    'api_key_set' => !empty($apiKey),
                    'api_secret_set' => !empty($apiSecret),
                    'note' => 'All new uploads will go to Cloudinary and persist forever'
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to enable Cloudinary, falling back to local storage', [
                    'error' => $e->getMessage(),
                    'cloud_name' => $cloudName
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