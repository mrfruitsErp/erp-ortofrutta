<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;

class ProductController extends Controller
{
    // -------------------------------------------------------
    private function generateSku(string $category): string
    {
        $prefixes = [
            'Frutta'            => 'FR',
            'Verdura'           => 'VE',
            'Erbe Aromatiche'   => 'ER',
            'Funghi'            => 'FU',
            'Frutta Secca'      => 'FS',
            'Legumi Secchi'     => 'LS',
            'Insalata 4a Gamma' => 'IV',
        ];

        $prefix = $prefixes[$category] ?? 'PR';

        $last = Product::where('sku', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(sku, 3) AS UNSIGNED) DESC')
            ->first();

        $nextNum = 1;
        if ($last && preg_match('/\d+$/', $last->sku, $match)) {
            $nextNum = (int) $match[0] + 1;
        }

        return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    // -------------------------------------------------------
    public function index()
    {
        $products = Product::orderBy('name')->get();

        foreach ($products as $product) {
            $product->stock = Stock::where('product_id', $product->id)->first();
        }

        return view('products.index', compact('products'));
    }

    // -------------------------------------------------------
    public function create()
    {
        return view('products.create');
    }

    // -------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'category'          => 'required|string|max:255',
            'modalita_vendita'  => 'required|in:cassa_kg,cassa_collo,kg_liberi,pezzo,peso_step',
        ]);

        $modalita = $request->modalita_vendita;

        $product = Product::create([
            'name'              => $request->name,
            'origin'            => $request->origin,
            'modalita_vendita'  => $modalita,
            'step_grammi'       => $modalita === 'peso_step' ? ($request->step_grammi ?? 100) : null,
            'avg_box_weight'    => $request->avg_box_weight ?? 0,
            'tara'              => $request->tara ?? 0,
            'pieces_per_box'    => $request->pieces_per_box ?? 0,
            'price'             => $request->price ?? 0,
            'cost_price'        => $request->cost_price ?? 0,
            'vat_rate'          => $request->vat_rate ?? 4,
            'category'          => $request->category,
            'disponibilita'     => $request->disponibilita ?? 'disponibile',
            'ordine_min'        => $request->ordine_min ?? 1,
            'sku'               => $this->generateSku($request->category),
        ]);

        Stock::create([
            'product_id' => $product->id,
            'quantity'   => $request->new_stock_qty ?? 0,
            'min_stock'  => $request->min_stock ?? 0,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Prodotto creato con successo');
    }

    // -------------------------------------------------------
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $stock   = Stock::where('product_id', $id)->first();

        return view('products.edit', compact('product', 'stock'));
    }

    // -------------------------------------------------------
    public function update(Request $request, $id)
    {
        $product  = Product::findOrFail($id);
        $modalita = $request->modalita_vendita ?? $product->modalita_vendita;

        $product->update([
            'name'              => $request->name,
            'origin'            => $request->origin,
            'modalita_vendita'  => $modalita,
            'step_grammi'       => $modalita === 'peso_step' ? ($request->step_grammi ?? 100) : null,
            'avg_box_weight'    => $request->avg_box_weight ?? 0,
            'tara'              => $request->tara ?? 0,
            'pieces_per_box'    => $request->pieces_per_box ?? 0,
            'price'             => $request->price ?? 0,
            'cost_price'        => $request->cost_price ?? 0,
            'vat_rate'          => $request->vat_rate ?? 4,
            'disponibilita'     => $request->disponibilita ?? 'disponibile',
            'ordine_min'        => $request->ordine_min ?? 1,
            'category'          => $request->category,
        ]);

        // Aggiorna stock se specificato
        if ($request->filled('new_stock_qty')) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $product->id],
                ['quantity' => 0, 'min_stock' => 0]
            );
            $stock->quantity = $request->new_stock_qty;
            $stock->save();
        }

        if ($request->filled('min_stock')) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $product->id],
                ['quantity' => 0, 'min_stock' => 0]
            );
            $stock->min_stock = $request->min_stock;
            $stock->save();
        }

        return redirect()->back()->with('success', 'Prodotto aggiornato');
    }

    // -------------------------------------------------------
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        Stock::where('product_id', $id)->delete();
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Prodotto eliminato.');
    }

    // -------------------------------------------------------
    public function massiveUpdate(Request $request)
    {
        $ids    = $request->input('ids', []);
        $action = $request->input('action');
        $value  = $request->input('value');

        if (empty($ids)) {
            return response()->json(['success' => false]);
        }

        try {
            foreach ($ids as $id) {
                $product = Product::find($id);
                if (!$product) continue;

                switch ($action) {
                    case 'disp_set':
                        if (in_array($value, ['disponibile', 'su_richiesta', 'non_disponibile'])) {
                            $product->disponibilita = $value;
                            $product->save();
                        }
                        break;

                    case 'price_set':
                        $product->price = $value;
                        $product->save();
                        break;

                    case 'cost_percent':
                        $product->cost_price *= (1 + ($value / 100));
                        $product->save();
                        break;

                    case 'price_percent':
                        $product->price *= (1 + ($value / 100));
                        $product->save();
                        break;

                    case 'stock_set':
                        $stock = Stock::firstOrCreate(
                            ['product_id' => $product->id],
                            ['quantity' => 0]
                        );
                        $stock->quantity = $value;
                        $stock->save();
                        break;

                    case 'min_stock':
                        $stock = Stock::firstOrCreate(
                            ['product_id' => $product->id],
                            ['quantity' => 0]
                        );
                        $stock->min_stock = $value;
                        $stock->save();
                        break;
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}