<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('disponibilita', ['disponibile', 'su_richiesta', 'non_disponibile'])
                  ->default('disponibile')
                  ->after('sale_type');
            $table->enum('ordine_step', ['colli', 'mezzo_collo', 'kg', 'grammi'])
                  ->default('colli')
                  ->after('disponibilita');
            $table->decimal('ordine_min', 8, 3)
                  ->default(1)
                  ->after('ordine_step');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->enum('modalita_ordine', ['colli', 'mezzo_collo', 'kg'])
                  ->default('colli')
                  ->after('zona_consegna');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['disponibilita', 'ordine_step', 'ordine_min']);
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('modalita_ordine');
        });
    }
};