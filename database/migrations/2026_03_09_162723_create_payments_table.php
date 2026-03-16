<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('document_id');

            $table->decimal('amount',10,2);

            $table->date('payment_date');

            $table->string('method')->nullable();

            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }

};