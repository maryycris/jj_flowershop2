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
            $table->date('delivery_date')->nullable()->after('status');
            $table->string('delivery_time')->nullable()->after('delivery_date');
            $table->string('recipient_name')->nullable()->after('delivery_time');
            $table->string('recipient_phone')->nullable()->after('recipient_name');
            $table->text('delivery_address')->nullable()->after('recipient_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['delivery_date', 'delivery_time', 'recipient_name', 'recipient_phone', 'delivery_address']);
        });
    }
};
