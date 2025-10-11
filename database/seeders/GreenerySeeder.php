<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GreenerySeeder extends Seeder
{
    public function run(): void
    {
        $greeneryProducts = [
            // Fresh greeneries
            ['name' => 'Fresh Eucalyptus', 'description' => 'Fragrant fresh eucalyptus stems for bouquets', 'price' => 120.00, 'stock' => 40, 'category' => 'Greenery'],
            ['name' => 'Fresh Ruscus', 'description' => 'Fresh Israeli Ruscus foliage', 'price' => 95.00, 'stock' => 50, 'category' => 'Greenery'],
            ['name' => 'Fresh Leatherleaf Fern', 'description' => 'Classic greenery filler fern', 'price' => 80.00, 'stock' => 60, 'category' => 'Greenery'],
            ['name' => 'Fresh Asparagus Fern', 'description' => 'Soft airy greenery for bouquets', 'price' => 110.00, 'stock' => 30, 'category' => 'Greenery'],
            ['name' => 'Fresh Monstera Leaf', 'description' => 'Statement tropical leaf', 'price' => 150.00, 'stock' => 25, 'category' => 'Greenery'],
            // Artificial greeneries
            ['name' => 'Artificial Eucalyptus', 'description' => 'Realistic faux eucalyptus stems', 'price' => 90.00, 'stock' => 80, 'category' => 'Greenery'],
            ['name' => 'Artificial Ruscus', 'description' => 'Durable artificial ruscus foliage', 'price' => 85.00, 'stock' => 75, 'category' => 'Greenery'],
            ['name' => 'Artificial Leatherleaf Fern', 'description' => 'Faux leatherleaf fern stems', 'price' => 70.00, 'stock' => 90, 'category' => 'Greenery'],
            ['name' => 'Artificial Ivy Vine', 'description' => 'Decorative faux ivy vine', 'price' => 65.00, 'stock' => 100, 'category' => 'Greenery'],
            ['name' => 'Artificial Olive Branch', 'description' => 'Elegant faux olive greenery', 'price' => 95.00, 'stock' => 70, 'category' => 'Greenery'],
        ];

        foreach ($greeneryProducts as $product) {
            $existing = DB::table('products')->where('name', $product['name'])->first();
            if ($existing) {
                // Update key fields if it already exists
                DB::table('products')->where('id', $existing->id)->update([
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => DB::raw('GREATEST(stock, '.$product['stock'].')'),
                    'category' => 'Greenery',
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('products')->insert([
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'category' => 'Greenery',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}


