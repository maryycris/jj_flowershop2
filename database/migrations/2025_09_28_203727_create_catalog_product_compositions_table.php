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
        Schema::create('catalog_product_compositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_product_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('component_id'); // References inventory product ID
            $table->string('component_name');
            $table->decimal('quantity', 8, 2);
            $table->string('unit');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_product_compositions');
    }
};
