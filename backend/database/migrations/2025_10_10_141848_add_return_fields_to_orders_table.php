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
            $table->timestamp('returned_at')->nullable();
            $table->text('return_reason')->nullable();
            $table->text('return_notes')->nullable()->after('return_reason');
            $table->unsignedBigInteger('returned_by')->nullable();
            $table->string('return_status')->default('pending')->after('returned_by');
            
            $table->foreign('returned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['returned_by']);
            $table->dropColumn(['returned_at', 'return_reason', 'return_notes', 'returned_by', 'return_status']);
        });
    }
};