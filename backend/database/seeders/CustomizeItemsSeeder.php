<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomizeItem;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class CustomizeItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Don't seed if items already exist
        if (CustomizeItem::count() > 0) {
            return;
        }

        // Ensure storage directory exists
        $customizeDir = storage_path('app/public/customize');
        if (!File::exists($customizeDir)) {
            File::makeDirectory($customizeDir, 0755, true);
        }

        // Copy placeholder images from public/images if they exist
        $placeholderImage = 'customize/placeholder.png';
        
        // Create sample customize items
        $items = [
            // Wrappers
            ['name' => 'White Wrapping Paper', 'category' => 'Wrappers', 'price' => 25.00],
            ['name' => 'Brown Kraft Paper', 'category' => 'Wrappers', 'price' => 20.00],
            ['name' => 'Pink Tissue Paper', 'category' => 'Wrappers', 'price' => 15.00],
            ['name' => 'Green Cellophane', 'category' => 'Wrappers', 'price' => 30.00],
            ['name' => 'Gold Foil Paper', 'category' => 'Wrappers', 'price' => 35.00],
            
            // Fresh Flowers - match existing products
            ['name' => 'Red roses', 'category' => 'Fresh Flowers', 'price' => 150.00],
            ['name' => 'White roses', 'category' => 'Fresh Flowers', 'price' => 150.00],
            ['name' => 'Pink roses', 'category' => 'Fresh Flowers', 'price' => 150.00],
            ['name' => 'White lily', 'category' => 'Fresh Flowers', 'price' => 180.00],
            ['name' => 'Sunflower', 'category' => 'Fresh Flowers', 'price' => 120.00],
            ['name' => 'Carnation', 'category' => 'Fresh Flowers', 'price' => 100.00],
            ['name' => 'Tulips', 'category' => 'Fresh Flowers', 'price' => 130.00],
            ['name' => 'Orchids', 'category' => 'Fresh Flowers', 'price' => 200.00],
            
            // Greenery
            ['name' => 'Eucalyptus', 'category' => 'Greenery', 'price' => 40.00],
            ['name' => 'Lemon leaf', 'category' => 'Greenery', 'price' => 35.00],
            ['name' => 'Misty', 'category' => 'Greenery', 'price' => 30.00],
            ['name' => 'Gypsophila', 'category' => 'Greenery', 'price' => 25.00],
            
            // Artificial Flowers
            ['name' => 'Artificial Rose', 'category' => 'Artificial Flowers', 'price' => 50.00],
            ['name' => 'Artificial Sunflower', 'category' => 'Artificial Flowers', 'price' => 45.00],
            ['name' => 'Artificial Lily', 'category' => 'Artificial Flowers', 'price' => 55.00],
            
            // Ribbon
            ['name' => 'Red Satin Ribbon', 'category' => 'Ribbon', 'price' => 15.00],
            ['name' => 'White Organza Ribbon', 'category' => 'Ribbon', 'price' => 12.00],
            ['name' => 'Gold Curling Ribbon', 'category' => 'Ribbon', 'price' => 18.00],
            ['name' => 'Pink Grosgrain Ribbon', 'category' => 'Ribbon', 'price' => 20.00],
        ];

        foreach ($items as $itemData) {
            // Try to find matching product for inventory_item_id
            $product = Product::where('name', $itemData['name'])
                ->where('category', $itemData['category'])
                ->first();
            
            // Set image to NULL - images should be uploaded through admin/clerk interface
            // This prevents showing inappropriate placeholder images
            $imagePath = null;

            CustomizeItem::create([
                'name' => $itemData['name'],
                'category' => $itemData['category'],
                'price' => $itemData['price'],
                'image' => $imagePath,
                'inventory_item_id' => $product ? $product->id : null,
                'status' => true,
                'is_approved' => true, // Auto-approve seeded items
            ]);
        }

        $this->command->info('Customize items seeded successfully!');
        $this->command->info('Total items created: ' . count($items));
    }
}

