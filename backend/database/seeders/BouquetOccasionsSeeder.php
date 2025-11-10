<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BouquetOccasion;

class BouquetOccasionsSeeder extends Seeder
{
    public function run()
    {
        $occasions = [
            [
                'name' => 'Funeral',
                'slug' => 'funeral',
                'description' => 'Sympathy bouquets for funeral services and memorials',
                'color_theme' => 'White',
                'recommended_flowers' => ['White Lilies', 'White Roses', 'Purple Lavender'],
                'recommended_wrappers' => ['White Wrapping Paper', 'Brown Kraft Paper'],
                'recommended_ribbons' => ['White Organza Ribbon', 'Green Velvet Ribbon'],
                'base_price' => 200.00,
                'is_active' => true
            ],
            [
                'name' => 'Birthday',
                'slug' => 'birthday',
                'description' => 'Celebratory bouquets for birthday celebrations',
                'color_theme' => 'Bright Colors',
                'recommended_flowers' => ['Red Roses', 'Pink Peonies', 'Yellow Sunflowers', 'Orange Marigolds'],
                'recommended_wrappers' => ['Pink Tissue Paper', 'Gold Foil Paper'],
                'recommended_ribbons' => ['Red Satin Ribbon', 'Pink Grosgrain Ribbon', 'Gold Curling Ribbon'],
                'base_price' => 250.00,
                'is_active' => true
            ],
            [
                'name' => 'Wedding',
                'slug' => 'wedding',
                'description' => 'Elegant bouquets for wedding ceremonies and receptions',
                'color_theme' => 'White & Pink',
                'recommended_flowers' => ['White Lilies', 'Pink Peonies', 'Red Roses'],
                'recommended_wrappers' => ['White Wrapping Paper', 'Pink Tissue Paper'],
                'recommended_ribbons' => ['White Organza Ribbon', 'Pink Grosgrain Ribbon'],
                'base_price' => 300.00,
                'is_active' => true
            ],
            [
                'name' => 'Valentine\'s Day',
                'slug' => 'valentines',
                'description' => 'Romantic bouquets for Valentine\'s Day celebrations',
                'color_theme' => 'Red & Pink',
                'recommended_flowers' => ['Red Roses', 'Pink Peonies'],
                'recommended_wrappers' => ['Pink Tissue Paper', 'Red Wrapping Paper'],
                'recommended_ribbons' => ['Red Satin Ribbon', 'Pink Grosgrain Ribbon'],
                'base_price' => 280.00,
                'is_active' => true
            ],
            [
                'name' => 'Anniversary',
                'slug' => 'anniversary',
                'description' => 'Special bouquets for wedding anniversaries and milestones',
                'color_theme' => 'Mixed Colors',
                'recommended_flowers' => ['Red Roses', 'Pink Peonies', 'White Lilies'],
                'recommended_wrappers' => ['Gold Foil Paper', 'White Wrapping Paper'],
                'recommended_ribbons' => ['Gold Curling Ribbon', 'Red Satin Ribbon'],
                'base_price' => 320.00,
                'is_active' => true
            ],
            [
                'name' => 'Get Well Soon',
                'slug' => 'get-well',
                'description' => 'Cheerful bouquets to wish someone a speedy recovery',
                'color_theme' => 'Bright & Cheerful',
                'recommended_flowers' => ['Yellow Sunflowers', 'Pink Peonies', 'Orange Marigolds'],
                'recommended_wrappers' => ['Pink Tissue Paper', 'Green Cellophane'],
                'recommended_ribbons' => ['Yellow Solidago', 'Pink Grosgrain Ribbon'],
                'base_price' => 180.00,
                'is_active' => true
            ],
            [
                'name' => 'Graduation',
                'slug' => 'graduation',
                'description' => 'Congratulations bouquets for graduation ceremonies',
                'color_theme' => 'School Colors',
                'recommended_flowers' => ['Yellow Sunflowers', 'Red Roses', 'White Lilies'],
                'recommended_wrappers' => ['Gold Foil Paper', 'White Wrapping Paper'],
                'recommended_ribbons' => ['Gold Curling Ribbon', 'White Organza Ribbon'],
                'base_price' => 220.00,
                'is_active' => true
            ],
            [
                'name' => 'Mother\'s Day',
                'slug' => 'mothers-day',
                'description' => 'Special bouquets to celebrate mothers',
                'color_theme' => 'Pink & White',
                'recommended_flowers' => ['Pink Peonies', 'White Lilies', 'Red Roses'],
                'recommended_wrappers' => ['Pink Tissue Paper', 'White Wrapping Paper'],
                'recommended_ribbons' => ['Pink Grosgrain Ribbon', 'White Organza Ribbon'],
                'base_price' => 260.00,
                'is_active' => true
            ]
        ];

        foreach ($occasions as $occasion) {
            BouquetOccasion::updateOrCreate(
                ['slug' => $occasion['slug']],
                $occasion
            );
        }

        $this->command->info('Bouquet occasions seeded successfully!');
        $this->command->info('Total occasions created: ' . count($occasions));
    }
}