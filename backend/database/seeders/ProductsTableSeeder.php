<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Product::create([
            'name' => 'Red Roses Bouquet',
            'price' => 1200,
            'category' => 'Bouquets',
            'description' => 'Classic red roses bouquet',
            'stock' => 50,
            'image' => 'products/red_roses_bouquet.jpg',
            'status' => true,
        ]);
        Product::create([
            'name' => 'Sunflower Arrangement',
            'price' => 950,
            'category' => 'Arrangements',
            'description' => 'Vibrant sunflower arrangement',
            'stock' => 40,
            'image' => 'products/sunflower_arrangement.jpg',
            'status' => true,
        ]);
        Product::create([
            'name' => 'Mixed Flower Basket',
            'price' => 1500,
            'category' => 'Baskets',
            'description' => 'Assorted flowers in a charming basket',
            'stock' => 30,
            'image' => 'products/mixed_flower_basket.jpg',
            'status' => true,
        ]);
        Product::create([
            'name' => 'Orchid Vase',
            'price' => 1800,
            'category' => 'Vases',
            'description' => 'Elegant orchid in a glass vase',
            'stock' => 20,
            'image' => 'products/orchid_vase.jpg',
            'status' => true,
        ]);
    }
}
