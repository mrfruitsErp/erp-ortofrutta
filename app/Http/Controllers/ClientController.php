<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientDeliveryPref;
use App\Models\DeliveryTimeSlot;
use App\Models\Document;
use App\Models\DocumentRow;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PriceList;

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
        $priceLists     = PriceList::attivi()->get();
        $paymentMethods = PaymentMethod::attivi()->get();
        $deliverySlots  = DeliveryTimeSlot::attivi()->get();

        return view('clients.create', compact('priceLists', 'paymentMethods', 'deliverySlots'));
    }

    public function store(Request $request)
    {
        $data = $this->extractFields($request);
        $data['order_token'] = Client::generateToken();

        $client = Client::create($data);

        $this->syncDeliveryPrefs($client, $request);

        return redirect('/clients')->with('success', 'Cliente creato.');
    }

    public function show($id)
    {
        $client = Client::with(['priceList', 'paymentMethod', 'deliveryPrefs'])->findOrFail($id);

        $documents = Document::with('payments')
            ->where('client_id', $id)
            ->orderBy('date', 'desc')
            ->get();

        $revenue = $documents->sum('total');

        // Calcola costo merce
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

        // Calcola pagato e da incassare
        $documentIds = $documents->pluck('id');
        $pagato      = Payment::whereIn('document_id', $documentIds)->sum('amount');
        $da_incassare = $revenue - $pagato;

        return view('clients.show', compact(
            'client', 'documents', 'revenue', 'cost',
            'margin', 'margin_percent', 'pagato', 'da_incassare'
        ));
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);

        if (!$client->order_token) {
            $client->order_token = Client::generateToken();
            $client->save();
        }

        $priceLists     = PriceList::attivi()->get();
        $paymentMethods = PaymentMethod::attivi()->get();
        $deliverySlots  = DeliveryTimeSlot::attivi()->get();

        $clientSlotIds    = $client->deliveryPrefs->pluck('delivery_time_slot_id')->toArray();
        $clientPrefSlotId = $client->deliveryPrefs->where('preferito', true)->first()?->delivery_time_slot_id;

        return view('clients.edit', compact(
            'client', 'priceLists', 'paymentMethods', 'deliverySlots',
            'clientSlotIds', 'clientPrefSlotId'
        ));
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->update($this->extractFields($request));

        $this->syncDeliveryPrefs($client, $request);

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

    // ── SYNC DELIVERY PREFS ──────────────────────────────
    private function syncDeliveryPrefs(Client $client, Request $request): void
    {
        $slotIds  = $request->input('delivery_slots', []);
        $prefSlot = $request->input('delivery_slot_preferito');

        ClientDeliveryPref::where('client_id', $client->id)->delete();

        foreach ($slotIds as $slotId) {
            ClientDeliveryPref::create([
                'client_id'             => $client->id,
                'delivery_time_slot_id' => $slotId,
                'preferito'             => ($slotId == $prefSlot),
            ]);
        }
    }

    // ── EXTRACT FIELDS ────────────────────────────────────
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
            'referente'            => $request->referente,
            'cellulare_referente'  => $request->cellulare_referente,
            'price_list_id'        => $request->price_list_id,
            'payment_method_id'    => $request->payment_method_id,
            'payment_terms'        => $request->payment_terms,
            'fido'                 => $request->fido ?? 0,
            'iban'                 => $request->iban,
            'banca'                => $request->banca,
            'puo_ordinare_kg'      => $request->has('puo_ordinare_kg') ? true : ($request->puo_ordinare_kg_select === 'null' ? null : ($request->puo_ordinare_kg_select === '1')),
            'orario_limite_ordine' => $request->orario_limite_ordine,
            'modalita_ordine'      => $request->modalita_ordine ?? 'colli',
            'zona_consegna'        => $request->zona_consegna,
            'giorni_consegna'      => $request->giorni_consegna ?? [],
            'giorni_chiusura'      => $request->giorni_chiusura ?? [],
            'fascia_oraria_inizio' => $request->fascia_oraria_inizio,
            'fascia_oraria_fine'   => $request->fascia_oraria_fine,
            'note_interne'         => $request->note_interne,
            'stato'                => $request->stato ?? 'attivo',
        ];
    }
}