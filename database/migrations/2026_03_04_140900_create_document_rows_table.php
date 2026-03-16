<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_rows', function (Blueprint $table) {

            $table->id();

            $table->foreignId('document_id');

            $table->foreignId('product_id');

            $table->integer('boxes');

            $table->decimal('kg_estimated',8,2);

            $table->decimal('kg_real',8,2)->nullable();

            $table->decimal('price_per_kg',8,2);

            $table->decimal('total',10,2);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_rows');
    }
};