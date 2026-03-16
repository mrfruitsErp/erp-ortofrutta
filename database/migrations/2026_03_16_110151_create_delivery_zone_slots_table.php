<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
{

Schema::create('delivery_zone_slots', function (Blueprint $table) {

$table->id();

$table->unsignedBigInteger('zone_id');

$table->unsignedBigInteger('slot_id');

$table->timestamps();

$table->foreign('zone_id')->references('id')->on('delivery_zones')->cascadeOnDelete();

$table->foreign('slot_id')->references('id')->on('delivery_slots')->cascadeOnDelete();

});

}

public function down(): void
{

Schema::dropIfExists('delivery_zone_slots');

}

};
