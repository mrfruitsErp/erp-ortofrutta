<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {

        Schema::create('purchases', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('supplier_id');

            $table->unsignedBigInteger('product_id');

            $table->decimal('kg',10,2);

            $table->decimal('price',10,2);

            $table->decimal('total',10,2);

            $table->date('date');

            $table->timestamps();

        });

    }

    public function down()
    {
        Schema::dropIfExists('purchases');
    }

};