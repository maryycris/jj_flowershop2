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
        if (!Schema::hasTable('pending_inventory_changes')) {
            Schema::create('pending_inventory_changes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->enum('action', ['edit', 'delete']);
                $table->json('changes')->nullable();
                $table->unsignedBigInteger('submitted_by');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();

                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                $table->foreign('submitted_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally do not drop table if it already exists from previous migration
        // to avoid accidentally removing data. Only drop if this migration created it.
        if (Schema::hasTable('pending_inventory_changes')) {
            // No-op: keep existing table
        }
    }
};


