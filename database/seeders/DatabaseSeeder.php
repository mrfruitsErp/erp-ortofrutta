<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =========================
        // UTENTE TEST
        // =========================
        User::factory()->create([
            'name' => 'Admin ERP',
            'email' => 'admin@erp.com',
        ]);

        // =========================
        // PRODOTTI DEMO
        // =========================
        Product::create([
            'name' => 'Aglio',
            'sku' => 'VE001',
            'category' => 'Verdura',
            'origin' => 'IT',
            'unit' => 'kg',
            'price' => 4.00,
            'cost' => 1.60
        ]);

        Product::create([
            'name' => 'Albicocche',
            'sku' => 'FR001',
            'category' => 'Frutta',
            'origin' => 'IT',
            'unit' => 'kg',
            'price' => 2.80,
            'cost' => 1.10
        ]);

        Product::create([
            'name' => 'Anguria',
            'sku' => 'FR002',
            'category' => 'Frutta',
            'origin' => 'IT',
            'unit' => 'kg',
            'price' => 0.90,
            'cost' => 0.30
        ]);
    }
}