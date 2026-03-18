<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 🔹 1. Allinea cost → cost_price (se serve)
        if (Schema::hasColumn('products', 'cost') && Schema::hasColumn('products', 'cost_price')) {

            DB::statement("
                UPDATE products 
                SET cost_price = cost 
                WHERE (cost_price IS NULL OR cost_price = 0)
            ");
        }

        // 🔹 2. Rimuovi colonna vecchia cost
        if (Schema::hasColumn('products', 'cost')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('cost');
            });
        }

        // 🔹 3. Default coerenti
        Schema::table('products', function (Blueprint $table) {

            if (Schema::hasColumn('products', 'sale_type')) {
                DB::statement("UPDATE products SET sale_type = 'kg' WHERE sale_type IS NULL OR sale_type = ''");
            }

            if (Schema::hasColumn('products', 'pieces_per_box')) {
                DB::statement("UPDATE products SET pieces_per_box = 0 WHERE pieces_per_box IS NULL");
            }

            if (Schema::hasColumn('products', 'avg_box_weight')) {
                DB::statement("UPDATE products SET avg_box_weight = 0 WHERE avg_box_weight IS NULL");
            }

            if (Schema::hasColumn('products', 'tara')) {
                DB::statement("UPDATE products SET tara = 0 WHERE tara IS NULL");
            }

        });
    }

    public function down(): void
    {
        // rollback non necessario (evitiamo casino)
    }
};