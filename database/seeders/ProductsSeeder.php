use App\Models\Product;

public function run(): void
{
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
}