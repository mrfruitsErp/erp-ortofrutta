@extends('layouts.app')
@section('page-title', 'Pagamenti')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">💶 Pagamenti</div>
        <div class="page-sub">Storico incassi e crediti aperti</div>
    </div>
    <a href="{{ url('/crediti') }}" class="btn btn-secondary">📋 Crediti aperti</a>
</div>

{{-- STATS --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
    <div class="card" style="padding:14px 18px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:4px">Totale Incassato</div>
        <div style="font-size:24px;font-weight:700;color:var(--green)">€ {{ number_format($payments->sum('amount'),2,',','.') }}</div>
    </div>
    <div class="card" style="padding:14px 18px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:4px">N. Pagamenti</div>
        <div style="font-size:24px;font-weight:700;color:var(--dark)">{{ $payments->count() }}</div>
    </div>
    <div class="card" style="padding:14px 18px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:4px">Questo Mese</div>
        <div style="font-size:24px;font-weight:700;color:var(--dark)">€ {{ number_format($payments->filter(fn($p) => \Carbon\Carbon::parse($p->payment_date)->isCurrentMonth())->sum('amount'),2,',','.') }}</div>
    </div>
    <div class="card" style="padding:14px 18px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:4px">Clienti Distinti</div>
        <div style="font-size:24px;font-weight:700;color:var(--dark)">{{ $payments->pluck('document.client.company_name')->filter()->unique()->count() }}</div>
    </div>
</div>

{{-- FILTRI --}}
<div class="card" style="padding:10px 14px;margin-bottom:14px;display:flex;gap:8px;flex-wrap:wrap;align-items:center">
    <input type="text" id="fSearch" placeholder="🔍 Cliente o documento..." style="max-width:220px;margin:0">
    <select id="fMetodo" style="max-width:160px;margin:0">
        <option value="">Tutti i metodi</option>
        @foreach($payments->pluck('method')->unique()->filter() as $m)
            <option value="{{ strtolower($m) }}">{{ ucfirst($m) }}</option>
        @endforeach
    </select>
    <select id="fMese" style="max-width:160px;margin:0">
        <option value="">Tutti i mesi</option>
        @foreach($payments->pluck('payment_date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m'))->unique()->sort()->reverse() as $m)
            <option value="{{ $m }}">{{ \Carbon\Carbon::parse($m)->translatedFormat('F Y') }}</option>
        @endforeach
    </select>
    <button onclick="resetFiltri()" class="btn btn-secondary" style="padding:6px 12px;font-size:12px">✕ Reset</button>
    <span id="countLabel" style="font-size:12px;color:var(--muted);margin-left:auto"></span>
</div>

{{-- TABELLA --}}
<div class="card" style="padding:0;overflow:hidden">
<table id="paymentsTable" style="width:100%">
    <thead>
        <tr>
            <th style="width:50px">ID</th>
            <th>Cliente</th>
            <th style="width:130px">Documento</th>
            <th style="width:100px;text-align:right">Importo</th>
            <th style="width:120px;text-align:center">Metodo</th>
            <th style="width:100px;text-align:center">Data</th>
        </tr>
    </thead>
    <tbody>
    @forelse($payments as $payment)
    @php
        $doc     = $payment->document;
        $client  = $doc?->client;
        $metodo  = strtolower($payment->method ?? '');
        $metodoBg = match($metodo) {
            'bonifico'  => ['bg'=>'#e3f0ff','color'=>'#1a56a0','label'=>'Bonifico'],
            'contanti'  => ['bg'=>'#f0faf4','color'=>'#2d6a4f','label'=>'Contanti'],
            'assegno'   => ['bg'=>'#fff3e0','color'=>'#e65100','label'=>'Assegno'],
            'rid','sdd' => ['bg'=>'#fce7f3','color'=>'#9d174d','label'=>strtoupper($metodo)],
            default     => ['bg'=>'#f3f4f6','color'=>'#555','label'=>ucfirst($payment->method ?? '—')],
        };
    @endphp
    <tr class="pay-row"
        data-cliente="{{ strtolower($client?->company_name ?? '') }}"
        data-doc="{{ strtolower($doc?->number ?? '') }}"
        data-metodo="{{ $metodo }}"
        data-mese="{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m') }}">

        <td style="font-size:11px;color:var(--muted);font-family:'DM Mono',monospace">{{ $payment->id }}</td>

        {{-- CLIENTE --}}
        <td>
            @if($client)
                <div style="font-weight:600;font-size:13px">{{ $client->company_name }}</div>
                <div style="font-size:11px;color:var(--muted)">
                    {{ $client->city ?? '' }}
                    @if($client->phone) · {{ $client->phone }} @endif
                </div>
            @else
                <span style="color:var(--muted);font-size:12px">—</span>
            @endif
        </td>

        {{-- DOCUMENTO --}}
        <td>
            @if($doc)
                <a href="{{ url('/documents/'.$doc->id) }}"
                   style="font-family:'DM Mono',monospace;font-size:12px;color:var(--green);text-decoration:none;font-weight:600">
                    {{ $doc->number }}
                </a>
                <div style="font-size:10px;color:var(--muted)">
                    {{ \Carbon\Carbon::parse($doc->date)->format('d/m/Y') }}
                </div>
            @else
                <span style="font-size:12px;color:var(--muted)">Doc #{{ $payment->document_id }}</span>
            @endif
        </td>

        {{-- IMPORTO --}}
        <td style="text-align:right;font-family:'DM Mono',monospace;font-size:13px;font-weight:700;
            color:{{ $payment->amount >= 0 ? 'var(--green)' : '#c0392b' }}">
            € {{ number_format($payment->amount, 2, ',', '.') }}
        </td>

        {{-- METODO --}}
        <td style="text-align:center">
            <span style="background:{{ $metodoBg['bg'] }};color:{{ $metodoBg['color'] }};
                padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600">
                {{ $metodoBg['label'] }}
            </span>
        </td>

        {{-- DATA --}}
        <td style="text-align:center;font-size:12px;color:var(--muted);font-family:'DM Mono',monospace">
            {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
        </td>

    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center;padding:48px;color:var(--muted)">Nessun pagamento registrato.</td></tr>
    @endforelse
    </tbody>
</table>
</div>

<script>
function filterRows() {
    const q  = document.getElementById('fSearch').value.toLowerCase();
    const m  = document.getElementById('fMetodo').value;
    const ms = document.getElementById('fMese').value;
    let n = 0;
    document.querySelectorAll('.pay-row').forEach(r => {
        const ok = (!q  || r.dataset.cliente.includes(q) || r.dataset.doc.includes(q))
                && (!m  || r.dataset.metodo === m)
                && (!ms || r.dataset.mese === ms);
        r.style.display = ok ? '' : 'none';
        if (ok) n++;
    });
    document.getElementById('countLabel').textContent = n + ' pagamenti';
}
function resetFiltri() {
    ['fSearch','fMetodo','fMese'].forEach(id => document.getElementById(id).value = '');
    filterRows();
}
document.getElementById('fSearch').addEventListener('input', filterRows);
['fMetodo','fMese'].forEach(id => document.getElementById(id).addEventListener('change', filterRows));
filterRows();
</script>

@endsection