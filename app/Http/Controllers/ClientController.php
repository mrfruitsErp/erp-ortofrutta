<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Document;
use App\Models\DocumentRow;
use App\Models\Payment;

class ClientController extends Controller
{

    public function index()
    {
        $clients = Client::all();

        foreach ($clients as $client) {
            $documents             = Document::where('client_id', $client->id)->get();
            $totaleVenduto         = $documents->sum('total');
            $pagato                = Payment::whereIn('document_id', $documents->pluck('id'))->sum('amount');
            $client->totale_venduto = $totaleVenduto;
            $client->pagato        = $pagato;
            $client->da_incassare  = $totaleVenduto - $pagato;
        }

        return view('clients.index', compact('clients'));
    }


    public function create()
    {
        return view('clients.create');
    }


    public function store(Request $request)
    {
        $client = new Client();

        $client->company_name  = $request->ragione_sociale;
        $client->vat_number    = $request->partita_iva;
        $client->fiscal_code   = $request->codice_fiscale;
        $client->address       = $request->indirizzo;
        $client->city          = $request->citta;
        $client->zip           = $request->cap;
        $client->province      = $request->provincia;
        $client->phone         = $request->telefono;
        $client->email         = $request->email;
        $client->payment_terms = $request->metodo_pagamento;

        $client->save();

        return redirect('/clients')->with('success', 'Cliente creato con successo.');
    }


    public function show($id)
    {
        $client    = Client::findOrFail($id);
        $documents = Document::where('client_id', $id)->orderBy('date', 'desc')->get();

        $revenue = $documents->sum('total');

        $rows = DocumentRow::join('documents', 'documents.id', '=', 'document_rows.document_id')
            ->where('documents.client_id', $id)
            ->select('document_rows.*')
            ->get();

        $cost = 0;
        foreach ($rows as $row) {
            $kg    = $row->kg_real ?? $row->kg_estimated;
            $cost += $kg * $row->price_per_kg;
        }

        $margin         = $revenue - $cost;
        $margin_percent = $revenue > 0 ? ($margin / $revenue) * 100 : 0;

        return view('clients.show', compact(
            'client',
            'documents',
            'revenue',
            'cost',
            'margin',
            'margin_percent'
        ));
    }


    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }


    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $client->company_name  = $request->ragione_sociale;
        $client->vat_number    = $request->partita_iva;
        $client->fiscal_code   = $request->codice_fiscale;
        $client->address       = $request->indirizzo;
        $client->city          = $request->citta;
        $client->zip           = $request->cap;
        $client->province      = $request->provincia;
        $client->phone         = $request->telefono;
        $client->email         = $request->email;
        $client->payment_terms = $request->metodo_pagamento;

        $client->save();

        return redirect('/clients')->with('success', 'Cliente aggiornato.');
    }


    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return redirect('/clients')->with('success', 'Cliente eliminato.');
    }


    public function report()
    {
        $clients = Client::all();
        $data    = [];

        foreach ($clients as $client) {
            $documents = Document::where('client_id', $client->id)->get();
            $revenue   = $documents->sum('total');

            $rows = DocumentRow::join('documents', 'documents.id', '=', 'document_rows.document_id')
                ->where('documents.client_id', $client->id)
                ->select('document_rows.*')
                ->get();

            $cost = 0;
            foreach ($rows as $row) {
                $kg    = $row->kg_real ?? $row->kg_estimated;
                $cost += $kg * $row->price_per_kg;
            }

            $margin  = $revenue - $cost;
            $percent = $revenue > 0 ? ($margin / $revenue) * 100 : 0;

            $data[] = [
                'name'           => $client->company_name,
                'revenue'        => $revenue,
                'cost'           => $cost,
                'margin'         => $margin,
                'margin_percent' => $percent,
            ];
        }

        usort($data, fn($a, $b) => $b['margin'] <=> $a['margin']);

        return view('reports.clients', ['clients' => $data]);
    }

}