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
        $client = Client::where('order_token', $token)->firstOrFail();

        // Chiusura ordini
        $cutoff = Setting::get('order_cutoff_time');
        if ($cutoff && now()->format('H:i') >= $cutoff) {
            return view('orders.closed');
        }

        // Slot consegna
        $zone  = DeliveryZone::findByCap($client->zip ?? '');
        $slots = $zone ? $zone->slots()->where('active', 1)->get() : collect();

        // Prodotti
        $products = Product::orderBy('category')->orderBy('name')->get();

        return view('orders.public', compact('client', 'products', 'slots'));
    }

    public function showOrder($token, $id)
    {
        $client = Client::where('order_token', $token)->firstOrFail();
        $order  = \App\Models\Order::with('items.product')
            ->where('id', $id)
            ->where('client_id', $client->id)
            ->firstOrFail();

        return view('orders.public_show', compact('client', 'order'));
    }

    public function store(Request $request, $token)
    {
        $client = Client::where('order_token', $token)->firstOrFail();

        $delivery_date = $request->delivery_date ?? date('Y-m-d', strtotime('+1 day'));
        $delivery_slot = $request->delivery_slot ?? null;

        // Numero ordine leggibile
        $year      = date('Y');
        $lastOrder = Order::whereYear('date', $year)
            ->where('number', 'like', 'WEB-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder && preg_match('/(\d+)$/', $lastOrder->number, $match)) {
            $nextNum = (int)$match[1] + 1;
        } else {
            $nextNum = 1;
        }

        $number = 'WEB-' . $year . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $order = Order::create([
            'client_id'     => $client->id,
            'number'        => $number,
            'date'          => date('Y-m-d'),
            'delivery_date' => $delivery_date,
            'delivery_slot' => $delivery_slot,
            'total'         => 0,
            'status'        => 'web',
        ]);

        $total = 0;

        foreach ($request->qty as $product_id => $qty) {
            $qty = (float)$qty;
            if ($qty <= 0) continue;

            $product  = Product::find($product_id);
            if (!$product) continue;

            $isUnit    = $product->sale_type === 'unit';
            $price     = (float)$product->price;
            $pesoCassa = (float)($product->avg_box_weight ?? 0);
            $tara      = (float)($product->tara ?? 0);

            if ($isUnit) {
                // Pezzi: qty = numero pezzi
                $pezziPerCassa = max(1, (int)($product->pieces_per_box ?? 1));
                $colli         = ceil($qty / $pezziPerCassa);
                $kgEstimated   = $colli * $pesoCassa;
                $kgNet         = max(0, $kgEstimated - ($colli * $tara));
                $rowTotal      = $qty * $price;
                $priceKg       = null;
            } else {
                // Kg: qty = kg
                $colli       = $pesoCassa > 0 ? ceil($qty / $pesoCassa) : 1;
                $kgEstimated = $qty;
                $kgNet       = max(0, $qty - ($colli * $tara));
                $rowTotal    = $kgNet * $price;
                $priceKg     = $price;
            }

            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $product_id,
                'origin'       => $product->origin,
                'colli'        => $colli,
                'peso_collo'   => $pesoCassa,
                'kg_estimated' => $kgEstimated,
                'kg_real'      => null,
                'tara'         => $tara,
                'kg_net'       => $kgNet,
                'price_kg'     => $priceKg,
                'qty'          => $qty,
                'price'        => $price,
                'total'        => $rowTotal,
            ]);

            $total += $rowTotal;
        }

        $order->update(['total' => $total]);

        return redirect()->back()->with('success', 'Ordine ' . $number . ' inviato! Ti contatteremo per conferma.');
    }
}