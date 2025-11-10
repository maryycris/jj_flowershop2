<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductComposition;

class ProductCompositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some products to use as components
        $redRoses = Product::where('name', 'like', '%red%rose%')->first();
        $whiteRoses = Product::where('name', 'like', '%white%rose%')->first();
        $ribbon = Product::where('name', 'like', '%ribbon%')->first();
        $leaves = Product::where('name', 'like', '%leaf%')->first();
        $wrappingPaper = Product::where('name', 'like', '%wrapping%')->first();
        
        // If no specific products found, get any products to use as components
        if (!$redRoses) {
            $redRoses = Product::where('category', 'Fresh Flowers')->first();
        }
        if (!$whiteRoses) {
            $whiteRoses = Product::where('category', 'Fresh Flowers')->skip(1)->first();
        }
        if (!$ribbon) {
            $ribbon = Product::where('category', 'Accessories')->first();
        }
        if (!$leaves) {
            $leaves = Product::where('category', 'Greenery')->first();
        }
        if (!$wrappingPaper) {
            $wrappingPaper = Product::where('category', 'Accessories')->skip(1)->first();
        }
        
        // Create compositions for Package products
        $packageProducts = Product::where('name', 'like', '%Package%')->get();
        
        foreach ($packageProducts as $package) {
            // Package 3 composition
            if (strpos($package->name, 'Package 3') !== false) {
                if ($redRoses) {
                    ProductComposition::create([
                        'product_id' => $package->id,
                        'component_id' => $redRoses->id,
                        'component_name' => 'Red Roses',
                        'quantity' => 5,
                        'unit' => 'pcs',
                        'description' => 'Fresh red roses for Package 3'
                    ]);
                }
                
                if ($ribbon) {
                    ProductComposition::create([
                        'product_id' => $package->id,
                        'component_id' => $ribbon->id,
                        'component_name' => 'White Ribbon',
                        'quantity' => 2,
                        'unit' => 'meters',
                        'description' => 'White ribbon for wrapping'
                    ]);
                }
                
                if ($leaves) {
                    ProductComposition::create([
                        'product_id' => $package->id,
                        'component_id' => $leaves->id,
                        'component_name' => 'Green Leaves',
                        'quantity' => 10,
                        'unit' => 'pcs',
                        'description' => 'Green leaves for decoration'
                    ]);
                }
                
                if ($wrappingPaper) {
                    ProductComposition::create([
                        'product_id' => $package->id,
                        'component_id' => $wrappingPaper->id,
                        'component_name' => 'Wrapping Paper',
                        'quantity' => 1,
                        'unit' => 'sheet',
                        'description' => 'Gift wrapping paper'
                    ]);
                }
            }
            
            // Package 4 composition
            if (strpos($package->name, 'Package 4') !== false) {
                if ($whiteRoses) {
                    ProductComposition::create([
                        'product_id' => $package->id,
                        'component_id' => $whiteRoses->id,
                        'component_name' => 'White Roses',
                        'quantity' => 8,
                        'unit' => 'pcs',
                        'description' => 'Fresh white roses for Package 4'
                    ]);
                }
                
                if ($ribbon) {
                    ProductComposition::create([
                        'product_id' => $package->id,
                        'component_id' => $ribbon->id,
                        'component_name' => 'Pink Ribbon',
                        'quantity' => 3,
                        'unit' => 'meters',
                        'description' => 'Pink ribbon for wrapping'
                    ]);
                }
                
                if ($leaves) {
                    ProductComposition::create([
                        'product_id' => $package->id,
                        'component_id' => $leaves->id,
                        'component_name' => 'Baby\'s Breath',
                        'quantity' => 15,
                        'unit' => 'pcs',
                        'description' => 'Baby\'s breath for filler'
                    ]);
                }
            }
        }
        
        // Create some basic compositions for other products
        $otherProducts = Product::where('name', 'not like', '%Package%')->take(3)->get();
        
        foreach ($otherProducts as $product) {
            if ($redRoses && $product->id !== $redRoses->id) {
                ProductComposition::create([
                    'product_id' => $product->id,
                    'component_id' => $redRoses->id,
                    'component_name' => 'Red Roses',
                    'quantity' => 3,
                    'unit' => 'pcs',
                    'description' => 'Main flower component'
                ]);
            }
            
            if ($ribbon && $product->id !== $ribbon->id) {
                ProductComposition::create([
                    'product_id' => $product->id,
                    'component_id' => $ribbon->id,
                    'component_name' => 'Decorative Ribbon',
                    'quantity' => 1,
                    'unit' => 'meter',
                    'description' => 'Decorative ribbon'
                ]);
            }
        }
    }
}