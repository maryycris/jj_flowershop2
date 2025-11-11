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
        
        // Use local storage only - images will be stored in storage/app/public
        // Note: Images will be lost on Railway redeployment, but uploads will work
        config(['filesystems.disks.public.driver' => 'local']);
        config(['filesystems.default' => 'local']);
        
        // Force HTTPS in production or if FORCE_HTTPS is set (Railway provides HTTPS)
        if (config('app.env') === 'production' || 
            config('app.env') === 'staging' || 
            env('FORCE_HTTPS', false)) {
            \URL::forceScheme('https');
        }
    }
} 