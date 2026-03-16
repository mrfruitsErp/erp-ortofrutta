<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('stock_movements', function (Blueprint $table) {

            $table->unsignedBigInteger('company_id')->nullable()->after('id');

            $table->decimal('weight',10,2)->nullable()->after('qty');

            $table->decimal('price',10,2)->nullable()->after('weight');

        });
    }


    public function down()
    {
        Schema::table('stock_movements', function (Blueprint $table) {

            $table->dropColumn('company_id');
            $table->dropColumn('weight');
            $table->dropColumn('price');

        });
    }

};