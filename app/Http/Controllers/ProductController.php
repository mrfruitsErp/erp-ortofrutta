<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();

        foreach ($products as $product) {
            $product->stock = Stock::where('product_id', $product->id)->first();
        }

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $product = Product::create([
            'name'           => $request->name,
            'origin'         => $request->origin,
            'unit'           => $request->unit ?? 'kg',
            'sale_type'      => $request->sale_type ?? 'kg',
            'avg_box_weight' => $request->avg_box_weight ?? 0,
            'tara'           => $request->tara ?? 0,
            'pieces_per_box' => $request->pieces_per_box ?? 0,
            'price'          => $request->price ?? 0,
            'cost_price'     => $request->cost_price ?? 0,
            'vat_rate'       => $request->vat_rate ?? 4,
        ]);

        // Stock iniziale
        $stock = Stock::firstOrCreate(
            ['product_id' => $product->id],
            ['quantity' => 0, 'min_stock' => 0]
        );

        if ($request->filled('new_stock_qty')) {
            $stock->quantity = $request->new_stock_qty;
        }
        if ($request->filled('min_stock')) {
            $stock->min_stock = $request->min_stock;
        }
        $stock->save();

        return redirect()->route('products.index')
            ->with('success', 'Prodotto ' . $product->name . ' creato.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $stock   = Stock::where('product_id', $id)->first();

        return view('products.edit', compact('product', 'stock'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'name'           => $request->name,
            'origin'         => $request->origin,
            'unit'           => $request->unit,
            'sale_type'      => $request->sale_type,
            'avg_box_weight' => $request->avg_box_weight,
            'tara'           => $request->tara ?? 0,
            'pieces_per_box' => $request->pieces_per_box,
            'price'          => $request->price,
            'cost_price'     => $request->cost_price,
            'vat_rate'       => $request->vat_rate ?? 4,
            'disponibilita'  => $request->disponibilita ?? 'disponibile',
            'ordine_step'    => $request->ordine_step ?? 'colli',
            'ordine_min'     => $request->ordine_min ?? 1,
        ]);

        if ($request->filled('new_stock_qty')) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $product->id],
                ['quantity' => 0]
            );
            $stock->quantity  = $request->new_stock_qty;
            $stock->min_stock = $request->min_stock ?? 0;
            $stock->save();
        } elseif ($request->filled('min_stock')) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $product->id],
                ['quantity' => 0]
            );
            $stock->min_stock = $request->min_stock;
            $stock->save();
        }

        return redirect()->back()->with('success', 'Prodotto aggiornato');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        Stock::where('product_id', $id)->delete();
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Prodotto eliminato.');
    }

    // -------------------------------------------------------
    // AGGIORNAMENTO MASSIVO + INLINE
    // -------------------------------------------------------
    public function massiveUpdate(Request $request)
    {
        $ids    = $request->ids ?? [];
        $action = $request->action;
        $value  = $request->value;

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Nessun prodotto selezionato']);
        }

        $products = Product::whereIn('id', $ids)->get();

        foreach ($products as $product) {
            switch ($action) {

                // Imposta stock diretto (editing inline)
                case 'stock_set':
                    $stock = Stock::firstOrCreate(
                        ['product_id' => $product->id],
                        ['quantity' => 0, 'min_stock' => 0]
                    );
                    $stock->quantity = round((float)$value, 3);
                    $stock->save();
                    break;

                // Imposta prezzo diretto (editing inline)
                case 'price_set':
                    $product->price = round((float)$value, 2);
                    $product->save();
                    break;

                // Modifica prezzo in percentuale
                case 'price_percent':
                    $product->price = round($product->price * (1 + $value / 100), 2);
                    $product->save();
                    break;

                // Modifica costo in percentuale
                case 'cost_percent':
                    $product->cost_price = round($product->cost_price * (1 + $value / 100), 2);
                    $product->save();
                    break;

                // Imposta scorta minima
                case 'min_stock':
                    $stock = Stock::firstOrCreate(
                        ['product_id' => $product->id],
                        ['quantity' => 0]
                    );
                    $stock->min_stock = (float)$value;
                    $stock->save();
                    break;
            }
        }

        return response()->json(['success' => true]);
    }
}