<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add order status tracking fields
            $table->enum('order_status', ['pending', 'approved', 'on_delivery', 'completed', 'cancelled'])->default('pending')->after('status');
            $table->timestamp('approved_at')->nullable()->after('order_status');
            $table->timestamp('on_delivery_at')->nullable()->after('approved_at');
            $table->timestamp('completed_at')->nullable()->after('on_delivery_at');
            $table->unsignedBigInteger('approved_by')->nullable()->after('completed_at');
            $table->unsignedBigInteger('assigned_driver_id')->nullable()->after('approved_by');
            
            // Add foreign key constraints
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_driver_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['assigned_driver_id']);
            $table->dropColumn([
                'order_status',
                'approved_at',
                'on_delivery_at', 
                'completed_at',
                'approved_by',
                'assigned_driver_id'
            ]);
        });
    }
};