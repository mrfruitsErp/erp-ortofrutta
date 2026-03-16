<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
{

Schema::create('orders', function (Blueprint $table) {

$table->id();

$table->unsignedBigInteger('client_id');

$table->string('number');

$table->date('date');

$table->decimal('total',10,2)->default(0);

$table->string('status')->default('open');

$table->timestamps();

$table->foreign('client_id')
->references('id')
->on('clients')
->onDelete('cascade');

});

}

public function down(): void
{

Schema::dropIfExists('orders');

}

};