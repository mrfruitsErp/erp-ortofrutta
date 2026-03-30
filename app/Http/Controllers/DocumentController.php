<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Client;
use App\Models\Product;
use App\Models\DocumentRow;
use App\Models\Stock;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $new_number = $last ? intval(substr($last->number, -4)) + 1 : 1;
        $formatted = str_pad($new_number, 4, '0', STR_PAD_LEFT);

        $document = Document::create([
            'type' => "DDT",
            'number' => "DDT-$year-$formatted",
            'date' => $request->date,
            'client_id' => $request->client_id,
            'total' => 0,
        ]);

        $total_document = 0;

        if ($request->products) {
            foreach ($request->products as $index => $product_id) {

                if (!$product_id) continue;

                $boxes = $request->qty[$index] ?? 0;
                $price = $request->price[$index] ?? 0;

                $product = Product::find($product_id);
                if (!$product) continue;

                $weight = $product->avg_box_weight ?? 0;
                $tara = $product->tara ?? 0;
                $kg = ($boxes * $weight) - ($boxes * $tara);
                $vat_rate = $product->vat_rate ?? 4;

                $row = DocumentRow::create([
                    'document_id' => $document->id,
                    'product_id' => $product_id,
                    'boxes' => $boxes,
                    'kg_estimated' => $kg,
                    'price_per_kg' => $price,
                    'vat_rate' => $vat_rate,
                    'total' => $kg * $price,
                ]);

                // Magazzino
                $stock = Stock::where('product_id', $product_id)->first();
                if ($stock) {
                    $stock->quantity -= $kg;
                    $stock->save();
                }

                StockMovement::create([
                    'product_id' => $product_id,
                    'document_id' => $document->id,
                    'type' => 'OUT',
                    'qty' => $kg,
                    'movement_date' => date('Y-m-d'),
                ]);

                $total_document += $row->total;
            }
        }

        $document->update(['total' => $total_document]);

        return redirect('/documents')->with('success', 'Documento creato');
    }

    public function show($id)
    {
        $document = Document::with('client')->findOrFail($id);
        $rows = DocumentRow::with('product')->where('document_id', $id)->get();
        return view('documents.show', compact('document', 'rows'));
    }

    public function edit($id)
    {
        $document = Document::with('rows.product')->findOrFail($id);
        $clients = Client::orderBy('company_name')->get();
        $products = Product::orderBy('name')->get();
        return view('documents.edit', compact('document', 'clients', 'products'));
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $document->update([
            'client_id' => $request->client_id,
            'date' => $request->date,
        ]);

        if ($request->delete_rows) {
            DocumentRow::whereIn('id', $request->delete_rows)->delete();
        }

        if ($request->existing_row_id) {
            foreach ($request->existing_row_id as $index => $rowId) {

                $row = DocumentRow::find($rowId);
                if (!$row) continue;

                if ($request->delete_rows && in_array($rowId, $request->delete_rows)) continue;

                $productId = $request->existing_product[$index] ?? $row->product_id;
                $boxes = $request->existing_boxes[$index] ?? $row->boxes;
                $price = $request->existing_price[$index] ?? $row->price_per_kg;

                $product = Product::find($productId);
                $weight = $product->avg_box_weight ?? 0;
                $tara = $product->tara ?? 0;
                $kg = ($boxes * $weight) - ($boxes * $tara);

                $kgReal = $request->existing_kg_real[$index] ?? null;
                $kgNet = $kgReal ? ($kgReal - ($boxes * $tara)) : $kg;

                $row->update([
                    'product_id' => $productId,
                    'boxes' => $boxes,
                    'kg_estimated' => $kg,
                    'kg_real' => $kgReal ?: null,
                    'price_per_kg' => $price,
                    'total' => $kgNet * $price,
                ]);
            }
        }

        $document->update([
            'total' => DocumentRow::where('document_id', $id)->sum('total')
        ]);

        return redirect('/documents/' . $id)->with('success', 'Documento aggiornato');
    }

    public function saveRealWeight(Request $request)
    {
        $row = DocumentRow::with('product')->findOrFail($request->document_row_id);

        $tara = $row->product->tara ?? 0;
        $kgReal = (float) $request->real_weight;
        $kgNet = $kgReal - ($row->boxes * $tara);

        $row->update([
            'kg_real' => $kgReal,
            'total' => $kgNet * $row->price_per_kg
        ]);

        $document = Document::findOrFail($row->document_id);
        $document->update([
            'total' => DocumentRow::where('document_id', $document->id)->sum('total')
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $document->rows()->delete();
        $document->delete();

        return redirect('/documents')->with('success', 'Documento eliminato');
    }

    /**
     * Genera e visualizza il PDF del documento
     */
    public function pdf($id)
    {
        $document = Document::with('client')->findOrFail($id);
        $rows = DocumentRow::with('product')->where('document_id', $id)->get();

        $pdf = Pdf::loadView('documents.pdf', [
            'document' => $document,
            'rows' => $rows
        ])->setOptions([
            'defaultFont'       => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'   => false,
            'margin_top'        => 0,
            'margin_bottom'     => 0,
            'margin_left'       => 0,
            'margin_right'      => 0,
            'dpi'               => 150,
        ]);

        // Imposta formato A4
        $pdf->setPaper('a4', 'portrait');

        // Visualizza nel browser (usa ->download() per forzare il download)
        return $pdf->stream('DDT-' . $document->number . '.pdf');
    }
}