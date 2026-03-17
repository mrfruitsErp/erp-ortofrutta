<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aggiunge vat_total a documents se non esiste
        if (!Schema::hasColumn('documents', 'vat_total')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->decimal('vat_total', 10, 2)->default(0)->after('total');
            });
        }

        // Aggiunge vat_rate a document_rows se non esiste
        if (!Schema::hasColumn('document_rows', 'vat_rate')) {
            Schema::table('document_rows', function (Blueprint $table) {
                $table->decimal('vat_rate', 5, 2)->default(4)->after('price_per_kg');
            });
        }

        // Cambia number in orders da integer a string per supportare ORD-2026-0001
        if (Schema::hasColumn('orders', 'number')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('number', 20)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('documents', 'vat_total')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('vat_total');
            });
        }

        if (Schema::hasColumn('document_rows', 'vat_rate')) {
            Schema::table('document_rows', function (Blueprint $table) {
                $table->dropColumn('vat_rate');
            });
        }
    }
};