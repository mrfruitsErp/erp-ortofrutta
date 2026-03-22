<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =========================
        // UTENTE ADMIN (ANTI-DUPLICATI)
        // =========================
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@erp.com'],
            [
                'name' => 'Admin ERP',
                'password' => bcrypt('password'),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        // =========================
        // PRODOTTI DEMO (ANTI-DUPLICATI)
        // =========================
        Product::updateOrCreate(
            ['name' => 'Aglio'],
            [
                'price' => 4.00,
                'cost' => 1.60,
                'stock' => 100,
                'unit' => 'kg'
            ]
        );

        Product::updateOrCreate(
            ['name' => 'Albicocche'],
            [
                'price' => 2.80,
                'cost' => 1.10,
                'stock' => 80,
                'unit' => 'kg'
            ]
        );

        Product::updateOrCreate(
            ['name' => 'Anguria'],
            [
                'price' => 0.90,
                'cost' => 0.30,
                'stock' => 50,
                'unit' => 'kg'
            ]
        );
    }
}