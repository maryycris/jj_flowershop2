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
        // Modify the existing 'orders' table
        Schema::table('orders', function (Blueprint $table) {
            // Make total_price nullable temporarily for removal, or set a default
            $table->decimal('total_price', 10, 2)->nullable()->change();
            
            // Drop foreign key constraints first if they exist
            $table->dropForeign('orders_product_id_foreign');

            // Remove product_id and quantity from orders table
            $table->dropColumn('product_id');
            $table->dropColumn('quantity');
        });

        // Create the pivot table for many-to-many relationship between orders and products
        Schema::create('order_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });

        // Re-add total_price as not nullable if it was changed, and ensure it's fillable in model
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_price', 10, 2)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the pivot table
        Schema::dropIfExists('order_product');

        // Revert changes to the 'orders' table
        Schema::table('orders', function (Blueprint $table) {
            // Re-add product_id and quantity columns
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');

            // Re-add foreign key constraint
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};
