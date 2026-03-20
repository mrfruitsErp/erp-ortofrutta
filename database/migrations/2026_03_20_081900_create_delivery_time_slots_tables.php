<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50);
            $table->string('orario_inizio', 5);
            $table->string('orario_fine', 5);
            $table->boolean('attivo')->default(true);
            $table->integer('ordine')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('delivery_time_slots')->insert([
            ['nome' => 'Mattina',    'orario_inizio' => '06:00', 'orario_fine' => '10:00', 'attivo' => true, 'ordine' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Pranzo',     'orario_inizio' => '10:00', 'orario_fine' => '13:00', 'attivo' => true, 'ordine' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['nome' => 'Pomeriggio', 'orario_inizio' => '14:00', 'orario_fine' => '18:00', 'attivo' => true, 'ordine' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::create('client_delivery_prefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('delivery_time_slot_id')->constrained('delivery_time_slots')->cascadeOnDelete();
            $table->boolean('preferito')->default(false);
            $table->timestamps();
            $table->unique(['client_id', 'delivery_time_slot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_delivery_prefs');
        Schema::dropIfExists('delivery_time_slots');
    }
};