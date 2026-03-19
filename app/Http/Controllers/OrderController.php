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

        $lastOrder = Order::whereYear('date', $year)
            ->whereNotNull('number')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder && preg_match('/(\d+)$/', $lastOrder->number, $match)) {
            $nextNumber = (int)$match[1] + 1;
        } else {
            $nextNumber = 1;
        }

        $number = 'ORD-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $order = Order::create([
            'number'    => $number,
            'client_id' => $request->client_id,
            'date'      => $request->date,
            'notes'     => $request->notes,
            'total'     => 0,
        ]);

        $total = $this->saveItems($order->id, $request);

        $order->update(['total' => $total]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Ordine ' . $number . ' creato.');
    }

    public function show(Order $order)
    {
        $order->load(['client', 'items.product', 'documents']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $clients  = Client::orderBy('company_name')->get();
        $products = Product::orderBy('name')->get();
        $order->load('items.product');
        return view('orders.edit', compact('order', 'clients', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'date'      => 'required|date',
        ]);

        $order->update([
            'client_id' => $request->client_id,
            'date'      => $request->date,
            'notes'     => $request->notes,
        ]);

        $order->items()->delete();

        $total = $this->saveItems($order->id, $request);

        $order->update(['total' => $total]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Ordine ' . $order->number . ' aggiornato.');
    }

    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Ordine eliminato.');
    }

    public function confirmOrder(Order $order)
    {
        if (!in_array($order->status, ['draft', 'web'])) {
            return redirect()->back()->with('error', 'L\'ordine non può essere confermato.');
        }

        $order->update(['status' => 'confirmed']);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Ordine ' . $order->number . ' confermato.');
    }

    public function generateDocument(Order $order)
    {
        if ($order->status !== 'confirmed') {
            return redirect()->back()->with('error', 'Solo gli ordini confermati possono generare un DDT.');
        }

        $order->load(['items.product', 'client']);

        // Numero DDT progressivo
        $year = date('Y');
        $last = Document::whereYear('date', $year)->orderBy('id', 'desc')->first();

        if ($last) {
            $lastNum = intval(substr($last->number, -4));
            $newNum  = $lastNum + 1;
        } else {
            $newNum = 1;
        }

        $ddtNumber = 'DDT-' . $year . '-' . str_pad($newNum, 4, '0', STR_PAD_LEFT);

        // Crea documento DDT
        $document = Document::create([
            'type'      => 'DDT',
            'number'    => $ddtNumber,
            'date'      => $order->date,
            'client_id' => $order->client_id,
            'order_id'  => $order->id,
            'total'     => 0,
        ]);

        $totalDocument = 0;

        foreach ($order->items as $item) {
            $product  = $item->product;
            $vatRate  = $product->vat_rate ?? 4;
            $kgNet    = $item->kg_net ?? 0;
            $price    = $item->price_kg ?? $item->price ?? 0;
            $rowTotal = $kgNet * $price;

            DocumentRow::create([
                'document_id'  => $document->id,
                'product_id'   => $item->product_id,
                'boxes'        => $item->colli,
                'kg_estimated' => $item->kg_estimated,
                'kg_real'      => $item->kg_real,
                'price_per_kg' => $price,
                'vat_rate'     => $vatRate,
                'total'        => $rowTotal,
            ]);

            // Scarico magazzino
            $stock = Stock::where('product_id', $item->product_id)->first();
            if ($stock) {
                $stock->quantity -= $kgNet;
                $stock->save();
            }

            // Movimento magazzino
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

        // Segna ordine come evaso
        $order->update(['status' => 'invoiced']);

        return redirect()->route('documents.show', $document->id)
            ->with('success', 'DDT ' . $ddtNumber . ' generato dall\'ordine ' . $order->number . '.');
    }

    // -------------------------------------------------------
    // LOGICA RIGHE
    // -------------------------------------------------------
    private function saveItems($orderId, Request $request): float
    {
        $total = 0;

        foreach ($request->product_id ?? [] as $index => $productId) {

            if (!$productId) continue;

            $product = Product::find($productId);
            if (!$product) continue;

            $isUnit    = ($product->sale_type === 'unit');
            $colli     = max(1, (float)($request->colli[$index] ?? 1));
            $origin    = $request->origin[$index] ?? $product->origin;
            $price     = (float)($request->price[$index] ?? $product->price ?? 0);
            $kgReal    = (float)($request->kg_real[$index] ?? 0) ?: null;

            $pesoCassa = (float)($product->avg_box_weight ?? 0);
            $taraUnit  = (float)($product->tara ?? 0);

            if ($isUnit) {

                $pezziPerCassa = max(1, (int)($product->pieces_per_box ?? 0));
                $pezziTotali   = $colli * $pezziPerCassa;
                $kgEstimated   = $colli * $pesoCassa;
                $taraTot       = $colli * $taraUnit;
                $kgNet         = max(0, $kgEstimated - $taraTot);
                $rowTotal      = $pezziTotali * $price;
                $qty           = $pezziTotali;
                $priceKg       = null;

            } else {

                $kgEstimated = $colli * $pesoCassa;
                $taraTot     = $colli * $taraUnit;
                $kgUsato     = $kgReal ?? $kgEstimated;
                $kgNet       = max(0, $kgUsato - $taraTot);
                $rowTotal    = $kgNet * $price;
                $qty         = $kgNet;
                $priceKg     = $price;
            }

            OrderItem::create([
                'order_id'     => $orderId,
                'product_id'   => $productId,
                'origin'       => $origin,
                'colli'        => $colli,
                'peso_collo'   => $pesoCassa,
                'kg_estimated' => $kgEstimated,
                'kg_real'      => $kgReal,
                'tara'         => $taraUnit,
                'kg_net'       => $kgNet,
                'price_kg'     => $priceKg,
                'qty'          => $qty,
                'price'        => $price,
                'total'        => $rowTotal,
            ]);

            $total += $rowTotal;
        }

        return $total;
    }
}