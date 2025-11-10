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
        Schema::create('customize_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('inventory_item_id')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            
            $table->foreign('inventory_item_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customize_items');
    }
};