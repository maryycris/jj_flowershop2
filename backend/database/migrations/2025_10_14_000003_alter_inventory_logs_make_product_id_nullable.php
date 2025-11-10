<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inventory_logs')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                // Drop existing FK if present
                try { $table->dropForeign(['product_id']); } catch (Throwable $e) {}
            });

            Schema::table('inventory_logs', function (Blueprint $table) {
                try { $table->unsignedBigInteger('product_id')->nullable()->change(); } catch (Throwable $e) {}
            });

            Schema::table('inventory_logs', function (Blueprint $table) {
                try { $table->foreign('product_id')->references('id')->on('products')->nullOnDelete(); } catch (Throwable $e) {}
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventory_logs')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                try { $table->dropForeign(['product_id']); } catch (Throwable $e) {}
            });

            Schema::table('inventory_logs', function (Blueprint $table) {
                try { $table->unsignedBigInteger('product_id')->nullable(false)->change(); } catch (Throwable $e) {}
            });

            Schema::table('inventory_logs', function (Blueprint $table) {
                try { $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete(); } catch (Throwable $e) {}
            });
        }
    }
};


