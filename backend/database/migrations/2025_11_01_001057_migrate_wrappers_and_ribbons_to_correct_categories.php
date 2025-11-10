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
        // Move wrappers from "Packaging Materials" to "Wrappers"
        $wrapperItemIds = DB::table('customize_items')
            ->where('category', 'Wrapper')
            ->whereNotNull('inventory_item_id')
            ->pluck('inventory_item_id')
            ->toArray();

        if (!empty($wrapperItemIds)) {
            DB::table('products')
                ->whereIn('id', $wrapperItemIds)
                ->where('category', 'Packaging Materials')
                ->update(['category' => 'Wrappers']);
        }

        // Also move by name pattern for wrappers (in case not linked via customize_items)
        DB::table('products')
            ->where('category', 'Packaging Materials')
            ->where(function($query) {
                $query->where('name', 'LIKE', '%wrapper%')
                      ->orWhere('name', 'LIKE', '%Wrapper%')
                      ->orWhere('name', 'LIKE', '%wrap%')
                      ->orWhere('name', 'LIKE', '%Wrap%');
            })
            ->update(['category' => 'Wrappers']);

        // Move ribbons from "Floral Supplies" to "Ribbon"
        $ribbonItemIds = DB::table('customize_items')
            ->whereIn('category', ['Ribbon', 'Ribbons'])
            ->whereNotNull('inventory_item_id')
            ->pluck('inventory_item_id')
            ->toArray();

        if (!empty($ribbonItemIds)) {
            DB::table('products')
                ->whereIn('id', $ribbonItemIds)
                ->where('category', 'Floral Supplies')
                ->update(['category' => 'Ribbon']);
        }

        // Also move by name pattern for ribbons (in case not linked via customize_items)
        DB::table('products')
            ->where('category', 'Floral Supplies')
            ->where(function($query) {
                $query->where('name', 'LIKE', '%ribbon%')
                      ->orWhere('name', 'LIKE', '%Ribbon%')
                      ->orWhere('name', 'LIKE', '%ribbons%')
                      ->orWhere('name', 'LIKE', '%Ribbons%');
            })
            ->update(['category' => 'Ribbon']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Move wrappers back to "Packaging Materials"
        $wrapperItemIds = DB::table('customize_items')
            ->where('category', 'Wrapper')
            ->whereNotNull('inventory_item_id')
            ->pluck('inventory_item_id')
            ->toArray();

        if (!empty($wrapperItemIds)) {
            DB::table('products')
                ->whereIn('id', $wrapperItemIds)
                ->where('category', 'Wrappers')
                ->update(['category' => 'Packaging Materials']);
        }

        DB::table('products')
            ->where('category', 'Wrappers')
            ->where(function($query) {
                $query->where('name', 'LIKE', '%wrapper%')
                      ->orWhere('name', 'LIKE', '%Wrapper%')
                      ->orWhere('name', 'LIKE', '%wrap%')
                      ->orWhere('name', 'LIKE', '%Wrap%');
            })
            ->update(['category' => 'Packaging Materials']);

        // Move ribbons back to "Floral Supplies"
        $ribbonItemIds = DB::table('customize_items')
            ->whereIn('category', ['Ribbon', 'Ribbons'])
            ->whereNotNull('inventory_item_id')
            ->pluck('inventory_item_id')
            ->toArray();

        if (!empty($ribbonItemIds)) {
            DB::table('products')
                ->whereIn('id', $ribbonItemIds)
                ->where('category', 'Ribbon')
                ->update(['category' => 'Floral Supplies']);
        }

        DB::table('products')
            ->where('category', 'Ribbon')
            ->where(function($query) {
                $query->where('name', 'LIKE', '%ribbon%')
                      ->orWhere('name', 'LIKE', '%Ribbon%')
                      ->orWhere('name', 'LIKE', '%ribbons%')
                      ->orWhere('name', 'LIKE', '%Ribbons%');
            })
            ->update(['category' => 'Floral Supplies']);
    }
};
