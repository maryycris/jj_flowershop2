<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->decimal('shipping_fee', 8, 2)->nullable()->after('delivery_address');
        });
    }

    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('shipping_fee');
        });
    }
}; 