<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BouquetOccasion;

class BouquetOccasionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occasions = [
            [
                'name' => 'Birthday',
                'slug' => 'birthday',
                'description' => 'Celebrate another year of life with beautiful birthday flowers',
                'color_theme' => 'Bright and colorful',
                'recommended_flowers' => ['Roses', 'Carnations', 'Sunflowers', 'Gerbera Daisies'],
                'recommended_wrappers' => ['Colorful wrapping paper', 'Tissue paper'],
                'recommended_ribbons' => ['Satin ribbon', 'Curling ribbon'],
                'base_price' => 500.00,
                'is_active' => true,
            ],
            [
                'name' => 'Wedding',
                'slug' => 'wedding',
                'description' => 'Elegant flowers for your special day',
                'color_theme' => 'White, cream, and pastels',
                'recommended_flowers' => ['White Roses', 'Peonies', 'Hydrangeas', 'Baby\'s Breath'],
                'recommended_wrappers' => ['Lace wrapping', 'Satin fabric'],
                'recommended_ribbons' => ['Pearl ribbon', 'Satin ribbon'],
                'base_price' => 1500.00,
                'is_active' => true,
            ],
            [
                'name' => 'Funeral',
                'slug' => 'funeral',
                'description' => 'Respectful and solemn flowers to honor a loved one',
                'color_theme' => 'White, cream, and soft pastels',
                'recommended_flowers' => ['White Lilies', 'White Roses', 'Chrysanthemums', 'Gladiolus'],
                'recommended_wrappers' => ['Simple white wrapping', 'Plain tissue'],
                'recommended_ribbons' => ['White ribbon', 'Black ribbon'],
                'base_price' => 800.00,
                'is_active' => true,
            ],
            [
                'name' => 'Anniversary',
                'slug' => 'anniversary',
                'description' => 'Romantic flowers to celebrate your love',
                'color_theme' => 'Red, pink, and romantic colors',
                'recommended_flowers' => ['Red Roses', 'Pink Roses', 'Tulips', 'Orchids'],
                'recommended_wrappers' => ['Elegant wrapping', 'Silk fabric'],
                'recommended_ribbons' => ['Satin ribbon', 'Velvet ribbon'],
                'base_price' => 700.00,
                'is_active' => true,
            ],
            [
                'name' => 'Valentine\'s Day',
                'slug' => 'valentines-day',
                'description' => 'Express your love with romantic Valentine\'s flowers',
                'color_theme' => 'Red, pink, and white',
                'recommended_flowers' => ['Red Roses', 'Pink Roses', 'Carnations', 'Tulips'],
                'recommended_wrappers' => ['Heart-patterned wrapping', 'Red tissue'],
                'recommended_ribbons' => ['Red satin ribbon', 'Pink ribbon'],
                'base_price' => 600.00,
                'is_active' => true,
            ],
            [
                'name' => 'Mother\'s Day',
                'slug' => 'mothers-day',
                'description' => 'Show appreciation for the special women in your life',
                'color_theme' => 'Pink, yellow, and spring colors',
                'recommended_flowers' => ['Carnations', 'Roses', 'Tulips', 'Daisies'],
                'recommended_wrappers' => ['Spring-themed wrapping', 'Pastel tissue'],
                'recommended_ribbons' => ['Pink ribbon', 'Yellow ribbon'],
                'base_price' => 550.00,
                'is_active' => true,
            ],
            [
                'name' => 'Graduation',
                'slug' => 'graduation',
                'description' => 'Celebrate academic achievements with congratulatory flowers',
                'color_theme' => 'School colors and bright hues',
                'recommended_flowers' => ['Sunflowers', 'Roses', 'Carnations', 'Mixed bouquets'],
                'recommended_wrappers' => ['Celebratory wrapping', 'Colorful tissue'],
                'recommended_ribbons' => ['Gold ribbon', 'Silver ribbon'],
                'base_price' => 450.00,
                'is_active' => true,
            ],
            [
                'name' => 'Get Well Soon',
                'slug' => 'get-well-soon',
                'description' => 'Bright and cheerful flowers to lift spirits during recovery',
                'color_theme' => 'Bright and cheerful colors',
                'recommended_flowers' => ['Sunflowers', 'Daisies', 'Carnations', 'Chrysanthemums'],
                'recommended_wrappers' => ['Cheerful wrapping', 'Bright tissue'],
                'recommended_ribbons' => ['Colorful ribbon', 'Polka dot ribbon'],
                'base_price' => 400.00,
                'is_active' => true,
            ],
            [
                'name' => 'Thank You',
                'slug' => 'thank-you',
                'description' => 'Express gratitude with beautiful thank you flowers',
                'color_theme' => 'Warm and appreciative colors',
                'recommended_flowers' => ['Mixed seasonal flowers', 'Roses', 'Carnations'],
                'recommended_wrappers' => ['Elegant wrapping', 'Simple tissue'],
                'recommended_ribbons' => ['Satin ribbon', 'Simple ribbon'],
                'base_price' => 350.00,
                'is_active' => true,
            ],
            [
                'name' => 'Congratulations',
                'slug' => 'congratulations',
                'description' => 'Celebrate achievements and milestones',
                'color_theme' => 'Celebratory and vibrant colors',
                'recommended_flowers' => ['Mixed bouquets', 'Roses', 'Carnations', 'Sunflowers'],
                'recommended_wrappers' => ['Celebratory wrapping', 'Colorful tissue'],
                'recommended_ribbons' => ['Gold ribbon', 'Silver ribbon', 'Colorful ribbon'],
                'base_price' => 500.00,
                'is_active' => true,
            ]
        ];

        foreach ($occasions as $occasion) {
            BouquetOccasion::create($occasion);
        }
    }
}