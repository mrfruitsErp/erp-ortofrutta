<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Stock;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // VERDURE
            ['name' => 'Zucchine Verdi',        'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 8.000, 'price' => 1.80, 'cost_price' => 0.80],
            ['name' => 'Zucchine Romanesche',    'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 8.000, 'price' => 2.00, 'cost_price' => 0.90],
            ['name' => 'Melanzane',              'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 9.000, 'price' => 1.60, 'cost_price' => 0.70],
            ['name' => 'Peperoni Rossi',         'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 8.000, 'price' => 2.50, 'cost_price' => 1.10],
            ['name' => 'Peperoni Gialli',        'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 8.000, 'price' => 2.50, 'cost_price' => 1.10],
            ['name' => 'Peperoni Verdi',         'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 8.000, 'price' => 1.80, 'cost_price' => 0.80],
            ['name' => 'Pomodori Tondi',         'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 9.000, 'price' => 1.50, 'cost_price' => 0.65],
            ['name' => 'Pomodori Costoluti',     'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 8.000, 'price' => 2.20, 'cost_price' => 1.00],
            ['name' => 'Pomodori Ciliegino',     'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.300, 'avg_box_weight' => 5.000, 'price' => 2.80, 'cost_price' => 1.30],
            ['name' => 'Pomodori Pachino',       'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.300, 'avg_box_weight' => 5.000, 'price' => 3.00, 'cost_price' => 1.40],
            ['name' => 'Insalata Iceberg',       'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.200, 'avg_box_weight' => 7.000, 'price' => 1.40, 'cost_price' => 0.60],
            ['name' => 'Insalata Romana',        'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.200, 'avg_box_weight' => 6.000, 'price' => 1.50, 'cost_price' => 0.65],
            ['name' => 'Radicchio Rosso',        'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.200, 'avg_box_weight' => 5.000, 'price' => 2.00, 'cost_price' => 0.90],
            ['name' => 'Spinaci',                'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.300, 'avg_box_weight' => 5.000, 'price' => 2.50, 'cost_price' => 1.10],
            ['name' => 'Broccoli',               'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 8.000, 'price' => 1.80, 'cost_price' => 0.80],
            ['name' => 'Cavolfiore',             'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.600, 'avg_box_weight' => 9.000, 'price' => 1.60, 'cost_price' => 0.70],
            ['name' => 'Cavolo Cappuccio',       'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 10.000,'price' => 1.20, 'cost_price' => 0.50],
            ['name' => 'Finocchi',               'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 8.000, 'price' => 1.50, 'cost_price' => 0.65],
            ['name' => 'Sedano',                 'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 7.000, 'price' => 1.40, 'cost_price' => 0.60],
            ['name' => 'Carote',                 'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 9.000, 'price' => 1.00, 'cost_price' => 0.40],
            ['name' => 'Patate Bianche',         'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 10.000,'price' => 0.90, 'cost_price' => 0.35],
            ['name' => 'Patate Rosse',           'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 10.000,'price' => 1.00, 'cost_price' => 0.40],
            ['name' => 'Cipolle Bianche',        'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 9.000, 'price' => 1.20, 'cost_price' => 0.50],
            ['name' => 'Cipolle Rosse',          'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 9.000, 'price' => 1.40, 'cost_price' => 0.60],
            ['name' => 'Aglio',                  'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.200, 'avg_box_weight' => 5.000, 'price' => 3.50, 'cost_price' => 1.60],
            ['name' => 'Porri',                  'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.300, 'avg_box_weight' => 7.000, 'price' => 1.60, 'cost_price' => 0.70],
            ['name' => 'Asparagi Verdi',         'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.300, 'avg_box_weight' => 5.000, 'price' => 4.50, 'cost_price' => 2.00],
            ['name' => 'Piselli',                'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.300, 'avg_box_weight' => 6.000, 'price' => 2.80, 'cost_price' => 1.20],
            ['name' => 'Fagiolini',              'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.300, 'avg_box_weight' => 5.000, 'price' => 2.50, 'cost_price' => 1.10],
            ['name' => 'Cetrioli',               'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 8.000, 'price' => 1.40, 'cost_price' => 0.60],
            ['name' => 'Carciofi',               'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 8.000, 'price' => 2.80, 'cost_price' => 1.20],
            ['name' => 'Funghi Champignon',      'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.300, 'avg_box_weight' => 5.000, 'price' => 3.00, 'cost_price' => 1.40],
            ['name' => 'Prezzemolo',             'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.100, 'avg_box_weight' => 2.000, 'price' => 4.00, 'cost_price' => 1.80],
            ['name' => 'Basilico',               'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.100, 'avg_box_weight' => 2.000, 'price' => 5.00, 'cost_price' => 2.20],

            // FRUTTA
            ['name' => 'Mele Golden',            'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 9.000, 'price' => 1.50, 'cost_price' => 0.65],
            ['name' => 'Mele Fuji',              'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 9.000, 'price' => 1.60, 'cost_price' => 0.70],
            ['name' => 'Pere Abate',             'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 9.000, 'price' => 1.80, 'cost_price' => 0.80],
            ['name' => 'Arance Navel',           'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.600, 'avg_box_weight' => 10.000,'price' => 1.20, 'cost_price' => 0.50],
            ['name' => 'Clementine',             'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 8.000, 'price' => 1.50, 'cost_price' => 0.65],
            ['name' => 'Limoni',                 'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 8.000, 'price' => 1.40, 'cost_price' => 0.60],
            ['name' => 'Banane',                 'origin' => 'EC', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 9.000, 'price' => 1.30, 'cost_price' => 0.55],
            ['name' => 'Kiwi',                   'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 8.000, 'price' => 2.00, 'cost_price' => 0.90],
            ['name' => 'Fragole',                'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.200, 'avg_box_weight' => 4.000, 'price' => 3.50, 'cost_price' => 1.60],
            ['name' => 'Uva Bianca',             'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 7.000, 'price' => 2.50, 'cost_price' => 1.10],
            ['name' => 'Uva Nera',               'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 7.000, 'price' => 2.50, 'cost_price' => 1.10],
            ['name' => 'Melone Giallo',          'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.600, 'avg_box_weight' => 9.000, 'price' => 1.60, 'cost_price' => 0.70],
            ['name' => 'Anguria',                'origin' => 'IT', 'unit' => 'kg', 'tara' => 1.000, 'avg_box_weight' => 15.000,'price' => 0.80, 'cost_price' => 0.30],
            ['name' => 'Pesche',                 'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 8.000, 'price' => 2.00, 'cost_price' => 0.90],
            ['name' => 'Nettarine',              'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.500, 'avg_box_weight' => 8.000, 'price' => 2.00, 'cost_price' => 0.90],
            ['name' => 'Albicocche',             'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 7.000, 'price' => 2.50, 'cost_price' => 1.10],
            ['name' => 'Ciliegie',               'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.300, 'avg_box_weight' => 5.000, 'price' => 5.00, 'cost_price' => 2.20],
            ['name' => 'Susine',                 'origin' => 'IT', 'unit' => 'kg', 'tara' => 0.400, 'avg_box_weight' => 7.000, 'price' => 2.20, 'cost_price' => 1.00],
        ];

        foreach ($products as $data) {
            // Evita duplicati per nome
            $existing = Product::where('name', $data['name'])->first();
            if ($existing) continue;

            $product = Product::create([
                'name'           => $data['name'],
                'origin'         => $data['origin'],
                'unit'           => $data['unit'],
                'tara'           => $data['tara'],
                'avg_box_weight' => $data['avg_box_weight'],
                'price'          => $data['price'],
                'cost_price'     => $data['cost_price'],
            ]);

            // Crea stock a zero
            Stock::create([
                'product_id' => $product->id,
                'quantity'   => 0,
                'min_stock'  => 0,
            ]);
        }

        $this->command->info('✅ ' . count($products) . ' prodotti ortofrutticoli caricati!');
    }
}