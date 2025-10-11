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
        // Remove duplicate status history entries
        // Keep only the first occurrence of each status per order within the same minute
        DB::statement("
            DELETE osh1 FROM order_status_histories osh1
            INNER JOIN order_status_histories osh2 
            WHERE osh1.id > osh2.id 
            AND osh1.order_id = osh2.order_id 
            AND osh1.status = osh2.status 
            AND osh1.message = osh2.message
            AND DATE_FORMAT(osh1.created_at, '%Y-%m-%d %H:%i') = DATE_FORMAT(osh2.created_at, '%Y-%m-%d %H:%i')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed as it deletes data
        // The duplicates were already removed
    }
};
