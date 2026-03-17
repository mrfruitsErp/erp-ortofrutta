<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Client;
use App\Models\Product;
use App\Models\Document;
use App\Models\DocumentRow;
use App\Models\Stock;
use App\Models\StockMovement;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with('client')->orderBy('id', 'desc')->get();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $clients  = Client::orderBy('company_name')->get();
        $products = Product::orderBy('name')->get();
        return view('orders.create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date'      => 'required|date',
        ]);

        $year = date('Y');

        $last = Order::whereYear('date', $year)->orderBy('id', 'desc')->first();

        if ($last && preg_match('/(\d+)$/', $last->number, $m)) {
            $newNum = (int) $m[1] + 1;
        } else {
            $newNum = 1;
        }

        $number = 'ORD-' . $year . '-' . str_pad($newNum, 4, '0', STR_PAD_LEFT);

        $order = Order::create([
            'number'        => $number,
            'client_id'     => $request->client_id,
            'date'          => $request->date,
            'delivery_date' => $request->delivery_date,
            'delivery_slot' => $request->delivery_slot,
            'status'        => 'draft',
            'total'         => 0,
        ]);

        $total = 0;

        if ($request->product_id) {

            foreach ($request->product_id as $index => $productId) {

                if (!$productId) continue;

                $qty         = $request->qty[$index] ?? 0;
                $price       = $request->price[$index] ?? 0;
                $kgEstimated = $request->kg_estimated[$index] ?? 0;
                $kgReal      = $request->kg_real[$index] ?? null;
                $tara        = $request->tara[$index] ?? 0;
                $kgNet       = $request->kg_net[$index] ?? 0;
                $rowTotal    = $request->total[$index] ?? ($kgNet * $price);
                $origin      = $request->origin[$index] ?? null;

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $productId,
                    'qty'          => $qty,
                    'price'        => $price,
                    'kg_estimated' => $kgEstimated,
                    'kg_real'      => $kgReal,
                    'tara'         => $tara,
                    'kg_net'       => $kgNet,
                    'total'        => $rowTotal,
                    'origin'       => $origin,
                ]);

                $total += $rowTotal;
            }
        }

        $order->update(['total' => $total]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Ordine ' . $number . ' creato con successo.');
    }

    public function show(Order $order)
    {
        $order->load('client', 'items.product');
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        if ($order->status == 'invoiced') {
            return back()->with('error', 'Ordine evaso, modifica non consentita');
        }

        $order->load('items.product');
        $clients  = Client::orderBy('company_name')->get();
        $products = Product::orderBy('name')->get();
        return view('orders.edit', compact('order', 'clients', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        if ($order->status == 'invoiced') {
            return back()->with('error', 'Ordine evaso, modifica non consentita');
        }

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date'      => 'required|date',
        ]);

        $order->update([
            'client_id'     => $request->client_id,
            'date'          => $request->date,
            'delivery_date' => $request->delivery_date,
            'delivery_slot' => $request->delivery_slot,
        ]);

        $order->items()->delete();

        $total = 0;

        if ($request->product_id) {

            foreach ($request->product_id as $index => $productId) {

                if (!$productId) continue;

                $qty         = $request->qty[$index] ?? 0;
                $price       = $request->price[$index] ?? 0;
                $kgEstimated = $request->kg_estimated[$index] ?? 0;
                $kgReal      = $request->kg_real[$index] ?? null;
                $tara        = $request->tara[$index] ?? 0;
                $kgNet       = $request->kg_net[$index] ?? 0;
                $rowTotal    = $request->total[$index] ?? ($kgNet * $price);
                $origin      = $request->origin[$index] ?? null;

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $productId,
                    'qty'          => $qty,
                    'price'        => $price,
                    'kg_estimated' => $kgEstimated,
                    'kg_real'      => $kgReal,
                    'tara'         => $tara,
                    'kg_net'       => $kgNet,
                    'total'        => $rowTotal,
                    'origin'       => $origin,
                ]);

                $total += $rowTotal;
            }
        }

        $order->update(['total' => $total]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Ordine aggiornato.');
    }

    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->delete();
        return redirect()->route('orders.index')
            ->with('success', 'Ordine eliminato.');
    }

    public function confirmOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'confirmed']);
        return redirect()->route('orders.show', $id)
            ->with('success', 'Ordine confermato.');
    }

    // 🔥 GENERA DDT SENZA NUMERO
    public function generateDocument($id)
    {
        $order = Order::with('client', 'items.product')->findOrFail($id);

        $document = Document::create([
            'type'      => 'DDT',
            'number'    => null,
            'date'      => date('Y-m-d'),
            'client_id' => $order->client_id,
            'total'     => 0,
            'status'    => 'draft',
        ]);

        $totalDocument = 0;

        foreach ($order->items as $item) {

            $product  = $item->product;
            $vatRate  = $product->vat_rate ?? 4;
            $kgNet    = $item->kg_net ?? $item->kg_estimated ?? 0;
            $price    = $item->price ?? 0;
            $rowTotal = $kgNet * $price;

            DocumentRow::create([
                'document_id'  => $document->id,
                'product_id'   => $item->product_id,
                'boxes'        => $item->qty,
                'kg_estimated' => $item->kg_estimated ?? 0,
                'kg_real'      => $item->kg_real ?? null,
                'price_per_kg' => $price,
                'vat_rate'     => $vatRate,
                'total'        => $rowTotal,
            ]);

            // scarico magazzino
            $stock = Stock::where('product_id', $item->product_id)->first();
            if ($stock) {
                $stock->quantity -= $kgNet;
                $stock->save();
            }

            StockMovement::create([
                'product_id'    => $item->product_id,
                'document_id'   => $document->id,
                'type'          => 'OUT',
                'qty'           => $kgNet,
                'movement_date' => date('Y-m-d'),
            ]);

            $totalDocument += $rowTotal;
        }

        $document->update(['total' => $totalDocument]);

        $order->update(['status' => 'invoiced']);

        return redirect()->route('documents.show', $document->id)
            ->with('success', 'DDT creato in bozza');
    }

    // 🔥 ASSEGNA NUMERO (STAMPA)
    public function assignDdtNumber($id)
    {
        $document = Document::findOrFail($id);

        if ($document->number) {
            return back()->with('error', 'Numero già assegnato');
        }

        $year = date('Y');

        $last = Document::where('type', 'DDT')
            ->whereYear('date', $year)
            ->whereNotNull('number')
            ->orderBy('id', 'desc')
            ->first();

        if ($last && preg_match('/(\d+)$/', $last->number, $m)) {
            $newNum = (int) $m[1] + 1;
        } else {
            $newNum = 1;
        }

        $docNumber = 'DDT-' . $year . '-' . str_pad($newNum, 4, '0', STR_PAD_LEFT);

        $document->update([
            'number' => $docNumber,
            'status' => 'confirmed'
        ]);

        return back()->with('success', 'DDT stampato: ' . $docNumber);
    }

    // 🔥 ANNULLA DDT
    public function cancelDdt($id)
    {
        $document = Document::with('rows')->findOrFail($id);

        if (!$document->number) {
            return back()->with('error', 'DDT non stampato');
        }

        if ($document->status == 'cancelled') {
            return back()->with('error', 'DDT già annullato');
        }

        foreach ($document->rows as $row) {

            $kg = $row->kg_real ?? $row->kg_estimated ?? 0;

            $stock = Stock::where('product_id', $row->product_id)->first();
            if ($stock) {
                $stock->quantity += $kg;
                $stock->save();
            }

            StockMovement::create([
                'product_id'    => $row->product_id,
                'document_id'   => $document->id,
                'type'          => 'IN',
                'qty'           => $kg,
                'movement_date' => date('Y-m-d'),
            ]);
        }

        $document->update(['status' => 'cancelled']);

        $order = Order::find($document->order_id);
        if ($order) {
            $order->update(['status' => 'confirmed']);
        }

        return back()->with('success', 'DDT annullato');
    }

}