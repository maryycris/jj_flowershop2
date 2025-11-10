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
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('orders', 'return_notes')) {
                $table->text('return_notes')->nullable()->after('return_reason');
            }
            if (!Schema::hasColumn('orders', 'return_status')) {
                $table->string('return_status')->default('pending')->after('returned_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'return_notes')) {
                $table->dropColumn('return_notes');
            }
            if (Schema::hasColumn('orders', 'return_status')) {
                $table->dropColumn('return_status');
            }
        });
    }
};