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
            $documents              = Document::where('client_id', $client->id)->get();
            $totaleVenduto          = $documents->sum('total');
            $pagato                 = Payment::whereIn('document_id', $documents->pluck('id'))->sum('amount');
            $client->totale_venduto = $totaleVenduto;
            $client->pagato         = $pagato;
            $client->da_incassare   = $totaleVenduto - $pagato;
        }

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $this->extractFields($request);
        $data['order_token'] = Client::generateToken();

        Client::create($data);

        return redirect('/clients')->with('success', 'Cliente creato.');
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
            'client', 'documents', 'revenue', 'cost', 'margin', 'margin_percent'
        ));
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);

        // Genera token se mancante
        if (!$client->order_token) {
            $client->order_token = Client::generateToken();
            $client->save();
        }

        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->update($this->extractFields($request));

        return redirect()->route('clients.show', $id)
            ->with('success', 'Cliente aggiornato.');
    }

    public function destroy($id)
    {
        Client::findOrFail($id)->delete();
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

    // ── HELPER PRIVATO ──────────────────────────────────────
    private function extractFields(Request $request): array
    {
        return [
            'company_name'         => $request->company_name,
            'vat_number'           => $request->vat_number,
            'fiscal_code'          => $request->fiscal_code,
            'address'              => $request->address,
            'city'                 => $request->city,
            'zip'                  => $request->zip,
            'province'             => $request->province,
            'phone'                => $request->phone,
            'email'                => $request->email,
            'payment_terms'        => $request->payment_terms,
            'referente'            => $request->referente,
            'cellulare_referente'  => $request->cellulare_referente,
            'zona_consegna'        => $request->zona_consegna,
            'giorni_consegna'      => $request->giorni_consegna ?? [],
            'giorni_chiusura'      => $request->giorni_chiusura ?? [],
            'fascia_oraria_inizio' => $request->fascia_oraria_inizio,
            'fascia_oraria_fine'   => $request->fascia_oraria_fine,
            'fido'                 => $request->fido ?? 0,
            'note_interne'         => $request->note_interne,
            'stato'                => $request->stato ?? 'attivo',
            'modalita_ordine'      => $request->modalita_ordine ?? 'colli',
        ];
    }
}