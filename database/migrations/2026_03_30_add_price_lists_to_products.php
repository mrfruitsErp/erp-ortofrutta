<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'price_horeca')) {
                $table->decimal('price_horeca', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'price_dettaglio')) {
                $table->decimal('price_dettaglio', 10, 2)->nullable()->after('price_horeca');
            }
            if (!Schema::hasColumn('products', 'price_gdo')) {
                $table->decimal('price_gdo', 10, 2)->nullable()->after('price_dettaglio');
            }
        });

        // Popola i listini con il prezzo base esistente se price_list_items è vuoto
        DB::statement("
            INSERT INTO price_list_items (price_list_id, product_id, prezzo_override, created_at, updated_at)
            SELECT pl.id, p.id, p.price, NOW(), NOW()
            FROM products p
            CROSS JOIN price_lists pl
            WHERE NOT EXISTS (
                SELECT 1 FROM price_list_items pli
                WHERE pli.price_list_id = pl.id AND pli.product_id = p.id
            )
        ");
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            foreach (['price_horeca', 'price_dettaglio', 'price_gdo'] as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};