<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update category names in products table
        DB::table('products')
            ->where('category', 'Dried Flowers')
            ->update(['category' => 'Greenery']);
            
        DB::table('products')
            ->where('category', 'Floral Supplies')
            ->update(['category' => 'Ribbons']);
            
        DB::table('products')
            ->where('category', 'Packaging Materials')
            ->update(['category' => 'Wrappers']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert category names back to original
        DB::table('products')
            ->where('category', 'Greenery')
            ->update(['category' => 'Dried Flowers']);
            
        DB::table('products')
            ->where('category', 'Ribbons')
            ->update(['category' => 'Floral Supplies']);
            
        DB::table('products')
            ->where('category', 'Wrappers')
            ->update(['category' => 'Packaging Materials']);
    }
};
