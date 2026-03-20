<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_product_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('prezzo_override', 10, 2)->nullable();
            $table->decimal('min_override', 10, 3)->nullable();
            $table->decimal('max_override', 10, 3)->nullable();
            $table->string('modalita_override', 20)->nullable();
            $table->boolean('bloccato')->default(false);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['client_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_product_overrides');
    }
};