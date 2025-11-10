<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_product', function (Blueprint $table) {
            if (!Schema::hasColumn('order_product', 'rating')) {
                $table->unsignedTinyInteger('rating')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('order_product', 'review_comment')) {
                $table->text('review_comment')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('order_product', 'reviewed')) {
                $table->boolean('reviewed')->default(false)->after('review_comment');
            }
            if (!Schema::hasColumn('order_product', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_product', function (Blueprint $table) {
            if (Schema::hasColumn('order_product', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('order_product', 'reviewed')) {
                $table->dropColumn('reviewed');
            }
            if (Schema::hasColumn('order_product', 'review_comment')) {
                $table->dropColumn('review_comment');
            }
            if (Schema::hasColumn('order_product', 'rating')) {
                $table->dropColumn('rating');
            }
        });
    }
};


