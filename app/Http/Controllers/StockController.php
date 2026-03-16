<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;

class StockController extends Controller
{

    public function index()
    {
        $products = Product::with('stock')->get();
        return view('stocks.index', compact('products'));
    }


    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('stocks.create', compact('products'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty'        => 'required|numeric|min:0.01',
            'note'       => 'nullable|string|max:255',
        ]);

        $stock = Stock::where('product_id', $request->product_id)->first();

        if ($stock) {
            $stock->quantity += $request->qty;
            $stock->save();
        } else {
            Stock::create([
                'product_id' => $request->product_id,
                'quantity'   => $request->qty,
            ]);
        }

        StockMovement::create([
            'product_id'    => $request->product_id,
            'document_id'   => null,
            'type'          => 'IN',
            'qty'           => $request->qty,
            'movement_date' => now()->toDateString(),
        ]);

        return redirect('/magazzino')
            ->with('success', 'Carico registrato con successo.');
    }

}