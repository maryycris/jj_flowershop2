<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure orders table no longer requires product_id/quantity
        if (Schema::hasTable('orders')) {
            // Drop FK by name if exists, then columns
            if (Schema::hasColumn('orders', 'product_id')) {
                try {
                    \DB::statement('ALTER TABLE `orders` DROP FOREIGN KEY `orders_product_id_foreign`');
                } catch (\Throwable $e) {}
            }
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'product_id')) {
                    $table->dropColumn('product_id');
                }
                if (Schema::hasColumn('orders', 'quantity')) {
                    $table->dropColumn('quantity');
                }
            });
        }

        // Create pivot if missing
        if (!Schema::hasTable('order_product')) {
            Schema::create('order_product', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->integer('quantity');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Drop pivot and restore columns (best effort)
        if (Schema::hasTable('order_product')) {
            Schema::dropIfExists('order_product');
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable();
                }
                if (!Schema::hasColumn('orders', 'quantity')) {
                    $table->integer('quantity')->default(1);
                }
            });
        }
    }
};


