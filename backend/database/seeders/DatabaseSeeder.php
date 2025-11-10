<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            UsersTableSeeder::class,
            ProductsTableSeeder::class,
            InventorySeeder::class,
            OrdersTableSeeder::class,
            DriverSeeder::class,
            GreenerySeeder::class,
            CreateAdminUserSeeder::class,
            CatalogProductsSeeder::class,
            CustomizeItemsSeeder::class,
            SalesReportSeeder::class,
        ]);

        // Seed a default store if not exists
        $store = Store::firstOrCreate([
            'name' => 'Main Branch',
        ], [
            'address' => 'Main St., City',
            'contact_number' => '09123456789',
        ]);
    }
}
