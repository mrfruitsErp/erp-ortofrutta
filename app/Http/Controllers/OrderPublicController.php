<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\DeliveryZone;

class OrderPublicController extends Controller
{

public function index($token)
{

$client = Client::where('order_token',$token)->firstOrFail();

/*
|--------------------------------------------------------------------------
| CHIUSURA ORDINI
|--------------------------------------------------------------------------
*/

$cutoff = Setting::get('order_cutoff_time');

if($cutoff && now()->format('H:i') >= $cutoff){
return view('orders.closed');
}

/*
|--------------------------------------------------------------------------
| ZONA CONSEGNA
|--------------------------------------------------------------------------
*/

$zone = DeliveryZone::findByCap($client->zip);

$slots = [];

if($zone){
$slots = $zone->slots()->where('active',1)->get();
}

/*
|--------------------------------------------------------------------------
| PRODOTTI
|--------------------------------------------------------------------------
*/

$products = Product::orderBy('category')
->orderBy('name')
->get();

return view('orders.public',compact('client','products','slots'));

}

public function store(Request $request,$token)
{

$client = Client::where('order_token',$token)->firstOrFail();

/*
|--------------------------------------------------------------------------
| DATA CONSEGNA
|--------------------------------------------------------------------------
*/

$delivery_date = $request->delivery_date ?? date('Y-m-d',strtotime('+1 day'));

$delivery_slot = $request->delivery_slot ?? null;

/*
|--------------------------------------------------------------------------
| CREAZIONE ORDINE
|--------------------------------------------------------------------------
*/

$order = Order::create([
'client_id'=>$client->id,
'number'=>'WEB-'.time(),
'date'=>date('Y-m-d'),
'delivery_date'=>$delivery_date,
'delivery_slot'=>$delivery_slot,
'total'=>0,
'status'=>'web'
]);

$total = 0;

/*
|--------------------------------------------------------------------------
| RIGHE ORDINE
|--------------------------------------------------------------------------
*/

foreach($request->qty as $product_id=>$qty){

if($qty <= 0) continue;

$product = Product::find($product_id);

$line_total = $qty * $product->price;

OrderItem::create([
'order_id'=>$order->id,
'product_id'=>$product_id,
'qty'=>$qty,
'price'=>$product->price,
'total'=>$line_total
]);

$total += $line_total;

}

/*
|--------------------------------------------------------------------------
| TOTALE ORDINE
|--------------------------------------------------------------------------
*/

$order->update([
'total'=>$total
]);

return redirect()->back()->with('success','Ordine inviato');

}

}
