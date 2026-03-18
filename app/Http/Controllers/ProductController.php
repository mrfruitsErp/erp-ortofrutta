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
        ]);

        // Aggiorna stock solo se il campo è stato compilato
        if ($request->filled('new_stock_qty')) {

            $stock = Stock::firstOrCreate(
                ['product_id' => $product->id],
                ['quantity' => 0]
            );

            $stock->quantity  = $request->new_stock_qty;
            $stock->min_stock = $request->min_stock ?? 0;
            $stock->save();

        } elseif ($request->filled('min_stock')) {

            // Aggiorna solo scorta minima se cambiata
            $stock = Stock::firstOrCreate(
                ['product_id' => $product->id],
                ['quantity' => 0]
            );
            $stock->min_stock = $request->min_stock;
            $stock->save();
        }

        return redirect()->back()->with('success', 'Prodotto aggiornato');
    }

}