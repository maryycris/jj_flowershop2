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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('license_number')->unique();
            $table->string('vehicle_type')->nullable(); // motorcycle, car, van
            $table->string('vehicle_plate')->nullable();
            $table->enum('availability_status', ['available', 'busy', 'off_duty', 'on_delivery'])->default('available');
            $table->time('work_start_time')->default('08:00');
            $table->time('work_end_time')->default('17:00');
            $table->json('delivery_areas')->nullable(); // areas they can deliver to
            $table->integer('max_deliveries_per_day')->default(10);
            $table->integer('current_deliveries_today')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
