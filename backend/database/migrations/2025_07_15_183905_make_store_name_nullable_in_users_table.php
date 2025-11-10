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
        Schema::table('users', function (Blueprint $table) {
            // Check if column exists, if not add it, if yes make it nullable
            if (Schema::hasColumn('users', 'store_name')) {
                $table->string('store_name')->nullable()->change();
            } else {
                $table->string('store_name')->nullable()->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('store_name')->nullable(false)->change();
        });
    }
};
