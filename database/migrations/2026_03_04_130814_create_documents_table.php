<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {

            $table->id();

            $table->string('type'); // order, ddt, invoice

            $table->string('number');

            $table->string('customer_name');

            $table->string('customer_vat')->nullable();

            $table->string('customer_address')->nullable();

            $table->date('date');

            $table->decimal('total',10,2)->default(0);

            $table->string('status')->default('draft');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};