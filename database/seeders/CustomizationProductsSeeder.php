<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class CustomizationProductsSeeder extends Seeder
{
    public function run()
    {
        // Wrapper products
        $wrappers = [
            ['name' => 'White Wrapping Paper', 'price' => 25.00, 'category' => 'Wrapper'],
            ['name' => 'Brown Kraft Paper', 'price' => 20.00, 'category' => 'Wrapper'],
            ['name' => 'Pink Tissue Paper', 'price' => 15.00, 'category' => 'Wrapper'],
            ['name' => 'Green Cellophane', 'price' => 30.00, 'category' => 'Wrapper'],
            ['name' => 'Gold Foil Paper', 'price' => 35.00, 'category' => 'Wrapper'],
        ];

        // Focal flowers
        $focalFlowers = [
            ['name' => 'Red Roses', 'price' => 150.00, 'category' => 'Focal'],
            ['name' => 'Pink Peonies', 'price' => 200.00, 'category' => 'Focal'],
            ['name' => 'White Lilies', 'price' => 180.00, 'category' => 'Focal'],
            ['name' => 'Yellow Sunflowers', 'price' => 120.00, 'category' => 'Focal'],
            ['name' => 'Purple Lavender', 'price' => 100.00, 'category' => 'Focal'],
            ['name' => 'Orange Marigolds', 'price' => 80.00, 'category' => 'Focal'],
        ];

        // Greenery
        $greenery = [
            ['name' => 'Eucalyptus Leaves', 'price' => 40.00, 'category' => 'Greeneries'],
            ['name' => 'Fern Fronds', 'price' => 35.00, 'category' => 'Greeneries'],
            ['name' => 'Baby\'s Breath', 'price' => 25.00, 'category' => 'Greeneries'],
            ['name' => 'Ruscus Leaves', 'price' => 30.00, 'category' => 'Greeneries'],
            ['name' => 'Asparagus Fern', 'price' => 45.00, 'category' => 'Greeneries'],
        ];

        // Fillers
        $fillers = [
            ['name' => 'White Baby\'s Breath', 'price' => 20.00, 'category' => 'Fillers'],
            ['name' => 'Pink Statice', 'price' => 25.00, 'category' => 'Fillers'],
            ['name' => 'Purple Limonium', 'price' => 30.00, 'category' => 'Fillers'],
            ['name' => 'White Waxflower', 'price' => 35.00, 'category' => 'Fillers'],
            ['name' => 'Yellow Solidago', 'price' => 28.00, 'category' => 'Fillers'],
        ];

        // Ribbons
        $ribbons = [
            ['name' => 'Red Satin Ribbon', 'price' => 15.00, 'category' => 'Ribbons'],
            ['name' => 'White Organza Ribbon', 'price' => 12.00, 'category' => 'Ribbons'],
            ['name' => 'Gold Curling Ribbon', 'price' => 18.00, 'category' => 'Ribbons'],
            ['name' => 'Pink Grosgrain Ribbon', 'price' => 20.00, 'category' => 'Ribbons'],
            ['name' => 'Green Velvet Ribbon', 'price' => 22.00, 'category' => 'Ribbons'],
        ];

        // Combine all products
        $allProducts = array_merge($wrappers, $focalFlowers, $greenery, $fillers, $ribbons);

        foreach ($allProducts as $productData) {
            Product::updateOrCreate(
                [
                    'name' => $productData['name'],
                    'category' => $productData['category']
                ],
                [
                    'code' => strtoupper(substr($productData['category'], 0, 3)) . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'name' => $productData['name'],
                    'price' => $productData['price'],
                    'category' => $productData['category'],
                    'description' => 'Customization component for bouquet making',
                    'stock' => 100,
                    'status' => true,
                    'is_approved' => true,
                    'cost_price' => $productData['price'] * 0.7, // 70% of selling price
                    'reorder_min' => 10,
                    'reorder_max' => 50,
                    'qty_consumed' => 0,
                    'qty_damaged' => 0,
                    'qty_sold' => 0,
                ]
            );
        }

        $this->command->info('Customization products seeded successfully!');
        $this->command->info('Total products created: ' . count($allProducts));
    }
}