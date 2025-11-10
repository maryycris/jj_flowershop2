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
            
            // For now, keep using local storage until we verify Cloudinary works
            // We'll let the upload handler try Cloudinary and fallback if needed
            // This prevents the app from breaking if Cloudinary has issues
            \Log::info('Cloudinary credentials found, but using local storage as default for safety', [
                'cloud_name' => $cloudName,
                'api_key_set' => !empty($apiKey),
                'api_secret_set' => !empty($apiSecret),
                'note' => 'Cloudinary will be used if explicitly requested, with local fallback'
            ]);
            
            // Keep local as default - we'll handle Cloudinary in the controllers with fallback
            // config(['filesystems.disks.public.driver' => 'cloudinary']);
            // config(['filesystems.default' => 'cloudinary']);
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