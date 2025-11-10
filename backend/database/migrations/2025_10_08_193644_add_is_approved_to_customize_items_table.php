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
        if (Schema::hasTable('customize_items') && !Schema::hasColumn('customize_items', 'is_approved')) {
        Schema::table('customize_items', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('status');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customize_items', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};
