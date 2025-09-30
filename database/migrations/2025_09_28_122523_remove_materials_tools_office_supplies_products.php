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
        // Delete products with "Materials, Tools, and Equipment" and "Office Supplies" categories
        DB::table('products')
            ->whereIn('category', ['Materials, Tools, and Equipment', 'Office Supplies'])
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed as we're deleting data
        // If you need to restore, you would need to restore from a backup
    }
};
