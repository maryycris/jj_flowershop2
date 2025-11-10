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
            // Only add columns if they do NOT exist yet
            if (!Schema::hasColumn('users', 'verification_code')) {
                $table->string('verification_code')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false);
            }
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'facebook_id')) {
                $table->string('facebook_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'verification_expires_at')) {
                $table->timestamp('verification_expires_at')->nullable();
            }
            // Remove/comment out address fields that already exist
            // if (!Schema::hasColumn('users', 'street_address')) {
            //     $table->string('street_address')->nullable();
            // }
            // if (!Schema::hasColumn('users', 'barangay')) {
            //     $table->string('barangay')->nullable();
            // }
            // if (!Schema::hasColumn('users', 'municipality')) {
            //     $table->string('municipality')->nullable();
            // }
            // if (!Schema::hasColumn('users', 'city')) {
            //     $table->string('city')->nullable();
            // }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('verification_code');
            $table->dropColumn('is_verified');
        });
    }
};
