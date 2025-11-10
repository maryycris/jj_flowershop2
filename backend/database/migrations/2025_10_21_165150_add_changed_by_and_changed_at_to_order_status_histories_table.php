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
        Schema::table('order_status_histories', function (Blueprint $table) {
            // Add changed_by and changed_at columns if they don't exist
            if (!Schema::hasColumn('order_status_histories', 'changed_by')) {
                $table->unsignedBigInteger('changed_by')->nullable()->after('message');
                $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('order_status_histories', 'changed_at')) {
                $table->timestamp('changed_at')->nullable()->after('changed_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_status_histories', function (Blueprint $table) {
            // Remove the foreign key constraint and columns
            if (Schema::hasColumn('order_status_histories', 'changed_by')) {
                $table->dropForeign(['changed_by']);
                $table->dropColumn('changed_by');
            }
            if (Schema::hasColumn('order_status_histories', 'changed_at')) {
                $table->dropColumn('changed_at');
            }
        });
    }
};
