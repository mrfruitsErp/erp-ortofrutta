<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Client;
use App\Models\Product;
use App\Models\DocumentRow;
use App\Models\Stock;
use App\Models\StockMovement;

class DocumentController extends Controller
{

    public function index()
    {
        $documents = Document::with('client')->orderBy('id','desc')->get();
        return view('documents.index', compact('documents'));
    }


    public function create()
    {
        $clients  = Client::orderBy('company_name')->get();
        $products = Product::orderBy('name')->get();
        return view('documents.create', compact('clients', 'products'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
        ]);

        $year = date('Y');

        $last = Document::whereYear('date', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $last_number = intval(substr($last->number, -4));
            $new_number  = $last_number + 1;
        } else {
            $new_number = 1;
        }

        $formatted = str_pad($new_number, 4, '0', STR_PAD_LEFT);

        $document = new Document();
        $document->type      = "DDT";
        $document->number    = "DDT-$year-$formatted";
        $document->date      = $request->date;
        $document->client_id = $request->client_id;
        $document->total     = 0;
        $document->save();

        $total_document = 0;

        if ($request->products) {

            foreach ($request->products as $index => $product_id) {

                if (!$product_id) continue;

                $boxes   = $request->qty[$index]   ?? 0;
                $price   = $request->price[$index]  ?? 0;
                $product = Product::find($product_id);

                if (!$product) continue;

                $weight   = $product->avg_box_weight ?? 0;
                $tara     = $product->tara           ?? 0;
                $kg       = ($boxes * $weight) - ($boxes * $tara);
                $vat_rate = $product->vat_rate       ?? 4;

                $row = DocumentRow::create([
                    'document_id'  => $document->id,
                    'product_id'   => $product_id,
                    'boxes'        => $boxes,
                    'kg_estimated' => $kg,
                    'price_per_kg' => $price,
                    'vat_rate'     => $vat_rate,
                    'total'        => $kg * $price,
                ]);

                // Scarico magazzino
                $stock = Stock::where('product_id', $product_id)->first();
                if ($stock) {
                    $stock->quantity -= $kg;
                    $stock->save();
                }

                // Movimento magazzino
                StockMovement::create([
                    'product_id'    => $product_id,
                    'document_id'   => $document->id,
                    'type'          => 'OUT',
                    'qty'           => $kg,
                    'movement_date' => date('Y-m-d'),
                ]);

                $total_document += $row->total;
            }
        }

        $document->total = $total_document;
        $document->save();

        return redirect('/documents')->with('success', 'Documento creato');
    }


    public function show($id)
    {
        $document = Document::with('client')->findOrFail($id);
        $rows     = DocumentRow::with('product')->where('document_id', $id)->get();
        return view('documents.show', compact('document', 'rows'));
    }


    public function edit($id)
    {
        $document = Document::with('rows.product')->findOrFail($id);
        $clients  = Client::orderBy('company_name')->get();
        $products = Product::orderBy('name')->get();
        return view('documents.edit', compact('document', 'clients', 'products'));
    }


    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        // Aggiorna testata
        $document->client_id = $request->client_id;
        $document->date      = $request->date;
        $document->save();

        // Elimina righe marcate per cancellazione
        if ($request->delete_rows) {
            DocumentRow::whereIn('id', $request->delete_rows)->delete();
        }

        // Aggiorna righe esistenti
        if ($request->existing_row_id) {

            foreach ($request->existing_row_id as $index => $rowId) {

                $row = DocumentRow::find($rowId);
                if (!$row) continue;

                if ($request->delete_rows && in_array($rowId, $request->delete_rows)) continue;

                $productId = $request->existing_product[$index] ?? $row->product_id;
                $boxes     = $request->existing_boxes[$index]   ?? $row->boxes;
                $price     = $request->existing_price[$index]   ?? $row->price_per_kg;

                $product = Product::find($productId);
                $weight  = $product->avg_box_weight ?? 0;
                $tara    = $product->tara           ?? 0;
                $kg      = ($boxes * $weight) - ($boxes * $tara);

                $kgReal = $request->existing_kg_real[$index] ?? null;
                $kgNet  = $kgReal ? ($kgReal - ($boxes * $tara)) : $kg;

                $row->product_id   = $productId;
                $row->boxes        = $boxes;
                $row->kg_estimated = $kg;
                $row->kg_real      = $kgReal ?: null;
                $row->price_per_kg = $price;
                $row->total        = $kgNet * $price;
                $row->save();
            }
        }

        // Ricalcola totale documento
        $document->total = DocumentRow::where('document_id', $id)->sum('total');
        $document->save();

        return redirect('/documents/' . $id)->with('success', 'Documento aggiornato');
    }


    // ─── SALVA PESO REALE (AJAX) ─────────────────────────────────────────────
    // La view manda: document_row_id, real_weight

    public function saveRealWeight(Request $request)
    {
        $row = DocumentRow::with('product')->findOrFail($request->document_row_id);

        $product  = $row->product;
        $tara     = $product->tara ?? 0;
        $kgReal   = (float) $request->real_weight;
        $kgNet    = $kgReal - ($row->boxes * $tara);

        $row->kg_real = $kgReal;
        $row->total   = $kgNet * $row->price_per_kg;
        $row->save();

        // Aggiorna totale documento
        $document        = Document::findOrFail($row->document_id);
        $document->total = DocumentRow::where('document_id', $document->id)->sum('total');
        $document->save();

        return response()->json([
            'success'           => true,
            'kg_net'            => round($kgNet, 3),
            'new_total_row'     => round($row->total, 2),
            'new_total_document'=> round($document->total, 2),
        ]);
    }


    public function pdf($id)
    {
        $document = Document::with('client')->findOrFail($id);
        $rows     = DocumentRow::with('product')->where('document_id', $id)->get();
        return view('documents.pdf', compact('document', 'rows'));
    }


    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $document->rows()->delete();
        $document->delete();
        return redirect('/documents')->with('success', 'Documento eliminato');
    }

}