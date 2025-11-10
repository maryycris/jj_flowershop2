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
        // Remove duplicate assigned status for order 182
        // Keep only the first one, delete the second one
        DB::statement("
            DELETE FROM order_status_histories 
            WHERE id IN (
                SELECT id FROM (
                    SELECT id, ROW_NUMBER() OVER (
                        PARTITION BY order_id, status, message, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') 
                        ORDER BY id
                    ) as row_num
                    FROM order_status_histories
                    WHERE order_id = 182 AND status = 'assigned'
                ) as ranked
                WHERE row_num > 1
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed as it deletes data
    }
};
