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
        Schema::table('payments', function (Blueprint $table) {
            // Add missing columns
            if (!Schema::hasColumn('payments', 'payment_number')) {
                $table->string('payment_number')->unique()->after('id');
            }
            if (!Schema::hasColumn('payments', 'order_id')) {
                $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade')->after('invoice_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn(['payment_number', 'order_id']);
        });
    }
};