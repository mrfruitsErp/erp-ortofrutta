<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Client;
use App\Models\Product;

class OrderController extends Controller
{

public function index()
{

$orders = Order::with('client')
->orderBy('id','desc')
->get();

return view('orders.index',compact('orders'));

}

public function create()
{

$clients = Client::all();
$products = Product::all();

return view('orders.create',compact('clients','products'));

}

public function store(Request $request)
{

$year = date('Y');

$last = Order::orderBy('id','desc')->first();

if($last){
$last_number = intval(substr($last->number,-4));
$new_number = $last_number + 1;
}else{
$new_number = 1;
}

$formatted = str_pad($new_number,4,'0',STR_PAD_LEFT);

$order = new Order();

$order->number = "ORD-$year-$formatted";
$order->client_id = $request->client_id;
$order->date = $request->date;
$order->total = 0;
$order->status = "open";

$order->save();

$total_order = 0;

for($i=0;$i<count($request->product_id);$i++){

$product_id = $request->product_id[$i];
$qty = $request->qty[$i];
$price = $request->price[$i];

if(!$product_id) continue;

$total = $qty * $price;

$item = new OrderItem();

$item->order_id = $order->id;
$item->product_id = $product_id;
$item->qty = $qty;
$item->price = $price;
$item->total = $total;

$item->save();

$total_order += $total;

}

$order->total = $total_order;
$order->save();

return redirect('/orders')->with('success','Ordine creato con successo');

}

public function show($id)
{

$order = Order::with(['client','items.product'])
->findOrFail($id);

return view('orders.show',compact('order'));

}

}
