<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            $table->decimal('colli',10,2)->nullable()->after('product_id');
            $table->decimal('peso_collo',10,2)->nullable()->after('colli');

            $table->decimal('kg_estimated',10,2)->nullable()->after('peso_collo');
            $table->decimal('kg_real',10,2)->nullable()->after('kg_estimated');

            $table->decimal('price_kg',10,2)->nullable()->after('kg_real');

        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            $table->dropColumn('colli');
            $table->dropColumn('peso_collo');
            $table->dropColumn('kg_estimated');
            $table->dropColumn('kg_real');
            $table->dropColumn('price_kg');

        });
    }
};