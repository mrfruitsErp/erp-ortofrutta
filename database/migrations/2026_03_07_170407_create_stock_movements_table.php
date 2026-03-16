<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->unsignedBigInteger('document_id')->nullable();

            $table->string('type');

            $table->decimal('qty',10,3);

            $table->date('movement_date');

            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
};