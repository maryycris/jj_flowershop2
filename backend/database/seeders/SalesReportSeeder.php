<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\SalesOrder;
use App\Models\CustomBouquet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a customer user
        $customer = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Test Customer',
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]
        );

        // Get existing products or create some
        $products = Product::where('status', true)->take(10)->get();
        
        if ($products->isEmpty()) {
            // Create some sample products if none exist
            $products = collect([
                Product::create([
                    'name' => '2ft Pink Teddy Bear',
                    'price' => 50.00,
                    'category' => 'Gifts',
                    'description' => 'Pink teddy bear',
                    'stock' => 20,
                    'status' => true,
                ]),
                Product::create([
                    'name' => 'Package 5',
                    'price' => 1000.00,
                    'category' => 'Packages',
                    'description' => 'Package 5',
                    'stock' => 15,
                    'status' => true,
                ]),
                Product::create([
                    'name' => 'Package 2',
                    'price' => 700.00,
                    'category' => 'Packages',
                    'description' => 'Package 2',
                    'stock' => 10,
                    'status' => true,
                ]),
                Product::create([
                    'name' => 'Package 6',
                    'price' => 600.00,
                    'category' => 'Packages',
                    'description' => 'Package 6',
                    'stock' => 12,
                    'status' => true,
                ]),
                Product::create([
                    'name' => 'Fluffy Whimsy',
                    'price' => 300.00,
                    'category' => 'Bouquets',
                    'description' => 'Fluffy Whimsy bouquet',
                    'stock' => 8,
                    'status' => true,
                ]),
                Product::create([
                    'name' => 'Golden Fade',
                    'price' => 315.00,
                    'category' => 'Bouquets',
                    'description' => 'Golden Fade bouquet',
                    'stock' => 6,
                    'status' => true,
                ]),
                Product::create([
                    'name' => 'Sunlit Love',
                    'price' => 250.00,
                    'category' => 'Bouquets',
                    'description' => 'Sunlit Love bouquet',
                    'stock' => 10,
                    'status' => true,
                ]),
                Product::create([
                    'name' => 'Sweet Symphony',
                    'price' => 300.00,
                    'category' => 'Bouquets',
                    'description' => 'Sweet Symphony bouquet',
                    'stock' => 9,
                    'status' => true,
                ]),
            ]);
        }

        // Create orders with different dates (last 30 days)
        $baseDate = Carbon::now()->subDays(15);
        
        // Helper function to generate unique SO number
        $generateSoNumber = function($prefix = 'SO') {
            $lastSo = SalesOrder::where('so_number', 'like', $prefix . '-%')
                ->orderByRaw('CAST(SUBSTRING(so_number, 4) AS UNSIGNED) DESC')
                ->first();
            
            if ($lastSo) {
                $lastNumber = intval(substr($lastSo->so_number, 3));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            return $prefix . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        };

        // Order 1 - Multiple products
        $order1 = Order::create([
            'user_id' => $customer->id,
            'total_price' => 1050.00,
            'status' => 'approved',
            'order_status' => 'approved',
            'type' => 'online',
            'payment_status' => 'paid',
            'created_at' => $baseDate->copy()->subDays(12),
        ]);
        
        // Attach products to order 1
        if ($products->count() >= 2) {
            $order1->products()->attach([
                $products[0]->id => ['quantity' => 1],
                $products[1]->id => ['quantity' => 1],
            ]);
        }
        
        // Create Sales Order for order 1
        SalesOrder::firstOrCreate(
            ['order_id' => $order1->id],
            [
                'so_number' => $generateSoNumber(),
                'user_id' => $customer->id,
                'subtotal' => 1050.00,
                'shipping_fee' => 0,
                'total_amount' => 1050.00,
                'status' => 'confirmed',
                'confirmed_at' => $baseDate->copy()->subDays(12),
            ]
        );

        // Order 2 - Multiple products
        $order2 = Order::create([
            'user_id' => $customer->id,
            'total_price' => 1300.00,
            'status' => 'approved',
            'order_status' => 'approved',
            'type' => 'online',
            'payment_status' => 'paid',
            'created_at' => $baseDate->copy()->subDays(10),
        ]);
        
        if ($products->count() >= 4) {
            $order2->products()->attach([
                $products[2]->id => ['quantity' => 1],
                $products[3]->id => ['quantity' => 1],
            ]);
        }
        
        SalesOrder::firstOrCreate(
            ['order_id' => $order2->id],
            [
                'so_number' => $generateSoNumber(),
                'user_id' => $customer->id,
                'subtotal' => 1300.00,
                'shipping_fee' => 0,
                'total_amount' => 1300.00,
                'status' => 'confirmed',
                'confirmed_at' => $baseDate->copy()->subDays(10),
            ]
        );

        // Order 3 - Multiple products
        $order3 = Order::create([
            'user_id' => $customer->id,
            'total_price' => 615.00,
            'status' => 'approved',
            'order_status' => 'approved',
            'type' => 'online',
            'payment_status' => 'paid',
            'created_at' => $baseDate->copy()->subDays(8),
        ]);
        
        if ($products->count() >= 6) {
            $order3->products()->attach([
                $products[4]->id => ['quantity' => 1],
                $products[5]->id => ['quantity' => 1],
            ]);
        }
        
        SalesOrder::firstOrCreate(
            ['order_id' => $order3->id],
            [
                'so_number' => $generateSoNumber(),
                'user_id' => $customer->id,
                'subtotal' => 615.00,
                'shipping_fee' => 0,
                'total_amount' => 615.00,
                'status' => 'confirmed',
                'confirmed_at' => $baseDate->copy()->subDays(8),
            ]
        );

        // Order 4 - Multiple products
        $order4 = Order::create([
            'user_id' => $customer->id,
            'total_price' => 550.00,
            'status' => 'approved',
            'order_status' => 'approved',
            'type' => 'online',
            'payment_status' => 'paid',
            'created_at' => $baseDate->copy()->subDays(5),
        ]);
        
        if ($products->count() >= 8) {
            $order4->products()->attach([
                $products[6]->id => ['quantity' => 1],
                $products[7]->id => ['quantity' => 1],
            ]);
        }
        
        SalesOrder::firstOrCreate(
            ['order_id' => $order4->id],
            [
                'so_number' => $generateSoNumber(),
                'user_id' => $customer->id,
                'subtotal' => 550.00,
                'shipping_fee' => 0,
                'total_amount' => 550.00,
                'status' => 'confirmed',
                'confirmed_at' => $baseDate->copy()->subDays(5),
            ]
        );

        // Create a few more single-product orders
        for ($i = 0; $i < 5; $i++) {
            $productIndex = $i % $products->count();
            $quantity = rand(1, 2);
            $product = $products[$productIndex];
            
            $order = Order::create([
                'user_id' => $customer->id,
                'total_price' => $product->price * $quantity,
                'status' => 'approved',
                'order_status' => 'approved',
                'type' => 'online',
                'payment_status' => 'paid',
                'created_at' => $baseDate->copy()->subDays(rand(1, 14)),
            ]);
            
            $order->products()->attach([
                $product->id => ['quantity' => $quantity]
            ]);
            
            // Create sales order with auto-generated SO number
            SalesOrder::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'so_number' => $generateSoNumber(),
                    'user_id' => $customer->id,
                    'subtotal' => $order->total_price,
                    'shipping_fee' => 0,
                    'total_amount' => $order->total_price,
                    'status' => 'confirmed',
                    'confirmed_at' => $order->created_at,
                ]
            );
        }

        $this->command->info('Sales report data seeded successfully!');
    }
}

