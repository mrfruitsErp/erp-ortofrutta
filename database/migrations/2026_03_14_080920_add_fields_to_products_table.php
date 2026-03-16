<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // origine prodotto (IT, ES, ZA ecc.)
            $table->string('origin')->nullable()->after('name');

            // tara cassa con decimali
            $table->decimal('tara', 6, 3)->default(0)->after('unit');

            // peso medio cassa
            $table->decimal('avg_box_weight', 6, 3)->default(0)->after('tara');

        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->dropColumn('origin');
            $table->dropColumn('tara');
            $table->dropColumn('avg_box_weight');

        });
    }
};