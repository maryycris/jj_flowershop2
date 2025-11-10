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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_marked_for_deletion')->default(false)->after('status');
            $table->unsignedBigInteger('marked_for_deletion_by')->nullable()->after('is_marked_for_deletion');
            $table->timestamp('marked_for_deletion_at')->nullable()->after('marked_for_deletion_by');
            
            $table->foreign('marked_for_deletion_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['marked_for_deletion_by']);
            $table->dropColumn(['is_marked_for_deletion', 'marked_for_deletion_by', 'marked_for_deletion_at']);
        });
    }
};
