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
        // Modify the order_status enum to include 'delivered'
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('pending', 'approved', 'on_delivery', 'delivered', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'delivered' from the enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('pending', 'approved', 'on_delivery', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};