<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            if (!Schema::hasColumn('order_items', 'origin')) {
                $table->string('origin')->nullable()->after('product_id');
            }

            if (!Schema::hasColumn('order_items', 'tara')) {
                $table->decimal('tara', 10, 3)->nullable()->after('kg_real');
            }

            if (!Schema::hasColumn('order_items', 'kg_net')) {
                $table->decimal('kg_net', 10, 2)->nullable()->after('tara');
            }

        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            if (Schema::hasColumn('order_items', 'origin')) {
                $table->dropColumn('origin');
            }

            if (Schema::hasColumn('order_items', 'tara')) {
                $table->dropColumn('tara');
            }

            if (Schema::hasColumn('order_items', 'kg_net')) {
                $table->dropColumn('kg_net');
            }

        });
    }
};