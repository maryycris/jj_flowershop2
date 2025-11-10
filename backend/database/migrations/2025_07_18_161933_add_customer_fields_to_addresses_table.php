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
        if (!Schema::hasColumn('addresses', 'first_name')) {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            });
        }
        if (!Schema::hasColumn('addresses', 'last_name')) {
            Schema::table('addresses', function (Blueprint $table) {
            $table->string('last_name')->nullable();
            });
        }
        if (!Schema::hasColumn('addresses', 'company')) {
            Schema::table('addresses', function (Blueprint $table) {
            $table->string('company')->nullable();
            });
        }
        if (!Schema::hasColumn('addresses', 'region')) {
            Schema::table('addresses', function (Blueprint $table) {
            $table->string('region')->nullable();
            });
        }
        if (!Schema::hasColumn('addresses', 'phone_number')) {
            Schema::table('addresses', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'company', 'region', 'phone_number']);
        });
    }
};
