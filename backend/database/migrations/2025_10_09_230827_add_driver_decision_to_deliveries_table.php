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
            $table->enum('driver_decision', ['accepted', 'declined'])->nullable()->after('status');
            $table->text('decline_reason')->nullable()->after('driver_decision');
            $table->timestamp('decision_at')->nullable()->after('decline_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['driver_decision', 'decline_reason', 'decision_at']);
        });
    }
};