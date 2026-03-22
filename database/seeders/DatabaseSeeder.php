<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // UTENTE
  User::firstOrCreate(
    ['email' => 'admin@erp.com'],
    [
        'name' => 'Admin ERP',
        'password' => bcrypt('password'),
    ]
);

        // PRODOTTI
        Product::create([
            'name' => 'Aglio',
            'sku' => 'VE001',
            'category' => 'Verdura',
            'origin' => 'IT',
            'price' => 4.00,
            'cost' => 1.60
        ]);

        Product::create([
            'name' => 'Albicocche',
            'sku' => 'FR001',
            'category' => 'Frutta',
            'origin' => 'IT',
            'price' => 2.80,
            'cost' => 1.10
        ]);

        Product::create([
            'name' => 'Anguria',
            'sku' => 'FR002',
            'category' => 'Frutta',
            'origin' => 'IT',
            'price' => 0.90,
            'cost' => 0.30
        ]);
    }
}