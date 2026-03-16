<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;

class ProductController extends Controller
{

public function index()
{
$products = Product::with('stock')->get();
return view('products.index', compact('products'));
}

public function create()
{
return view('products.create');
}

public function store(Request $request)
{

$product = new Product();

$product->name           = $request->name;
$product->origin         = $request->origin;
$product->unit           = $request->unit;
$product->tara           = $request->tara;
$product->avg_box_weight = $request->avg_box_weight;
$product->price          = $request->price;
$product->cost_price     = $request->cost_price;
$product->vat_rate       = $request->vat_rate ?? 4;

$product->save();

return redirect('/products');

}

public function edit($id)
{

$product = Product::findOrFail($id);
$stock   = Stock::where('product_id', $id)->first();

return view('products.edit', compact('product','stock'));

}

public function update(Request $request, $id)
{

$product = Product::findOrFail($id);

$product->name           = $request->name;
$product->origin         = $request->origin;
$product->unit           = $request->unit;
$product->tara           = $request->tara;
$product->avg_box_weight = $request->avg_box_weight;
$product->price          = $request->price;
$product->cost_price     = $request->cost_price;
$product->vat_rate       = $request->vat_rate ?? 4;

$product->save();

return redirect('/products')->with('success','Prodotto aggiornato');

}

}