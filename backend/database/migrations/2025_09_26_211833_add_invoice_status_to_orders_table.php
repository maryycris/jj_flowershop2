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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('invoice_status', ['draft', 'ready', 'paid', 'overdue'])->default('draft')->after('order_status');
            $table->timestamp('invoice_generated_at')->nullable()->after('invoice_status');
            $table->timestamp('invoice_paid_at')->nullable()->after('invoice_generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['invoice_status', 'invoice_generated_at', 'invoice_paid_at']);
        });
    }
};