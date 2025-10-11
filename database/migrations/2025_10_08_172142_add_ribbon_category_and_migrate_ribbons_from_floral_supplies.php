<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Move products from Floral Supplies to Ribbon category if they contain "ribbon" in name or description
        DB::table('products')
            ->where('category', 'Floral Supplies')
            ->where(function ($q) {
                $q->where('name', 'like', '%ribbon%')
                  ->orWhere('description', 'like', '%ribbon%');
            })
            ->update(['category' => 'Ribbon']);

        // Also handle common alternate spellings/cases
        DB::table('products')
            ->whereIn('category', ['Ribbons', 'ribbon', 'ribbons'])
            ->update(['category' => 'Ribbon']);
    }

    public function down(): void
    {
        // Rollback: move back to Floral Supplies if they were Ribbon and have 'ribbon' in name/description
        DB::table('products')
            ->where('category', 'Ribbon')
            ->where(function ($q) {
                $q->where('name', 'like', '%ribbon%')
                  ->orWhere('description', 'like', '%ribbon%');
            })
            ->update(['category' => 'Floral Supplies']);
    }
};