<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all wrapper products
        $wrappers = DB::table('products')
            ->where('category', 'Wrappers')
            ->get();

        foreach ($wrappers as $wrapper) {
            // Generate random price between 60 and 120
            $randomPrice = rand(60, 120);
            
            DB::table('products')
                ->where('id', $wrapper->id)
                ->update(['price' => $randomPrice]);
        }
    }

    /**
     * Reverse the migrations.
     * Note: This cannot be fully reversed as we don't store original prices
     */
    public function down(): void
    {
        // Cannot fully reverse - original prices are not stored
        // Could set a default price if needed
    }
};
