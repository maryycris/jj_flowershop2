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
        Schema::table('events', function (Blueprint $table) {
            $table->integer('guest_count')->nullable()->after('recipient_phone');
            $table->text('personalized_message')->nullable()->after('guest_count');
            $table->text('special_instructions')->nullable()->after('personalized_message');
            $table->string('color_scheme')->nullable()->after('special_instructions');
            $table->string('contact_phone')->nullable()->after('color_scheme');
            $table->string('contact_email')->nullable()->after('contact_phone');
            $table->decimal('subtotal', 10, 2)->default(0)->after('status');
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('subtotal');
            $table->decimal('service_fee', 10, 2)->default(0)->after('delivery_fee');
            $table->decimal('total', 10, 2)->default(0)->after('service_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'guest_count',
                'personalized_message',
                'special_instructions',
                'color_scheme',
                'contact_phone',
                'contact_email',
                'subtotal',
                'delivery_fee',
                'service_fee',
                'total'
            ]);
        });
    }
};
