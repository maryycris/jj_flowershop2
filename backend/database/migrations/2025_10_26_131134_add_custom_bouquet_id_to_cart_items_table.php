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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('custom_bouquet_id')->nullable()->constrained('custom_bouquets')->onDelete('cascade');
            $table->string('item_type')->default('product'); // 'product' or 'custom_bouquet'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['custom_bouquet_id']);
            $table->dropColumn(['custom_bouquet_id', 'item_type']);
        });
    }
};
