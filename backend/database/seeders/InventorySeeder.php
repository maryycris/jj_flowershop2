<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Fresh Flowers' => [
                'Red roses', 'White roses', 'Pink roses', 'White lily', 'Sunflower', 
                'Carnation', 'Tulips', 'Aster', 'Gypsophila', 'Eucalyptus', 'Misty', 
                'Lemon leaf', 'Bil is', 'Orchids', 'Wander', 'Winter', 'Statice'
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
            'Other Offers' => [
                'Glass Dome Galaxy Rose', 'Bobo Balloon', 'Foil Balloon (I Love You)',
                'Foil Balloon (Happy Birthday)', 'Foil Balloon (Happy Anniversary)',
                '2 ft size teddy bear', '3 ft size teddy bear', '3.5 ft size teddy bear',
                'Human size teddy bear', 'Steno small teddy bear', 'Heart Shape Ferrero Chocolate',
                'Heart Shape Coco Chocolate', 'Toblerone', 'Goya', 'Kitkat', 'Kisses', 'Take-It'
            ]
        ];

        foreach ($categories as $category => $items) {
            foreach ($items as $item) {
                Product::create([
                    'name' => $item,
                    'category' => $category,
                    'price' => $this->getPriceForItem($item, $category),
                    'stock' => $this->getStockForItem($item, $category),
                    'description' => 'Inventory item from ' . $category,
                    'cost_price' => $this->getPriceForItem($item, $category) * 0.7,
                    'reorder_min' => 10,
                    'reorder_max' => 50,
                    'qty_consumed' => 0,
                    'qty_damaged' => 0,
                    'qty_sold' => 0,
                ]);
            }
        }
    }

    private function getPriceForItem($item, $category)
    {
        $prices = [
            'Fresh Flowers' => 25.00,
            'Dried Flowers' => 40.00,
            'Artificial Flowers' => 12.00,
            'Floral Supplies' => 3.00,
            'Packaging Materials' => 5.00,
            'Materials, Tools, and Equipment' => 15.00,
            'Office Supplies' => 5.00,
            'Other Offers' => 50.00,
        ];
        
        return $prices[$category] ?? 10.00;
    }

    private function getStockForItem($item, $category)
    {
        $stocks = [
            'Fresh Flowers' => 80,
            'Dried Flowers' => 30,
            'Artificial Flowers' => 60,
            'Floral Supplies' => 150,
            'Packaging Materials' => 100,
            'Materials, Tools, and Equipment' => 25,
            'Office Supplies' => 100,
            'Other Offers' => 20,
        ];
        
        return $stocks[$category] ?? 50;
    }
}