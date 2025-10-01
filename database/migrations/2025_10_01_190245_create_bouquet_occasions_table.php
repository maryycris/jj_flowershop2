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
        Schema::create('bouquet_occasions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Funeral", "Birthday", "Wedding"
            $table->string('slug')->unique(); // e.g., "funeral", "birthday", "wedding"
            $table->text('description')->nullable();
            $table->string('color_theme')->nullable(); // e.g., "White", "Pink", "Red"
            $table->json('recommended_flowers')->nullable(); // Array of recommended flower names
            $table->json('recommended_wrappers')->nullable(); // Array of recommended wrapper names
            $table->json('recommended_ribbons')->nullable(); // Array of recommended ribbon names
            $table->decimal('base_price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bouquet_occasions');
    }
};
