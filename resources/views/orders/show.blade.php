@extends('layouts.app')

@section('page-title','Ordine ' . $order->number)

@section('content')

{{-- ── HEADER ── --}}
<div class="page-header">
    <div>
        <div class="page-title">📦 Ordine {{ $order->number }}</div>
        <div class="page-sub">Cliente: <strong>{{ $order->client->company_name ?? '' }}</strong></div>
    </div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">

        <a href="{{ route('orders.index') }}" class="btn btn-secondary">← Ordini</a>

        <button onclick="window.print()" class="btn btn-secondary" style="padding:7px 14px;font-size:13px" title="Stampa questo ordine">🖨️ Stampa</button>

        @if($order->status === 'draft')
            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-secondary">✏️ Modifica</a>
            <a href="{{ route('orders.confirm', $order->id) }}" class="btn btn-primary"
               onclick="return confirm('Confermare l\'ordine {{ $order->number }}?')">✅ Conferma</a>
        @endif

        @if($order->status === 'web')
            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-secondary">✏️ Modifica</a>
            <a href="{{ route('orders.confirm', $order->id) }}" class="btn btn-primary"
               onclick="return confirm('Confermare l\'ordine {{ $order->number }}?')">✅ Accetta e Conferma</a>
        @endif

        @if($order->status === 'confirmed')
            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-secondary">✏️ Modifica</a>
            <a href="{{ route('orders.generateDocument', $order->id) }}" class="btn btn-primary"
               onclick="return confirm('Generare DDT dall\'ordine {{ $order->number }}?')">📄 Genera DDT</a>
        @endif

        @if($order->status === 'invoiced')
            @if($order->documents->first())
                <a href="/documents/{{ $order->documents->first()->id }}" class="btn btn-secondary" style="color:var(--green)">📄 Apri DDT</a>
            @endif
            <span class="btn btn-secondary" style="opacity:.5;cursor:default;font-size:12px">✔ Evaso</span>
        @endif

    </div>
</div>

@if(session('success'))
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:8px;background:#d4edda;color:#155724;font-size:14px">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:8px;background:#fde8e8;color:#8b0000;font-size:14px">⚠ {{ session('error') }}</div>
@endif

