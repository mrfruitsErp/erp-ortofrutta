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
        $orders = Order::with(['client', 'documents'])->orderBy('id', 'desc')->get();
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

        $year      = date('Y');
        $lastOrder = Order::whereYear('date', $year)->whereNotNull('number')->orderBy('id', 'desc')->first();

        $nextNumber = 1;
        if ($lastOrder && preg_match('/(\d+)$/', $lastOrder->number, $match)) {
            $nextNumber = (int)$match[1] + 1;
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
        return redirect()->route('orders.index')->with('success', 'Ordine eliminato.');
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

        $year  = date('Y');
        $last  = Document::whereYear('date', $year)->orderBy('id', 'desc')->first();
        $newNum    = $last ? intval(substr($last->number, -4)) + 1 : 1;
        $ddtNumber = 'DDT-' . $year . '-' . str_pad($newNum, 4, '0', STR_PAD_LEFT);

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

            $stock = Stock::where('product_id', $item->product_id)->first();
            if ($stock) { $stock->quantity -= $kgNet; $stock->save(); }

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
            ->with('success', 'DDT ' . $ddtNumber . ' generato dall\'ordine ' . $order->number . '.');
    }

    // ── STAMPA MULTIPLA CON DETTAGLIO ────────────────────
    // Vista standalone (no layout ERP) — apre in nuova tab
    // Filtri: ?stato=confirmed&from=2026-01-01&to=2026-03-31&q=sigma
    public function printView(Request $request)
    {
        $query = Order::with(['client', 'items.product'])->orderBy('date', 'desc');

        if ($request->filled('stato')) {
            $query->where('status', $request->stato);
        }
        if ($request->filled('from')) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('date', '<=', $request->to);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('number', 'like', '%' . $q . '%')
                    ->orWhereHas('client', fn($c) => $c->where('company_name', 'like', '%' . $q . '%'));
            });
        }

        $orders = $query->get();

        return view('orders.print', compact('orders'));
    }

    // ── AZIONI MASSIVE ────────────────────────────────────
    public function massiveAction(Request $request)
    {
        $ids    = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Nessun ordine selezionato']);
        }

        try {
            $count = 0;
            foreach ($ids as $id) {
                $order = Order::find($id);
                if (!$order) continue;

                if ($action === 'confirm' && in_array($order->status, ['draft', 'web'])) {
                    $order->update(['status' => 'confirmed']);
                    $count++;
                }
                if ($action === 'delete' && in_array($order->status, ['draft', 'web'])) {
                    $order->items()->delete();
                    $order->delete();
                    $count++;
                }
            }
            return response()->json([
                'success' => true, 'count' => $count,
                'message' => $count . ' ordini ' . ($action === 'delete' ? 'eliminati' : 'confermati'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── EXPORT 1: Ordini riepilogo ────────────────────────
    public function exportOrders(Request $request)
    {
        $query = Order::with('client')->orderBy('date', 'desc');

        if ($request->filled('ids')) {
            $ids = array_filter(explode(',', $request->ids), 'is_numeric');
            if (!empty($ids)) $query->whereIn('id', $ids);
        }
        if ($request->filled('stato')) $query->where('status', $request->stato);
        if ($request->filled('from'))  $query->where('date', '>=', $request->from);
        if ($request->filled('to'))    $query->where('date', '<=', $request->to);

        $orders    = $query->get();
        $statusMap = ['draft' => 'Bozza', 'web' => 'Web', 'confirmed' => 'Confermato', 'invoiced' => 'Evaso'];

        return response()->stream(function () use ($orders, $statusMap) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, ['ID', 'Numero', 'Cliente', 'Data', 'Stato', 'Totale (€)', 'Note'], ';');
            foreach ($orders as $o) {
                fputcsv($f, [
                    $o->id,
                    $o->number ?? '',
                    $o->client->company_name ?? '',
                    \Carbon\Carbon::parse($o->date)->format('d/m/Y'),
                    $statusMap[$o->status] ?? $o->status,
                    number_format($o->total ?? 0, 2, ',', ''),
                    $o->notes ?? '',
                ], ';');
            }
            fclose($f);
        }, 200, $this->csvHeaders('ordini_' . date('Y-m-d') . '.csv'));
    }

    // ── EXPORT 2: Prodotti ordinati dettaglio ─────────────
    public function exportOrderItems(Request $request)
    {
        $query = OrderItem::with(['order.client', 'product'])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->orderBy('orders.date', 'desc')
            ->select('order_items.*');

        if ($request->filled('stato'))      $query->where('orders.status', $request->stato);
        if ($request->filled('from'))       $query->where('orders.date', '>=', $request->from);
        if ($request->filled('to'))         $query->where('orders.date', '<=', $request->to);
        if ($request->filled('product_id')) $query->where('order_items.product_id', $request->product_id);

        $items     = $query->get();
        $statusMap = ['draft' => 'Bozza', 'web' => 'Web', 'confirmed' => 'Confermato', 'invoiced' => 'Evaso'];

        $columns = [
            'Data Ordine', 'Numero Ordine', 'Stato', 'Cliente',
            'Prodotto', 'Origine', 'Colli', 'Kg Stimati', 'Kg Reali', 'Kg Netti', 'Tara (kg)',
            '€/Kg', 'Totale Riga (€)',
        ];

        return response()->stream(function () use ($items, $columns, $statusMap) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, $columns, ';');
            foreach ($items as $item) {
                $o = $item->order;
                fputcsv($f, [
                    $o ? \Carbon\Carbon::parse($o->date)->format('d/m/Y') : '',
                    $o->number ?? '',
                    $statusMap[$o->status ?? ''] ?? ($o->status ?? ''),
                    $o->client->company_name ?? '',
                    $item->product->name ?? '',
                    $item->origin ?? '',
                    number_format($item->colli ?? 0, 0, ',', ''),
                    number_format($item->kg_estimated ?? 0, 3, ',', ''),
                    number_format($item->kg_real ?? 0, 3, ',', ''),
                    number_format($item->kg_net ?? 0, 3, ',', ''),
                    number_format($item->tara ?? 0, 3, ',', ''),
                    number_format($item->price_kg ?? $item->price ?? 0, 2, ',', ''),
                    number_format($item->total ?? 0, 2, ',', ''),
                ], ';');
            }
            fclose($f);
        }, 200, $this->csvHeaders('prodotti_ordinati_dettaglio_' . date('Y-m-d') . '.csv'));
    }

    // ── EXPORT 3: Totali per prodotto ─────────────────────
    public function exportProductSummary(Request $request)
    {
        $query = OrderItem::with('product')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->select(
                'order_items.product_id',
                \DB::raw('SUM(order_items.colli)        AS tot_colli'),
                \DB::raw('SUM(order_items.kg_estimated) AS tot_kg_stimati'),
                \DB::raw('SUM(order_items.kg_net)       AS tot_kg_netti'),
                \DB::raw('SUM(order_items.total)        AS tot_importo'),
                \DB::raw('COUNT(DISTINCT orders.id)     AS n_ordini'),
                \DB::raw('MIN(orders.date)              AS prima_data'),
                \DB::raw('MAX(orders.date)              AS ultima_data')
            )
            ->groupBy('order_items.product_id');

        if ($request->filled('stato')) $query->where('orders.status', $request->stato);
        if ($request->filled('from'))  $query->where('orders.date', '>=', $request->from);
        if ($request->filled('to'))    $query->where('orders.date', '<=', $request->to);

        $rows = $query->get()->map(function ($row) {
            $row->product = Product::find($row->product_id);
            return $row;
        })->sortBy(fn($r) => $r->product->name ?? 'zzz');

        $columns = [
            'Prodotto', 'Categoria', 'N. Ordini',
            'Tot. Colli', 'Tot. Kg Stimati', 'Tot. Kg Netti',
            'Tot. Importo (€)', '€/Kg Medio', 'Prima Data', 'Ultima Data',
        ];

        return response()->stream(function () use ($rows, $columns) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, $columns, ';');

            $totColli = $totKgS = $totKgN = $totImp = 0;

            foreach ($rows as $row) {
                $kgMedio = $row->tot_kg_netti > 0 ? $row->tot_importo / $row->tot_kg_netti : 0;
                fputcsv($f, [
                    $row->product->name ?? '—',
                    $row->product->category ?? '—',
                    $row->n_ordini,
                    number_format($row->tot_colli ?? 0, 0, ',', ''),
                    number_format($row->tot_kg_stimati ?? 0, 3, ',', ''),
                    number_format($row->tot_kg_netti ?? 0, 3, ',', ''),
                    number_format($row->tot_importo ?? 0, 2, ',', ''),
                    number_format($kgMedio, 2, ',', ''),
                    $row->prima_data ? \Carbon\Carbon::parse($row->prima_data)->format('d/m/Y') : '',
                    $row->ultima_data ? \Carbon\Carbon::parse($row->ultima_data)->format('d/m/Y') : '',
                ], ';');
                $totColli += $row->tot_colli ?? 0;
                $totKgS   += $row->tot_kg_stimati ?? 0;
                $totKgN   += $row->tot_kg_netti ?? 0;
                $totImp   += $row->tot_importo ?? 0;
            }

            fputcsv($f, [
                'TOTALE', '', '',
                number_format($totColli, 0, ',', ''),
                number_format($totKgS, 3, ',', ''),
                number_format($totKgN, 3, ',', ''),
                number_format($totImp, 2, ',', ''),
                '', '', '',
            ], ';');

            fclose($f);
        }, 200, $this->csvHeaders('riepilogo_prodotti_ordinati_' . date('Y-m-d') . '.csv'));
    }

    // ── Helper ────────────────────────────────────────────
    private function csvHeaders(string $filename): array
    {
        return [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];
    }

    // ── LOGICA RIGHE ──────────────────────────────────────
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
                $kgNet         = max(0, $kgEstimated - $colli * $taraUnit);
                $rowTotal      = $pezziTotali * $price;
                $qty           = $pezziTotali;
                $priceKg       = null;
            } else {
                $kgEstimated = $colli * $pesoCassa;
                $kgUsato     = $kgReal ?? $kgEstimated;
                $kgNet       = max(0, $kgUsato - $colli * $taraUnit);
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