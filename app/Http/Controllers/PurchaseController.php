<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;

class PurchaseController extends Controller
{

    public function index()
    {

        $purchases = Purchase::with(['supplier','product'])
            ->orderBy('date','desc')
            ->get();

        return view('purchases.index', compact('purchases'));

    }


    public function create()
    {

        $suppliers = Supplier::orderBy('company_name')->get();
        $products  = Product::orderBy('name')->get();

        return view('purchases.create', compact(
            'suppliers',
            'products'
        ));

    }


    public function store(Request $request)
    {

        $total = $request->kg * $request->price;

        Purchase::create([
            'supplier_id' => $request->supplier_id,
            'product_id'  => $request->product_id,
            'kg'          => $request->kg,
            'price'       => $request->price,
            'total'       => $total,
            'date'        => $request->date
        ]);

        return redirect('/purchases');

    }

}