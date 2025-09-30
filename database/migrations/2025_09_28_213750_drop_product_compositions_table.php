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
        Schema::dropIfExists('product_compositions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('product_compositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('component_id');
            $table->string('component_name');
            $table->decimal('quantity', 8, 2);
            $table->string('unit');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
};
