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
        Schema::table('users', function (Blueprint $table) {
            // All these columns already exist in your table, so don't add them again:
            // $table->string('last_name')->nullable();
            // $table->string('street_address')->nullable();
            // $table->string('barangay')->nullable();
            // $table->string('municipality')->nullable();
            // $table->string('city')->nullable();
            // $table->string('contact_number')->nullable();

            // Only add columns that do NOT exist yet:
            $table->string('address')->nullable(); // Add this only if it does not exist yet
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address']); // Only drop what you added in up()
        });
    }
};
