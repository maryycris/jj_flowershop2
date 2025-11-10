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
            // Add refund columns if they don't exist
            if (!Schema::hasColumn('orders', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable()->after('admin_notes');
            }
            if (!Schema::hasColumn('orders', 'refund_reason')) {
                $table->string('refund_reason')->nullable()->after('refund_amount');
            }
            if (!Schema::hasColumn('orders', 'refund_method')) {
                $table->string('refund_method')->nullable()->after('refund_reason');
            }
            if (!Schema::hasColumn('orders', 'refund_processed_at')) {
                $table->timestamp('refund_processed_at')->nullable()->after('refund_method');
            }
            if (!Schema::hasColumn('orders', 'refund_processed_by')) {
                $table->unsignedBigInteger('refund_processed_by')->nullable()->after('refund_processed_at');
                $table->foreign('refund_processed_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remove foreign key constraint and refund columns
            if (Schema::hasColumn('orders', 'refund_processed_by')) {
                $table->dropForeign(['refund_processed_by']);
            }
            $table->dropColumn([
                'refund_amount', 'refund_reason', 'refund_method', 
                'refund_processed_at', 'refund_processed_by'
            ]);
        });
    }
};
