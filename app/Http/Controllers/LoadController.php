<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockMovement;

class LoadController extends Controller
{

    public function create()
    {

        $products = Product::all();

        return view('loads.create', compact('products'));

    }


    public function store(Request $request)
    {

        $product = Product::find($request->product_id);

        if($product){

            $qty = $request->qty;
            $new_cost = $request->cost_price;

            $old_stock = $product->stock;
            $old_cost = $product->cost_price;

            $new_stock = $old_stock + $qty;


            if($old_stock > 0){

                $average_cost =
                (($old_stock * $old_cost) + ($qty * $new_cost))
                / $new_stock;

            } else {

                $average_cost = $new_cost;

            }


            $product->stock = $new_stock;
            $product->cost_price = $average_cost;

            $product->save();


            $movement = new StockMovement();

            $movement->product_id = $product->id;
            $movement->type = "LOAD";
            $movement->qty = $qty;
            $movement->movement_date = now();

            $movement->save();

        }

        return redirect('/magazzino');

    }

}