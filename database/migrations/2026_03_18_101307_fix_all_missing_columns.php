<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── PRODUCTS ────────────────────────────────────────────────────────
        Schema::table('products', function (Blueprint $table) {

            if (!Schema::hasColumn('products', 'sale_type')) {
                $table->string('sale_type', 20)->default('kg')->after('unit');
            }
            if (!Schema::hasColumn('products', 'avg_box_weight')) {
                $table->decimal('avg_box_weight', 8, 3)->nullable()->after('tara');
            }
            if (!Schema::hasColumn('products', 'pieces_per_box')) {
                $table->integer('pieces_per_box')->nullable()->after('avg_box_weight');
            }
            if (!Schema::hasColumn('products', 'avg_unit_weight')) {
                $table->decimal('avg_unit_weight', 8, 3)->nullable()->after('pieces_per_box');
            }
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'vat_rate')) {
                $table->decimal('vat_rate', 5, 2)->default(4)->after('cost_price');
            }
        });

        // ─── ORDER ITEMS ─────────────────────────────────────────────────────
        Schema::table('order_items', function (Blueprint $table) {

            if (!Schema::hasColumn('order_items', 'kg_estimated')) {
                $table->decimal('kg_estimated', 10, 3)->nullable()->after('qty');
            }
            if (!Schema::hasColumn('order_items', 'kg_real')) {
                $table->decimal('kg_real', 10, 3)->nullable()->after('kg_estimated');
            }
            if (!Schema::hasColumn('order_items', 'tara')) {
                $table->decimal('tara', 8, 3)->nullable()->after('kg_real');
            }
            if (!Schema::hasColumn('order_items', 'kg_net')) {
                $table->decimal('kg_net', 10, 3)->nullable()->after('tara');
            }
            if (!Schema::hasColumn('order_items', 'origin')) {
                $table->string('origin', 10)->nullable()->after('kg_net');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('products', 'sale_type')       ? 'sale_type'       : null,
                Schema::hasColumn('products', 'avg_box_weight')  ? 'avg_box_weight'  : null,
                Schema::hasColumn('products', 'pieces_per_box')  ? 'pieces_per_box'  : null,
                Schema::hasColumn('products', 'avg_unit_weight') ? 'avg_unit_weight' : null,
                Schema::hasColumn('products', 'cost_price')      ? 'cost_price'      : null,
                Schema::hasColumn('products', 'vat_rate')        ? 'vat_rate'        : null,
            ]));
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('order_items', 'kg_estimated') ? 'kg_estimated' : null,
                Schema::hasColumn('order_items', 'kg_real')      ? 'kg_real'      : null,
                Schema::hasColumn('order_items', 'tara')         ? 'tara'         : null,
                Schema::hasColumn('order_items', 'kg_net')       ? 'kg_net'       : null,
                Schema::hasColumn('order_items', 'origin')       ? 'origin'       : null,
            ]));
        });
    }
};