{{-- ── INTESTAZIONE STAMPA (visibile solo in print) ── --}}
<div id="print-header-order">
    <table style="width:100%;margin-bottom:16px;border:none">
        <tr>
            <td style="border:none;padding:0;vertical-align:top;width:50%">
                <div style="font-size:18px;font-weight:700;color:#1a6b3c">Mr. Fruits</div>
                <div style="font-size:11px;color:#555">Gestionale ERP</div>
            </td>
            <td style="border:none;padding:0;text-align:right;vertical-align:top">
                <div style="font-size:16px;font-weight:700">ORDINE {{ $order->number }}</div>
                <div style="font-size:11px;color:#555">
                    Data: {{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}
                    &nbsp;·&nbsp;
                    Stampato il {{ date('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>
    <hr style="border:2px solid #1a6b3c;margin-bottom:12px">
    <table style="width:100%;margin-bottom:16px;border:none">
        <tr>
            <td style="border:none;padding:0;vertical-align:top;width:50%">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#888;margin-bottom:3px">Cliente</div>
                <div style="font-size:13px;font-weight:700">{{ $order->client->company_name ?? '—' }}</div>
                @if($order->client->address ?? null)
                <div style="font-size:11px;color:#555">{{ $order->client->address }}</div>
                @endif
                @if($order->client->city ?? null)
                <div style="font-size:11px;color:#555">{{ $order->client->city }}{{ $order->client->province ? ' (' . $order->client->province . ')' : '' }}</div>
                @endif
            </td>
            <td style="border:none;padding:0;vertical-align:top">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#888;margin-bottom:3px">Dettagli ordine</div>
                <table style="border:none;font-size:11px">
                    <tr><td style="border:none;padding:1px 8px 1px 0;color:#666">Stato</td>
                        <td style="border:none;padding:1px 0;font-weight:600">
                            @php $stati = ['draft'=>'Bozza','web'=>'Web','confirmed'=>'Confermato','invoiced'=>'Evaso']; @endphp
                            {{ $stati[$order->status] ?? $order->status }}
                        </td></tr>
                    @if($order->delivery_date)
                    <tr><td style="border:none;padding:1px 8px 1px 0;color:#666">Consegna</td>
                        <td style="border:none;padding:1px 0;font-weight:600">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</td></tr>
                    @endif
                    @if($order->delivery_slot)
                    <tr><td style="border:none;padding:1px 8px 1px 0;color:#666">Fascia oraria</td>
                        <td style="border:none;padding:1px 0;font-weight:600">{{ $order->delivery_slot }}</td></tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
</div>

{{-- ── INFO ORDINE (schermo) ── --}}
<div class="card" style="margin-bottom:20px" id="order-info-screen">
    <div style="display:flex;gap:32px;flex-wrap:wrap;align-items:flex-start">
        <div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:4px">Numero</div>
            <div style="font-weight:700;font-family:'DM Mono',monospace">{{ $order->number }}</div>
        </div>
        <div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:4px">Data</div>
            <div>{{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}</div>
        </div>
        @if($order->delivery_date)
        <div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:4px">Consegna</div>
            <div>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</div>
        </div>
        @endif
        @if($order->delivery_slot)
        <div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:4px">Fascia oraria</div>
            <div>{{ $order->delivery_slot }}</div>
        </div>
        @endif
        <div>
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:4px">Stato</div>
            <div>
                @if($order->status === 'draft')
                    <span style="background:#fff3e0;color:#e65100;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">● Bozza</span>
                @elseif($order->status === 'web')
                    <span style="background:#f3e8ff;color:#6b21a8;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">● Web</span>
                @elseif($order->status === 'confirmed')
                    <span style="background:#e3f0ff;color:#1a56a0;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">● Confermato</span>
                @elseif($order->status === 'invoiced')
                    <span style="background:#d4edda;color:#155724;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">● Evaso</span>
                @endif
            </div>
        </div>
        @if($order->notes)
        <div style="flex:1;min-width:200px">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:4px">Note</div>
            <div style="font-size:13px;font-style:italic;color:var(--muted)">{{ $order->notes }}</div>
        </div>
        @endif
    </div>
</div>

{{-- ── RIGHE ORDINE ── --}}
<div class="card" style="padding:0;overflow:hidden" id="order-items">
    <table>
        <thead>
            <tr>
                <th>Prodotto</th>
                <th style="text-align:center;width:80px">Origine</th>
                <th style="text-align:center;width:50px">UM</th>
                <th style="text-align:center;width:70px">Colli</th>
                <th style="text-align:right;width:100px">Kg stimati</th>
                <th style="text-align:right;width:100px">Kg reali</th>
                <th style="text-align:right;width:100px">Kg netti</th>
                <th style="text-align:right;width:90px">Prezzo</th>
                <th style="text-align:right;width:110px">Totale</th>
            </tr>
        </thead>
        <tbody>
        @forelse($order->items as $item)
        @php
            $isUnit = ($item->product->sale_type ?? 'kg') === 'unit';
            $um     = $isUnit ? 'PZ' : 'KG';
        @endphp
        <tr>
            <td style="font-weight:600">{{ $item->product->name ?? '—' }}</td>
            <td style="text-align:center;font-size:13px;color:var(--muted)">{{ $item->origin ?? $item->product->origin ?? '—' }}</td>
            <td style="text-align:center;font-weight:700;font-size:12px;color:{{ $isUnit ? '#2d6a4f' : '#1a56a0' }}">{{ $um }}</td>
            <td style="text-align:center;font-weight:600">{{ $item->colli ?? '—' }}</td>
            <td style="text-align:right;font-family:'DM Mono',monospace">
                @if(!$isUnit) {{ number_format($item->kg_estimated ?? 0, 2, ',', '.') }} @else — @endif
            </td>
            <td style="text-align:right;font-family:'DM Mono',monospace">
                @if(!$isUnit) {{ $item->kg_real ? number_format($item->kg_real, 2, ',', '.') : '—' }} @else — @endif
            </td>
            <td style="text-align:right;font-family:'DM Mono',monospace">
                @if(!$isUnit) {{ number_format($item->kg_net ?? 0, 2, ',', '.') }}
                @else {{ number_format($item->qty ?? 0, 0, ',', '.') }} pz @endif
            </td>
            <td style="text-align:right;font-family:'DM Mono',monospace">
                € {{ number_format($item->price_kg ?? $item->price ?? 0, 2, ',', '.') }}
                <span style="font-size:10px;color:var(--muted)">{{ $isUnit ? '/pz' : '/kg' }}</span>
            </td>
            <td style="text-align:right;font-weight:700;font-family:'DM Mono',monospace">
                € {{ number_format($item->total, 2, ',', '.') }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align:center;padding:24px;color:var(--muted)">Nessun prodotto in questo ordine</td>
        </tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" style="text-align:right;font-weight:700;padding:12px 8px;border-top:2px solid var(--border)">Totale ordine</td>
                <td style="text-align:right;font-weight:700;font-size:16px;font-family:'DM Mono',monospace;padding:12px 8px;border-top:2px solid var(--border)">
                    € {{ number_format($order->total, 2, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>

@if($order->notes)
<div class="card" style="margin-top:16px;font-size:13px">
    <strong>Note:</strong> {{ $order->notes }}
</div>
@endif

{{-- ── CSS STAMPA ORDINE SINGOLO ── --}}
<style>
#print-header-order { display: none; }

@media print {
    nav, aside, .page-header, .btn, button,
    #order-info-screen { display: none !important; }

    #print-header-order { display: block !important; }
    #order-items { border: none !important; overflow: visible !important; }

    body { font-size: 11px !important; color: #000 !important; }
    .card { box-shadow: none !important; border: none !important; margin: 0 !important; }
    table { width: 100% !important; border-collapse: collapse !important; font-size: 10px !important; }
    th, td { border: 1px solid #ccc !important; padding: 5px 7px !important; }
    thead { background: #e8f5ee !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    tfoot td { background: #f0faf4 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    @page { size: A4 portrait; margin: 14mm; }
}
</style>

@endsection