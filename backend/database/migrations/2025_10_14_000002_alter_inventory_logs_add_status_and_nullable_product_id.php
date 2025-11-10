<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_logs', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('action');
            }
            try {
                $table->unsignedBigInteger('product_id')->nullable()->change();
            } catch (Throwable $e) {
                // ignore if platform doesn't support change here
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_logs', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};


