<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Aggiungi nuove colonne
        Schema::table('products', function (Blueprint $table) {
            // cassa_kg | cassa_collo | kg_liberi | pezzo | peso_step
            $table->string('modalita_vendita', 20)->default('cassa_kg')->after('vat_rate');
            // step in grammi (solo per peso_step)
            $table->integer('step_grammi')->nullable()->after('modalita_vendita');
        });

        // 2. Migra dati esistenti: sale_type + ordine_step -> modalita_vendita
        DB::table('products')->where('ordine_step', 'colli')->update(['modalita_vendita' => 'cassa_kg']);
        DB::table('products')->where('ordine_step', 'mezzo_collo')->update(['modalita_vendita' => 'cassa_kg']);
        DB::table('products')->where('ordine_step', 'kg')->update(['modalita_vendita' => 'kg_liberi']);
        DB::table('products')->where('ordine_step', 'grammi')->update(['modalita_vendita' => 'peso_step', 'step_grammi' => 100]);
        DB::table('products')->where('ordine_step', 'pezzi_interi')->update(['modalita_vendita' => 'pezzo']);

        // Fallback per chi non ha ordine_step: usa sale_type
        DB::table('products')
            ->where(function ($q) {
                $q->whereNull('ordine_step')->orWhere('ordine_step', '');
            })
            ->where('sale_type', 'unit')
            ->update(['modalita_vendita' => 'pezzo']);

        DB::table('products')
            ->where(function ($q) {
                $q->whereNull('ordine_step')->orWhere('ordine_step', '');
            })
            ->where('sale_type', 'kg')
            ->update(['modalita_vendita' => 'cassa_kg']);

        // 3. Rimuovi vecchie colonne
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sale_type', 'ordine_step', 'unit']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sale_type', 10)->default('kg')->after('vat_rate');
            $table->string('ordine_step', 20)->default('colli')->after('disponibilita');
            $table->string('unit', 10)->default('kg')->after('origin');
        });

        DB::table('products')->where('modalita_vendita', 'cassa_kg')->update(['sale_type' => 'kg', 'ordine_step' => 'colli', 'unit' => 'kg']);
        DB::table('products')->where('modalita_vendita', 'cassa_collo')->update(['sale_type' => 'kg', 'ordine_step' => 'colli', 'unit' => 'kg']);
        DB::table('products')->where('modalita_vendita', 'kg_liberi')->update(['sale_type' => 'kg', 'ordine_step' => 'kg', 'unit' => 'kg']);
        DB::table('products')->where('modalita_vendita', 'pezzo')->update(['sale_type' => 'unit', 'ordine_step' => 'pezzi_interi', 'unit' => 'pz']);
        DB::table('products')->where('modalita_vendita', 'peso_step')->update(['sale_type' => 'kg', 'ordine_step' => 'grammi', 'unit' => 'kg']);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['modalita_vendita', 'step_grammi']);
        });
    }
};