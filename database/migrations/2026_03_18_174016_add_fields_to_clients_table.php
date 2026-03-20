<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'referente')) {
                $table->string('referente')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('clients', 'cellulare_referente')) {
                $table->string('cellulare_referente')->nullable()->after('referente');
            }
            if (!Schema::hasColumn('clients', 'zona_consegna')) {
                $table->string('zona_consegna')->nullable()->after('cellulare_referente');
            }
            if (!Schema::hasColumn('clients', 'giorni_consegna')) {
                $table->json('giorni_consegna')->nullable()->after('zona_consegna');
            }
            if (!Schema::hasColumn('clients', 'giorni_chiusura')) {
                $table->json('giorni_chiusura')->nullable()->after('giorni_consegna');
            }
            if (!Schema::hasColumn('clients', 'fascia_oraria_inizio')) {
                $table->string('fascia_oraria_inizio', 5)->nullable()->after('giorni_chiusura');
            }
            if (!Schema::hasColumn('clients', 'fascia_oraria_fine')) {
                $table->string('fascia_oraria_fine', 5)->nullable()->after('fascia_oraria_inizio');
            }
            if (!Schema::hasColumn('clients', 'fido')) {
                $table->decimal('fido', 10, 2)->default(0)->after('fascia_oraria_fine');
            }
            if (!Schema::hasColumn('clients', 'note_interne')) {
                $table->text('note_interne')->nullable()->after('fido');
            }
            if (!Schema::hasColumn('clients', 'stato')) {
                $table->enum('stato', ['attivo', 'sospeso', 'potenziale'])->default('attivo')->after('note_interne');
            }
        });
    }
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'referente', 'cellulare_referente', 'zona_consegna',
                'giorni_consegna', 'giorni_chiusura',
                'fascia_oraria_inizio', 'fascia_oraria_fine',
                'fido', 'note_interne', 'stato',
            ]);
        });
    }
};