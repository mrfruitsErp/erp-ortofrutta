<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('codice', 20)->unique();
            $table->text('descrizione')->nullable();
            $table->decimal('sconto_default_pct', 5, 2)->default(0);
            $table->boolean('puo_ordinare_kg')->default(false);
            $table->decimal('ordine_min_importo', 10, 2)->default(0);
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->boolean('attivo')->default(true);
            $table->integer('ordine')->default(0);
            $table->timestamps();

            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->nullOnDelete();
        });

        $now = now();
        DB::table('price_lists')->insert([
            ['nome' => 'HoReCa',    'codice' => 'horeca',    'descrizione' => 'Ristoranti, bar, hotel, catering. Possono ordinare anche a kg.', 'sconto_default_pct' => 0, 'puo_ordinare_kg' => true,  'ordine_min_importo' => 0,   'payment_method_id' => null, 'attivo' => true, 'ordine' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Dettaglio', 'codice' => 'dettaglio', 'descrizione' => 'Fruttivendoli, negozi alimentari, ambulanti. Ordini a casse intere.',            'sconto_default_pct' => 0, 'puo_ordinare_kg' => false, 'ordine_min_importo' => 0,   'payment_method_id' => null, 'attivo' => true, 'ordine' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'GDO',       'codice' => 'gdo',       'descrizione' => 'Supermercati, catene, grande distribuzione. Volumi alti, pagamenti dilazionati.', 'sconto_default_pct' => 0, 'puo_ordinare_kg' => false, 'ordine_min_importo' => 100, 'payment_method_id' => null, 'attivo' => true, 'ordine' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::create('price_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_list_id')->constrained('price_lists')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('prezzo_override', 10, 2)->nullable();
            $table->decimal('sconto_pct', 5, 2)->nullable();
            $table->decimal('min_qty', 10, 3)->nullable();
            $table->decimal('max_qty', 10, 3)->nullable();
            $table->decimal('min_qty_kg', 10, 3)->nullable();
            $table->boolean('bloccato')->default(false);
            $table->timestamps();
            $table->unique(['price_list_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_list_items');
        Schema::dropIfExists('price_lists');
    }
};