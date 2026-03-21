<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            if (!Schema::hasColumn('products', 'category')) {
                $table->string('category')->nullable();
            }

            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->unique();
            }

            if (!Schema::hasColumn('products', 'origin')) {
                $table->string('origin')->nullable();
            }

            if (!Schema::hasColumn('products', 'vat_rate')) {
                $table->integer('vat_rate')->default(4);
            }

            if (!Schema::hasColumn('products', 'modalita_vendita')) {
                $table->string('modalita_vendita')->nullable();
            }

            if (!Schema::hasColumn('products', 'avg_box_weight')) {
                $table->decimal('avg_box_weight',10,3)->default(0);
            }

            if (!Schema::hasColumn('products', 'tara')) {
                $table->decimal('tara',10,3)->default(0);
            }

            if (!Schema::hasColumn('products', 'pieces_per_box')) {
                $table->integer('pieces_per_box')->default(0);
            }

            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price',10,2)->default(0);
            }

            if (!Schema::hasColumn('products', 'disponibilita')) {
                $table->string('disponibilita')->default('disponibile');
            }

            if (!Schema::hasColumn('products', 'ordine_min')) {
                $table->decimal('ordine_min',10,3)->default(1);
            }

        });
    }

    public function down(): void {}
};