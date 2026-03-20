<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Existing columns from DB:
            // id, company_name, vat_number, fiscal_code, address, city, zip, province,
            // email, phone, payment_terms, created_at, updated_at, order_token,
            // referente, cellulare_referente, zona_consegna, giorni_consegna,
            // giorni_chiusura, fascia_oraria_inizio, fascia_oraria_fine, fido,
            // note_interne, stato, modalita_ordine

            if (!Schema::hasColumn('clients', 'price_list_id')) {
                $table->unsignedBigInteger('price_list_id')->nullable()->after('id');
                $table->foreign('price_list_id')->references('id')->on('price_lists')->nullOnDelete();
            }

            if (!Schema::hasColumn('clients', 'payment_method_id')) {
                $table->unsignedBigInteger('payment_method_id')->nullable()->after('price_list_id');
                $table->foreign('payment_method_id')->references('id')->on('payment_methods')->nullOnDelete();
            }

            if (!Schema::hasColumn('clients', 'puo_ordinare_kg')) {
                $table->boolean('puo_ordinare_kg')->nullable()->after('modalita_ordine');
            }

            if (!Schema::hasColumn('clients', 'orario_limite_ordine')) {
                $table->string('orario_limite_ordine', 5)->nullable()->after('puo_ordinare_kg');
            }

            if (!Schema::hasColumn('clients', 'iban')) {
                $table->string('iban', 34)->nullable()->after('fido');
            }

            if (!Schema::hasColumn('clients', 'banca')) {
                $table->string('banca', 100)->nullable()->after('iban');
            }
        });

        // Settings globali
        if (Schema::hasTable('settings')) {
            if (!DB::table('settings')->where('key', 'tempo_preparazione_minuti')->exists()) {
                DB::table('settings')->insert([
                    'key' => 'tempo_preparazione_minuti',
                    'value' => '120',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if (!DB::table('settings')->where('key', 'orario_limite_ordine_default')->exists()) {
                DB::table('settings')->insert([
                    'key' => 'orario_limite_ordine_default',
                    'value' => '21:00',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'banca'))                $table->dropColumn('banca');
            if (Schema::hasColumn('clients', 'iban'))                 $table->dropColumn('iban');
            if (Schema::hasColumn('clients', 'orario_limite_ordine')) $table->dropColumn('orario_limite_ordine');
            if (Schema::hasColumn('clients', 'puo_ordinare_kg'))      $table->dropColumn('puo_ordinare_kg');
            if (Schema::hasColumn('clients', 'payment_method_id')) {
                $table->dropForeign(['payment_method_id']);
                $table->dropColumn('payment_method_id');
            }
            if (Schema::hasColumn('clients', 'price_list_id')) {
                $table->dropForeign(['price_list_id']);
                $table->dropColumn('price_list_id');
            }
        });

        DB::table('settings')->whereIn('key', ['tempo_preparazione_minuti', 'orario_limite_ordine_default'])->delete();
    }
};