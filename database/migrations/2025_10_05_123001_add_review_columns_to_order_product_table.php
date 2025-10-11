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
        Schema::table('order_product', function (Blueprint $table) {
            $table->integer('rating')->nullable()->after('quantity');
            $table->text('review_comment')->nullable()->after('rating');
            $table->boolean('reviewed')->default(false)->after('review_comment');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_product', function (Blueprint $table) {
            $table->dropColumn(['rating', 'review_comment', 'reviewed', 'reviewed_at']);
        });
    }
};