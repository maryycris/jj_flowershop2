<?php

namespace App\Services;

use App\Models\CustomizeItem;
use App\Models\CustomBouquet;
use Illuminate\Support\Facades\Storage;

class CustomBouquetImageService
{
    /**
     * Generate a composite image for custom bouquet
     */
    public function generateCompositeImage(CustomBouquet $customBouquet)
    {
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            throw new \Exception('GD extension is not available');
        }
        
        // Base canvas size
        $canvasWidth = 400;
        $canvasHeight = 400;
        
        // Create base canvas
        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
        
        // Enable alpha blending for proper image layering
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);
        
        // Set white background
        $backgroundColor = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $backgroundColor);
        
        // Get all customize items
        $items = CustomizeItem::where('status', true)->get();
        
        // Layer components - wrapper first (background), then other components on top
        // Wrapper covers full canvas (400x400) but centered
        $this->addComponent($canvas, $customBouquet->wrapper, $items, 'wrapper', 50, 50);
        
        // Flowers positioned in center, overlapping wrapper
        $this->addComponent($canvas, $customBouquet->focal_flower_1, $items, 'flower1', 160, 120);
        $this->addComponent($canvas, $customBouquet->focal_flower_2, $items, 'flower2', 200, 150);
        $this->addComponent($canvas, $customBouquet->focal_flower_3, $items, 'flower3', 180, 180);
        
        // Greenery and filler around flowers
        $this->addComponent($canvas, $customBouquet->greenery, $items, 'greenery', 140, 200);
        $this->addComponent($canvas, $customBouquet->filler, $items, 'filler', 220, 210);
        
        // Ribbon on top (usually wraps around)
        $this->addComponent($canvas, $customBouquet->ribbon, $items, 'ribbon', 100, 250);
        
        // Generate unique filename
        $filename = 'custom_bouquet_' . $customBouquet->id . '_' . time() . '.png';
        $path = 'custom_bouquets/' . $filename;
        
        // Save image
        $fullPath = storage_path('app/public/' . $path);
        
        // Create directory if not exists
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Save the image
        imagepng($canvas, $fullPath);
        imagedestroy($canvas);
        
        return $path;
    }
    
    /**
     * Add a component to the canvas
     */
    private function addComponent($canvas, $componentName, $items, $type, $x, $y)
    {
        if (!$componentName) {
            return;
        }
        
        $item = $items->firstWhere('name', $componentName);
        if (!$item || !$item->image) {
            return;
        }
        
        $imagePath = storage_path('app/public/' . $item->image);
        if (!file_exists($imagePath)) {
            return;
        }
        
        // Load component image
        $componentImage = $this->loadImage($imagePath);
        if (!$componentImage) {
            return;
        }
        
        // Get original dimensions
        $originalWidth = imagesx($componentImage);
        $originalHeight = imagesy($componentImage);
        
        // Resize component based on type
        $width = $this->getComponentWidth($type);
        $height = $this->getComponentHeight($type);
        
        // Create resized image with transparency support
        $resizedComponent = imagecreatetruecolor($width, $height);
        imagealphablending($resizedComponent, false);
        imagesavealpha($resizedComponent, true);
        
        // Create transparent background for resized image
        $transparent = imagecolorallocatealpha($resizedComponent, 255, 255, 255, 127);
        imagefill($resizedComponent, 0, 0, $transparent);
        
        // Enable alpha blending for resampling
        imagealphablending($resizedComponent, true);
        
        // Resize the image
        imagecopyresampled(
            $resizedComponent, $componentImage,
            0, 0, 0, 0,
            $width, $height,
            $originalWidth, $originalHeight
        );
        
        // Copy to canvas with alpha blending (preserves transparency)
        imagecopy($canvas, $resizedComponent, $x, $y, 0, 0, $width, $height);
        
        imagedestroy($componentImage);
        imagedestroy($resizedComponent);
    }
    
    /**
     * Load image from file
     */
    private function loadImage($path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                return imagecreatefromjpeg($path);
            case 'png':
                $img = imagecreatefrompng($path);
                if ($img) {
                    // Enable alpha blending and save alpha channel for PNG
                    imagealphablending($img, true);
                    imagesavealpha($img, true);
                }
                return $img;
            case 'gif':
                return imagecreatefromgif($path);
            default:
                return false;
        }
    }
    
    /**
     * Get component width based on type
     */
    private function getComponentWidth($type)
    {
        switch ($type) {
            case 'wrapper':
                return 300;
            case 'flower1':
            case 'flower2':
            case 'flower3':
                return 80;
            case 'greenery':
            case 'filler':
                return 60;
            case 'ribbon':
                return 200;
            default:
                return 50;
        }
    }
    
    /**
     * Get component height based on type
     */
    private function getComponentHeight($type)
    {
        switch ($type) {
            case 'wrapper':
                return 300;
            case 'flower1':
            case 'flower2':
            case 'flower3':
                return 80;
            case 'greenery':
            case 'filler':
                return 60;
            case 'ribbon':
                return 30;
            default:
                return 50;
        }
    }
}
