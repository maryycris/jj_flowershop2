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
        // Fix inconsistent category names
        DB::table('products')
            ->where('category', 'Fillers')
            ->update(['category' => 'Artificial Flowers']);
            
        DB::table('products')
            ->where('category', 'Greeneries')
            ->update(['category' => 'Greenery']);
            
        DB::table('products')
            ->where('category', 'Wrapper')
            ->update(['category' => 'Wrappers']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert category names back to original
        DB::table('products')
            ->where('category', 'Artificial Flowers')
            ->update(['category' => 'Fillers']);
            
        DB::table('products')
            ->where('category', 'Greenery')
            ->update(['category' => 'Greeneries']);
            
        DB::table('products')
            ->where('category', 'Wrappers')
            ->update(['category' => 'Wrapper']);
    }
};
