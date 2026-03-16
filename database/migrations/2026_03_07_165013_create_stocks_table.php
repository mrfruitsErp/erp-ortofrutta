<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->decimal('quantity',10,3)->default(0);

            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};