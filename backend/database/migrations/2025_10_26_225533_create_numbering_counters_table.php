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
        Schema::create('numbering_counters', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // 'sales_order', 'invoice', 'order'
            $table->string('prefix'); // 'SO-', 'INV-', 'ORD-'
            $table->integer('current_number')->default(0);
            $table->integer('padding_length')->default(5); // 00001, 00002, etc.
            $table->timestamps();
        });

        // Insert initial counters
        DB::table('numbering_counters')->insert([
            [
                'type' => 'sales_order',
                'prefix' => 'SO-',
                'current_number' => 0,
                'padding_length' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'invoice',
                'prefix' => 'INV-',
                'current_number' => 0,
                'padding_length' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'order',
                'prefix' => 'ORD-',
                'current_number' => 0,
                'padding_length' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('numbering_counters');
    }
};