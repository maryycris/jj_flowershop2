<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Order::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // $customer = User::where('role', 'customer')->first();
        // $products = Product::all();

        // if ($customer && $products->count() > 0) {
        //     foreach ($products as $product) {
        //         $quantity = rand(1, 3);
        //         $totalPrice = $product->price * $quantity;

        //         Order::create([
        //             'user_id' => $customer->id,
        //             'product_id' => $product->id,
        //             'quantity' => $quantity,
        //             'total_price' => $totalPrice,
        //         ]);
        //     }
        // }
    }
}
