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
        $documents = Document::with('client')->get();
        return view('documents.index', compact('documents'));
    }


    public function create()
    {
        $clients  = Client::all();
        $products = Product::all();

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
        $vat_total      = 0;

        if ($request->products) {

            foreach ($request->products as $index => $product_id) {

                if (!$product_id) continue;

                $boxes = $request->qty[$index] ?? 0;
                $price = $request->price[$index] ?? 0;

                $product = Product::find($product_id);

                if (!$product) continue;

                $weight = $product->avg_box_weight ?? 0;
                $tara   = $product->tara ?? 0;

                $kg = ($boxes * $weight) - ($boxes * $tara);

                /* IVA PRODOTTO */

                $vat_rate = $product->vat_rate ?? 4;

                $row = new DocumentRow();

                $row->document_id  = $document->id;
                $row->product_id   = $product_id;
                $row->boxes        = $boxes;
                $row->kg_estimated = $kg;
                $row->price_per_kg = $price;
                $row->vat_rate     = $vat_rate;

                $row->total = $kg * $price;

                $row->save();

                /* CALCOLO IVA */

                $vat_total += $row->total * ($vat_rate / 100);

                /* SCARICO MAGAZZINO */

                $stock = Stock::where('product_id', $product_id)->first();

                if ($stock) {

                    $stock->quantity -= $kg;
                    $stock->save();

                }

                /* MOVIMENTO MAGAZZINO */

                $movement = new StockMovement();

                $movement->product_id    = $product_id;
                $movement->document_id   = $document->id;
                $movement->type          = "OUT";
                $movement->qty           = $kg;
                $movement->movement_date = date('Y-m-d');

                $movement->save();

                $total_document += $row->total;

            }

        }

        $document->total     = $total_document;
        $document->vat_total = $vat_total;

        $document->save();

        return redirect('/documents')
            ->with('success', 'Documento creato');

    }


    public function show($id)
    {

        $document = Document::with('client')
            ->findOrFail($id);

        $rows = DocumentRow::with('product')
            ->where('document_id', $id)
            ->get();

        return view('documents.show', compact('document', 'rows'));

    }


    public function edit($id)
    {

        $document = Document::with('rows.product')
            ->findOrFail($id);

        $clients  = Client::orderBy('company_name')->get();
        $products = Product::orderBy('name')->get();

        return view('documents.edit', compact('document', 'clients', 'products'));

    }


    public function update(Request $request, $id)
    {

        $document = Document::findOrFail($id);

        $document->client_id = $request->client_id;
        $document->date      = $request->date;

        $document->save();

        $document->total = DocumentRow::where('document_id', $id)->sum('total');

        $document->save();

        return redirect('/documents/' . $id)
            ->with('success', 'Documento aggiornato');

    }


    public function pdf($id)
    {

        $document = Document::with('client')
            ->findOrFail($id);

        $rows = DocumentRow::with('product')
            ->where('document_id', $id)
            ->get();

        return view('documents.pdf', compact('document', 'rows'));

    }

}