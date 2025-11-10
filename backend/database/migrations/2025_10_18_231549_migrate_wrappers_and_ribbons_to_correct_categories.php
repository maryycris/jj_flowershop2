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
        // Migrate wrappers from Packaging Materials to Wrappers category
        $wrapperCount = DB::table('products')
            ->where('category', 'Packaging Materials')
            ->where(function ($query) {
                $query->where('name', 'like', '%wrapper%')
                      ->orWhere('description', 'like', '%wrapper%')
                      ->orWhere('name', 'like', '%wrap%')
                      ->orWhere('description', 'like', '%wrap%');
            })
            ->update(['category' => 'Wrappers']);

        // Migrate ribbons from Floral Supplies to Ribbon category
        $ribbonCount = DB::table('products')
            ->where('category', 'Floral Supplies')
            ->where(function ($query) {
                $query->where('name', 'like', '%ribbon%')
                      ->orWhere('description', 'like', '%ribbon%');
            })
            ->update(['category' => 'Ribbon']);

        // Also handle any products that might be in incorrect case variations
        DB::table('products')
            ->whereIn('category', ['Wrapper', 'wrapper', 'wrappers'])
            ->update(['category' => 'Wrappers']);

        DB::table('products')
            ->whereIn('category', ['Ribbons', 'ribbon', 'ribbons'])
            ->update(['category' => 'Ribbon']);

        echo "Migration completed:\n";
        echo "- Moved {$wrapperCount} wrapper products from Packaging Materials to Wrappers\n";
        echo "- Moved {$ribbonCount} ribbon products from Floral Supplies to Ribbon\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: move wrappers back to Packaging Materials
        DB::table('products')
            ->where('category', 'Wrappers')
            ->where(function ($query) {
                $query->where('name', 'like', '%wrapper%')
                      ->orWhere('description', 'like', '%wrapper%')
                      ->orWhere('name', 'like', '%wrap%')
                      ->orWhere('description', 'like', '%wrap%');
            })
            ->update(['category' => 'Packaging Materials']);

        // Rollback: move ribbons back to Floral Supplies
        DB::table('products')
            ->where('category', 'Ribbon')
            ->where(function ($query) {
                $query->where('name', 'like', '%ribbon%')
                      ->orWhere('description', 'like', '%ribbon%');
            })
            ->update(['category' => 'Floral Supplies']);
    }
};
