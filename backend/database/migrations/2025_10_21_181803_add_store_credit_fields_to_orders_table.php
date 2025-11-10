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
            $table->decimal('store_credit_used', 10, 2)->nullable()->after('admin_notes');
            $table->unsignedBigInteger('store_credit_order_id')->nullable()->after('store_credit_used');
            
            $table->foreign('store_credit_order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['store_credit_order_id']);
            $table->dropColumn(['store_credit_used', 'store_credit_order_id']);
        });
    }
};