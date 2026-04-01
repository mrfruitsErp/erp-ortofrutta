<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('delivery_time_slots', function (Blueprint $table) {
            $table->integer('max_orders')->default(20);
        });
    }

    public function down()
    {
        Schema::table('delivery_time_slots', function (Blueprint $table) {
            $table->dropColumn('max_orders');
        });
    }
};