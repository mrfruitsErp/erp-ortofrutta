<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Existing products columns:
        // id, name, sku, origin, price, cost_price, vat_rate, modalita_vendita,
        // step_grammi, stock_legacy, tara, avg_box_weight, pieces_per_box,
        // avg_unit_weight, created_at, updated_at, min_margin, category,
        // disponibilita, ordine_min

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'ordine_min_kg')) {
                $table->decimal('ordine_min_kg', 8, 3)->nullable()->after('ordine_min');
            }
            if (!Schema::hasColumn('products', 'ordine_max')) {
                $table->decimal('ordine_max', 8, 3)->nullable()->after('ordine_min_kg');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'ordine_max'))    $table->dropColumn('ordine_max');
            if (Schema::hasColumn('products', 'ordine_min_kg')) $table->dropColumn('ordine_min_kg');
        });
    }
};