<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Starting inventory population...');

        $categories = [
            'Fresh Flowers' => [
                'Red roses', 'White roses', 'Pink roses', 'Yellow roses', 'Orange roses',
                'White lily', 'Sunflower', 'Carnation', 'Tulips', 'Aster', 'Gypsophila', 
                'Eucalyptus', 'Misty', 'Lemon leaf', 'Bil is', 'Orchids', 'Wander', 
                'Winter', 'Statice', 'Baby breath', 'Chrysanthemum', 'Gerbera', 'Iris',
                'Peony', 'Daisy', 'Lavender', 'Marigold', 'Snapdragon', 'Zinnia'
            ],
            'Dried Flowers' => [
                'Fossilized Roses', 'Preserve Roses', 'Gypsophila (Dried)', 
                'Eucalyptus (Dried)', 'Bunny tails', 'Trigo grass', 'Palm Spear Anahaw'
            ],
            'Artificial Flowers' => [
                'Tulip flower (Artificial)', 'Rose flower (Artificial)', 'Satin Ribbon flower'
            ],
            'Floral Supplies' => [
                'Floral foam', 'Glitter Ribbon (gold)', 'Glitter Ribbon (silver)', 
                'Metal heart shape stick', 'Quick dry floral supply (black)', 
                'Quick dry floral supply (blue)', 'Quick dry floral supply (purple)', 
                'Quick dry floral supply (yellow)', 'Quick dry floral supply (pink)',
                '2 cm satin ribbon (red)', '2 cm satin ribbon (yellow)', '2 cm satin ribbon (green)',
                '2 cm satin ribbon (blue)', '2 cm satin ribbon (purple)', '2 cm satin ribbon (pink)',
                '2 cm satin ribbon (black)', '2 cm satin ribbon (creamy white)', '2 cm satin ribbon (brown)',
                '2 cm satin ribbon (peach)', '2.5 cm satin ribbon (red)', '2.5 cm satin ribbon (yellow)',
                '2.5 cm satin ribbon (green)', '2.5 cm satin ribbon (blue)', '2.5 cm satin ribbon (purple)',
                '2.5 cm satin ribbon (pink)', '2.5 cm satin ribbon (black)', '2.5 cm satin ribbon (creamy white)',
                '2.5 cm satin ribbon (brown)', '2.5 cm satin ribbon (peach)', '4cm satin ribbon (red)',
                '4cm satin ribbon (yellow)', '4cm satin ribbon (green)', '4cm satin ribbon (blue)',
                '4cm satin ribbon (purple)', '4cm satin ribbon (pink)', '4cm satin ribbon (black)',
                '4cm satin ribbon (creamy white)', '4cm satin ribbon (brown)', '4cm satin ribbon (peach)',
                'Fancy ribbon plain (red)', 'Fancy ribbon plain (yellow)', 'Fancy ribbon plain (gold)',
                'Fancy ribbon plain (green)', 'Fancy ribbon plain (blue)', 'Fancy ribbon plain (purple)',
                'Fancy ribbon plain (pink)', 'Fancy ribbon plain (black)', 'Fancy ribbon plain (white)',
                'Fancy ribbon plain (peach)', 'Fancy ribbon with gold outline (red)', 'Fancy ribbon with gold outline (yellow)',
                'Fancy ribbon with gold outline (gold)', 'Fancy ribbon with gold outline (green)', 'Fancy ribbon with gold outline (blue)',
                'Fancy ribbon with gold outline (purple)', 'Fancy ribbon with gold outline (pink)', 'Fancy ribbon with gold outline (black)',
                'Fancy ribbon with gold outline (white)', 'Fancy ribbon with gold outline (peach)',
                'Plastic ribbon heart printed', 'Plastic ribbon I miss you printed', 'Plastic ribbon Always by your side printed',
                'Plastic ribbon I love you printed', 'Plastic ribbon with brown horizontal line printed',
                'Plastic ribbon with white vertical line printed', 'Plastic ribbon with words printed',
                'Green stick', 'Floral green tape', '4cm fishtail ribbon (red)', '4cm fishtail ribbon (gold)',
                '4cm fishtail ribbon (peach)', '4cm fishtail ribbon (green)', '4cm fishtail ribbon (blue)',
                '4cm fishtail ribbon (purple)', '4cm fishtail ribbon (pink)', '4cm fishtail ribbon (black)',
                '4cm fishtail ribbon (white)', 'Organza ribbon with gold lining (red)', 'Organza ribbon with gold lining (yellow)',
                'Organza ribbon with gold lining (green)', 'Organza ribbon with gold lining (purple)',
                'Organza ribbon with gold lining (pink)', 'Organza ribbon with gold lining (black)',
                'Organza ribbon with gold lining (white)', 'Organza ribbon with gold lining (gold)'
            ],
            'Packaging Materials' => [
                'Foggy bouquet wrapper (red)', 'Foggy bouquet wrapper (gold)', 'Foggy bouquet wrapper (blue)',
                'Foggy bouquet wrapper (purple)', 'Foggy bouquet wrapper (black)', 'Foggy bouquet wrapper (white)',
                'Foggy bouquet wrapper (pink)', 'Two toned bouquet wrapper (red)', 'Two toned bouquet wrapper (yellow)',
                'Two toned bouquet wrapper (green)', 'Two toned bouquet wrapper (blue)', 'Two toned bouquet wrapper (purple)',
                'Two toned bouquet wrapper (pink)', 'Two toned bouquet wrapper (brown)', 'Two toned bouquet wrapper (peach)',
                'With gold outline bouquet wrapper (red)', 'With gold outline bouquet wrapper (yellow)',
                'With gold outline bouquet wrapper (green)', 'With gold outline bouquet wrapper (blue)',
                'With gold outline bouquet wrapper (purple)', 'With gold outline bouquet wrapper (pink)',
                'With gold outline bouquet wrapper (gold)', 'With gold outline bouquet wrapper (peach)',
                'With gold outline bouquet wrapper (black)', 'Single tone bouquet wrapper (red)',
                'Single tone bouquet wrapper (yellow)', 'Single tone bouquet wrapper (green)',
                'Single tone bouquet wrapper (blue)', 'Single tone bouquet wrapper (purple)',
                'Single tone bouquet wrapper (pink)', 'Single tone bouquet wrapper (gold)',
                'Single tone bouquet wrapper (peach)', 'Single tone bouquet wrapper (black)',
                'English news flower bouquet wrapper (brown)', 'English news flower bouquet wrapper (black)',
                'English news flower bouquet wrapper (pink)', 'English news flower bouquet wrapper (blue)',
                'English news flower bouquet wrapper (purple)', 'English news flower bouquet wrapper (green)',
                'English news flower bouquet wrapper (white)', 'Printed heart bouquet wrapper (red)',
                'Printed heart bouquet wrapper (green)', 'Printed heart bouquet wrapper (blue)',
                'Printed heart bouquet wrapper (purple)', 'Printed heart bouquet wrapper (pink)',
                'Printed heart bouquet wrapper (black)', 'Printed heart bouquet wrapper (peach)',
                'Printed heart bouquet wrapper (white)', 'Gradient color bouquet wrapper (red)',
                'Gradient color bouquet wrapper (peach)', 'Gradient color bouquet wrapper (blue)',
                'Gradient color bouquet wrapper (purple)', 'Gradient color bouquet wrapper (black)',
                'Gradient color bouquet wrapper (brown)', 'Gradient color bouquet wrapper (white)',
                'Gradient color bouquet wrapper (pink)', 'Mesh web (black)', 'Mesh web (red)',
                'Mesh web (blue)', 'Mesh web (pink)', 'Mesh web (brown)', 'Mesh web (yellow)',
                'Mesh web (purple)', 'Mesh web (peach)', 'Tissue wrapper (black)', 'Tissue wrapper (white)',
                'Tissue wrapper (pink)', 'Tissue wrapper (red)', 'Tissue wrapper (blue)',
                'Tissue wrapper (purple)', 'Tissue wrapper (brown)', 'Abacca burlap',
                'Single flower rose plastic', 'Clear plastic bag transparent', 'Small cartoon boxes',
                'Tela bouquet wrapper (black)', 'Tela bouquet wrapper (white)', 'Tela bouquet wrapper (gold)',
                'Tela bouquet wrapper (red)', 'Tela bouquet wrapper (pink)', 'Tela bouquet wrapper (yellow)',
                'Heart boxes (small)', 'Heart boxes (medium)', 'Heart boxes (large)', 'Heart boxes (jumbo)',
                'Circle boxes (small)', 'Circle boxes (medium)', 'Circle boxes (large)', 'Circle boxes (jumbo)',
                'Pearl yarn pleated flower mesh (white)', 'Pearl yarn pleated flower mesh (black)',
                'Pearl yarn pleated flower mesh (red)', 'Pearl yarn pleated flower mesh (pink)',
                'Yarn pleated flower mesh (white)', 'Yarn pleated flower mesh (black)',
                'Yarn pleated flower mesh (red)', 'Yarn pleated flower mesh (pink)'
            ],
            'Materials, Tools, and Equipment' => [
                'Scissor', 'Shear Plant Cutter', 'Cutter', 'Apron', 'Tape Dispenser',
                'Electric Balloon Pump', 'Cricket lighter', 'Metal rack', 'Table', 'Storage drum',
                'Pail', 'Deeper', 'Chair', 'Solar light', 'Straw', 'Alambre wire', 'Hammer', 'Round rag',
                'Glue Gun', 'Floral foam cutter', 'Thorn Remover', 'Leaves Remover', 'Sealer',
                'Flower Stand', 'Flower basket', 'Floral Ice bucket', 'Wire Mesh', 'Ferry lights',
                'Plastic (20x30)', 'Plastic (5x9)', 'Plastic (8x11)', 'Sando bag (medium)',
                'Sando bag (xl)', 'Sando bag (jumbo)', 'Opp plastic for money', 'Bamboo stick',
                'Scotch tape', 'Pin', 'Glue stick (big)', 'Glue stick (small)', 'Cartoon', 'Tent'
            ],
            'Office Supplies' => [
                'Bondpaper (Long)', 'Bondpaper (Short)', 'Ballpen (Black)', 'Ballpen (Blue)',
                'Ballpen (Red)', 'Permanent marker', 'Whiteboard marker', 'Notebook', 'Pencil',
                'Paper', 'Correction tape', 'Double sided tape', 'Ruler', 'Calculator',
                'Ink pad & Stamp', 'Glue', 'Receipt', 'Sticker', 'Loyalty card',
                'Battery (AA)', 'Battery (AAA)', 'Price stamp', 'Stapler', 'Eraser', 'Sharpener'
            ],
            'Greenery' => [
                'Eucalyptus leaves', 'Fern leaves', 'Ivy leaves', 'Palm leaves',
                'Monstera leaves', 'Fiddle leaf', 'Pothos leaves', 'Philodendron',
                'Asparagus fern', 'Ruscus', 'Salal leaves', 'Leather leaf',
                'Lemon leaf', 'Boxwood', 'Cedar', 'Pine branches'
            ],
            'Wrappers' => [
                'Foggy bouquet wrapper (red)', 'Foggy bouquet wrapper (gold)', 
                'Foggy bouquet wrapper (blue)', 'Foggy bouquet wrapper (purple)',
                'Foggy bouquet wrapper (black)', 'Foggy bouquet wrapper (white)',
                'Foggy bouquet wrapper (pink)', 'Two toned bouquet wrapper (red)',
                'Two toned bouquet wrapper (yellow)', 'Two toned bouquet wrapper (green)',
                'Two toned bouquet wrapper (blue)', 'Two toned bouquet wrapper (purple)',
                'Two toned bouquet wrapper (pink)', 'Single tone bouquet wrapper (red)',
                'Single tone bouquet wrapper (yellow)', 'Single tone bouquet wrapper (green)',
                'Single tone bouquet wrapper (blue)', 'Single tone bouquet wrapper (purple)',
                'Single tone bouquet wrapper (pink)', 'English news flower bouquet wrapper',
                'Printed heart bouquet wrapper', 'Gradient color bouquet wrapper',
                'Tissue wrapper', 'Tela bouquet wrapper', 'Mesh web'
            ],
            'Ribbon' => [
                '2 cm satin ribbon (red)', '2 cm satin ribbon (yellow)', '2 cm satin ribbon (green)',
                '2 cm satin ribbon (blue)', '2 cm satin ribbon (purple)', '2 cm satin ribbon (pink)',
                '2 cm satin ribbon (black)', '2 cm satin ribbon (white)', '2.5 cm satin ribbon (red)',
                '2.5 cm satin ribbon (yellow)', '2.5 cm satin ribbon (green)', '2.5 cm satin ribbon (blue)',
                '2.5 cm satin ribbon (purple)', '2.5 cm satin ribbon (pink)', '2.5 cm satin ribbon (black)',
                '4cm satin ribbon (red)', '4cm satin ribbon (yellow)', '4cm satin ribbon (green)',
                '4cm satin ribbon (blue)', '4cm satin ribbon (purple)', '4cm satin ribbon (pink)',
                '4cm satin ribbon (black)', 'Fancy ribbon plain', 'Fancy ribbon with gold outline',
                'Plastic ribbon heart printed', 'Plastic ribbon I love you printed',
                '4cm fishtail ribbon', 'Organza ribbon with gold lining', 'Glitter Ribbon (gold)',
                'Glitter Ribbon (silver)'
            ],
            'Other Offers' => [
                'Glass Dome Galaxy Rose', 'Bobo Balloon', 'Foil Balloon (I Love You)',
                'Foil Balloon (Happy Birthday)', 'Foil Balloon (Happy Anniversary)',
                '2 ft size teddy bear', '3 ft size teddy bear', '3.5 ft size teddy bear',
                'Human size teddy bear', 'Steno small teddy bear', 'Heart Shape Ferrero Chocolate',
                'Heart Shape Coco Chocolate', 'Toblerone', 'Goya', 'Kitkat', 'Kisses', 'Take-It'
            ]
        ];

        $categoryPrefixes = [
            'Fresh Flowers' => 'FRE',
            'Dried Flowers' => 'DRI',
            'Artificial Flowers' => 'ART',
            'Floral Supplies' => 'FLO',
            'Packaging Materials' => 'PAC',
            'Materials, Tools, and Equipment' => 'MAT',
            'Office Supplies' => 'OFF',
            'Greenery' => 'GRE',
            'Wrappers' => 'WRA',
            'Ribbon' => 'RIB',
            'Other Offers' => 'OTH'
        ];

        $productCount = 0;
        foreach ($categories as $category => $items) {
            foreach ($items as $index => $item) {
                // Generate product code: Category prefix + Name abbreviation + Number
                $prefix = $categoryPrefixes[$category] ?? 'PRO';
                $nameAbbr = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $item), 0, 5));
                $code = $prefix . '-' . $nameAbbr . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                
                // Check if product with same code already exists
                if (Product::where('code', $code)->exists()) {
                    continue;
                }

                Product::create([
                    'code' => $code,
                    'name' => $item,
                    'category' => $category,
                    'price' => $this->getPriceForItem($item, $category),
                    'stock' => $this->getStockForItem($item, $category),
                    'description' => 'Inventory item from ' . $category,
                    'cost_price' => $this->getPriceForItem($item, $category) * 0.5,
                    'reorder_min' => $this->getReorderMin($category),
                    'reorder_max' => $this->getReorderMax($category),
                    'qty_consumed' => rand(0, 10),
                    'qty_damaged' => rand(0, 3),
                    'qty_sold' => rand(0, 20),
                    'status' => true,
                    'is_approved' => true,
                    'is_customize_item' => in_array($category, ['Fresh Flowers', 'Dried Flowers', 'Artificial Flowers', 'Greenery', 'Floral Supplies']),
                ]);
                $productCount++;
            }
        }

        $this->command->info("Successfully created {$productCount} inventory items!");
    }

    private function getPriceForItem($item, $category)
    {
        $prices = [
            'Fresh Flowers' => rand(15, 30),
            'Dried Flowers' => rand(35, 50),
            'Artificial Flowers' => rand(10, 20),
            'Floral Supplies' => rand(2, 8),
            'Packaging Materials' => rand(3, 10),
            'Materials, Tools, and Equipment' => rand(10, 25),
            'Office Supplies' => rand(3, 10),
            'Greenery' => rand(12, 25),
            'Wrappers' => rand(4, 12),
            'Ribbon' => rand(2, 6),
            'Other Offers' => rand(40, 100),
        ];
        
        return $prices[$category] ?? 10.00;
    }

    private function getStockForItem($item, $category)
    {
        $stocks = [
            'Fresh Flowers' => rand(50, 100),
            'Dried Flowers' => rand(20, 40),
            'Artificial Flowers' => rand(40, 80),
            'Floral Supplies' => rand(100, 200),
            'Packaging Materials' => rand(80, 150),
            'Materials, Tools, and Equipment' => rand(15, 35),
            'Office Supplies' => rand(80, 150),
            'Greenery' => rand(30, 70),
            'Wrappers' => rand(60, 120),
            'Ribbon' => rand(100, 200),
            'Other Offers' => rand(10, 30),
        ];
        
        return $stocks[$category] ?? 50;
    }

    private function getReorderMin($category)
    {
        $mins = [
            'Fresh Flowers' => 20,
            'Dried Flowers' => 10,
            'Artificial Flowers' => 15,
            'Floral Supplies' => 30,
            'Packaging Materials' => 25,
            'Materials, Tools, and Equipment' => 5,
            'Office Supplies' => 20,
            'Greenery' => 15,
            'Wrappers' => 20,
            'Ribbon' => 30,
            'Other Offers' => 5,
        ];
        
        return $mins[$category] ?? 10;
    }

    private function getReorderMax($category)
    {
        $maxs = [
            'Fresh Flowers' => 100,
            'Dried Flowers' => 50,
            'Artificial Flowers' => 80,
            'Floral Supplies' => 200,
            'Packaging Materials' => 150,
            'Materials, Tools, and Equipment' => 50,
            'Office Supplies' => 150,
            'Greenery' => 80,
            'Wrappers' => 120,
            'Ribbon' => 200,
            'Other Offers' => 40,
        ];
        
        return $maxs[$category] ?? 50;
    }
}