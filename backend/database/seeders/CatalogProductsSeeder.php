<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatalogProduct;
use Illuminate\Support\Facades\DB;

class CatalogProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Don't truncate if there are already products
        if (CatalogProduct::count() > 0) {
            return;
        }

        $catalogProducts = [
            [
                'name' => 'Red Roses Bouquet',
                'description' => 'Classic red roses bouquet perfect for expressing love and romance. Fresh, long-lasting roses arranged beautifully.',
                'price' => 1200.00,
                'category' => 'Bouquets',
                'image' => null, // Will use fallback logo
                'status' => true,
                'is_approved' => true,
            ],
            [
                'name' => 'Sunflower Arrangement',
                'description' => 'Vibrant sunflower arrangement that brings sunshine to any room. Bright and cheerful flowers.',
                'price' => 950.00,
                'category' => 'Bouquets',
                'image' => null,
                'status' => true,
                'is_approved' => true,
            ],
            [
                'name' => 'Mixed Flower Basket',
                'description' => 'Assorted flowers in a charming basket. A perfect gift for any occasion with various colorful blooms.',
                'price' => 1500.00,
                'category' => 'Bouquets',
                'image' => null,
                'status' => true,
                'is_approved' => true,
            ],
            [
                'name' => 'Orchid Vase',
                'description' => 'Elegant orchid in a beautiful glass vase. Sophisticated and timeless arrangement.',
                'price' => 1800.00,
                'category' => 'Bouquets',
                'image' => null,
                'status' => true,
                'is_approved' => true,
            ],
            [
                'name' => 'White Lilies Bouquet',
                'description' => 'Pure white lilies symbolizing purity and elegance. Perfect for weddings and special occasions.',
                'price' => 1350.00,
                'category' => 'Bouquets',
                'image' => null,
                'status' => true,
                'is_approved' => true,
            ],
            [
                'name' => 'Pink Carnation Bouquet',
                'description' => 'Beautiful pink carnations arranged in a lovely bouquet. Express gratitude and appreciation.',
                'price' => 850.00,
                'category' => 'Bouquets',
                'image' => null,
                'status' => true,
                'is_approved' => true,
            ],
            [
                'name' => 'Valentine\'s Day Package',
                'description' => 'Special Valentine\'s Day package with red roses, chocolates, and a card. Perfect for your loved one.',
                'price' => 2500.00,
                'category' => 'Packages',
                'image' => null,
                'status' => true,
                'is_approved' => true,
            ],
            [
                'name' => 'Birthday Celebration Package',
                'description' => 'Complete birthday package with flowers, balloons, and a birthday card. Make their day special!',
                'price' => 2200.00,
                'category' => 'Packages',
                'image' => null,
                'status' => true,
                'is_approved' => true,
            ],
            [
                'name' => 'Anniversary Gift Set',
                'description' => 'Romantic anniversary gift set with flowers, wine, and chocolates. Celebrate your love story.',
                'price' => 3000.00,
                'category' => 'Packages',
                'image' => null,
                'status' => true,
                'is_approved' => true,
            ],
            [
                'name' => 'Get Well Soon Package',
                'description' => 'Thoughtful get well soon package with flowers and a card. Send your warmest wishes.',
                'price' => 1800.00,
                'category' => 'Packages',
                'image' => null,
                'status' => true,
                'is_approved' => true,
            ],
            [
                'name' => 'Thank You Gift Basket',
                'description' => 'Elegant gift basket with flowers and treats. Perfect way to say thank you.',
                'price' => 2000.00,
                'category' => 'Gifts',
                'image' => null,
                'status' => true,
                'is_approved' => true,
            ],
            [
                'name' => 'Congratulations Bouquet',
                'description' => 'Celebrate achievements with this beautiful congratulatory bouquet. Bright and festive.',
                'price' => 1600.00,
                'category' => 'Gifts',
                'image' => null,
                'status' => true,
                'is_approved' => true,
            ],
        ];

        foreach ($catalogProducts as $product) {
            CatalogProduct::create($product);
        }
    }
}

