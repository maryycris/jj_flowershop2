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
        Schema::create('custom_bouquets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('bouquet_type', ['regular', 'money'])->default('regular');
            $table->string('wrapper')->nullable();
            $table->string('focal_flower_1')->nullable();
            $table->string('focal_flower_2')->nullable();
            $table->string('focal_flower_3')->nullable();
            $table->string('greenery')->nullable();
            $table->string('filler')->nullable();
            $table->string('ribbon')->nullable();
            $table->decimal('money_amount', 10, 2)->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('total_price', 10, 2);
            $table->text('customization_data')->nullable(); // JSON data for all selections
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_bouquets');
    }
};
