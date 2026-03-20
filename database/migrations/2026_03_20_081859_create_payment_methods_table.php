<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('metodo', 30);
            $table->string('scadenza', 30);
            $table->integer('giorni_scadenza')->default(0);
            $table->boolean('fine_mese')->default(false);
            $table->decimal('spese_incasso', 6, 2)->default(0);
            $table->boolean('attivo')->default(true);
            $table->integer('ordine')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('payment_methods')->insert([
            ['nome' => 'Contanti alla consegna',  'metodo' => 'contanti',  'scadenza' => 'immediato', 'giorni_scadenza' => 0,   'fine_mese' => false, 'spese_incasso' => 0,    'attivo' => true, 'ordine' => 1,  'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Bonifico immediato',       'metodo' => 'bonifico',  'scadenza' => 'immediato', 'giorni_scadenza' => 0,   'fine_mese' => false, 'spese_incasso' => 0,    'attivo' => true, 'ordine' => 2,  'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Carta alla consegna',      'metodo' => 'carta',     'scadenza' => 'immediato', 'giorni_scadenza' => 0,   'fine_mese' => false, 'spese_incasso' => 0,    'attivo' => true, 'ordine' => 3,  'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Assegno alla consegna',    'metodo' => 'assegno',   'scadenza' => 'immediato', 'giorni_scadenza' => 0,   'fine_mese' => false, 'spese_incasso' => 0,    'attivo' => true, 'ordine' => 4,  'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Bonifico 7gg DF',          'metodo' => 'bonifico',  'scadenza' => '7gg_df',    'giorni_scadenza' => 7,   'fine_mese' => false, 'spese_incasso' => 0,    'attivo' => true, 'ordine' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Bonifico 15gg DF',         'metodo' => 'bonifico',  'scadenza' => '15gg_df',   'giorni_scadenza' => 15,  'fine_mese' => false, 'spese_incasso' => 0,    'attivo' => true, 'ordine' => 11, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Bonifico 30gg DF',         'metodo' => 'bonifico',  'scadenza' => '30gg_df',   'giorni_scadenza' => 30,  'fine_mese' => false, 'spese_incasso' => 0,    'attivo' => true, 'ordine' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Bonifico 30gg FM',         'metodo' => 'bonifico',  'scadenza' => '30gg_fm',   'giorni_scadenza' => 30,  'fine_mese' => true,  'spese_incasso' => 0,    'attivo' => true, 'ordine' => 21, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Ri.Ba. 30gg DF',           'metodo' => 'riba',      'scadenza' => '30gg_df',   'giorni_scadenza' => 30,  'fine_mese' => false, 'spese_incasso' => 3.00, 'attivo' => true, 'ordine' => 30, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Ri.Ba. 30gg FM',           'metodo' => 'riba',      'scadenza' => '30gg_fm',   'giorni_scadenza' => 30,  'fine_mese' => true,  'spese_incasso' => 3.00, 'attivo' => true, 'ordine' => 31, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Ri.Ba. 60gg DF',           'metodo' => 'riba',      'scadenza' => '60gg_df',   'giorni_scadenza' => 60,  'fine_mese' => false, 'spese_incasso' => 3.00, 'attivo' => true, 'ordine' => 32, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Ri.Ba. 60gg FM',           'metodo' => 'riba',      'scadenza' => '60gg_fm',   'giorni_scadenza' => 60,  'fine_mese' => true,  'spese_incasso' => 3.00, 'attivo' => true, 'ordine' => 33, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Bonifico 60gg DF',         'metodo' => 'bonifico',  'scadenza' => '60gg_df',   'giorni_scadenza' => 60,  'fine_mese' => false, 'spese_incasso' => 0,    'attivo' => true, 'ordine' => 40, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Bonifico 60gg FM',         'metodo' => 'bonifico',  'scadenza' => '60gg_fm',   'giorni_scadenza' => 60,  'fine_mese' => true,  'spese_incasso' => 0,    'attivo' => true, 'ordine' => 41, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Ri.Ba. 90gg DF',           'metodo' => 'riba',      'scadenza' => '90gg_df',   'giorni_scadenza' => 90,  'fine_mese' => false, 'spese_incasso' => 3.00, 'attivo' => true, 'ordine' => 50, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Ri.Ba. 90gg FM',           'metodo' => 'riba',      'scadenza' => '90gg_fm',   'giorni_scadenza' => 90,  'fine_mese' => true,  'spese_incasso' => 3.00, 'attivo' => true, 'ordine' => 51, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Bonifico 90gg FM',         'metodo' => 'bonifico',  'scadenza' => '90gg_fm',   'giorni_scadenza' => 90,  'fine_mese' => true,  'spese_incasso' => 0,    'attivo' => true, 'ordine' => 52, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Ri.Ba. 120gg FM',          'metodo' => 'riba',      'scadenza' => '120gg_fm',  'giorni_scadenza' => 120, 'fine_mese' => true,  'spese_incasso' => 3.00, 'attivo' => true, 'ordine' => 60, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'SDD addebito diretto',     'metodo' => 'sdd',       'scadenza' => '30gg_fm',   'giorni_scadenza' => 30,  'fine_mese' => true,  'spese_incasso' => 1.50, 'attivo' => true, 'ordine' => 70, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Compensazione',            'metodo' => 'compensazione', 'scadenza' => 'immediato', 'giorni_scadenza' => 0, 'fine_mese' => false, 'spese_incasso' => 0,  'attivo' => true, 'ordine' => 90, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};