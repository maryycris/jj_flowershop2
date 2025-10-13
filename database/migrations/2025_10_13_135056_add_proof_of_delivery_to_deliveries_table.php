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
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('proof_of_delivery_image')->nullable()->after('status');
            $table->timestamp('proof_of_delivery_taken_at')->nullable()->after('proof_of_delivery_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['proof_of_delivery_image', 'proof_of_delivery_taken_at']);
        });
    }
};
