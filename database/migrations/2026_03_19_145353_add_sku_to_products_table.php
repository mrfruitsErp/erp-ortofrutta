<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku', 10)->nullable()->unique()->after('name');
            }
            $table->enum('category', [
                'Frutta',
                'Verdura',
                'Erbe Aromatiche',
                'Funghi',
                'Frutta Secca',
                'Legumi Secchi',
                'Insalata 4a Gamma',
            ])->nullable()->change();
        });
    }
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sku');
        });
    }
};