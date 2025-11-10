<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize: move any Packaging Materials that look like wrappers into Wrappers
        DB::table('products')
            ->where('category', 'Packaging Materials')
            ->where(function ($q) {
                $q->where('name', 'like', '%wrapper%')
                  ->orWhere('description', 'like', '%wrapper%');
            })
            ->update(['category' => 'Wrappers']);

        // Also handle common alternate spellings/cases
        DB::table('products')
            ->whereIn('category', ['Wrapper', 'wrapper'])
            ->update(['category' => 'Wrappers']);
    }

    public function down(): void
    {
        // Best-effort rollback: move back to Packaging Materials if they were Wrappers and have 'wrapper' in name/description
        DB::table('products')
            ->where('category', 'Wrappers')
            ->where(function ($q) {
                $q->where('name', 'like', '%wrapper%')
                  ->orWhere('description', 'like', '%wrapper%');
            })
            ->update(['category' => 'Packaging Materials']);
    }
};


