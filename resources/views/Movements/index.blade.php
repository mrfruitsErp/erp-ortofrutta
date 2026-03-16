@extends('layouts.app')

@section('page-title', 'Movimenti Magazzino')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Movimenti Magazzino</div>
        <div class="page-sub">Storico completo entrate e uscite</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ url('/magazzino') }}" class="btn btn-secondary">← Magazzino</a>
        <a href="{{ url('/carico-magazzino') }}" class="btn btn-primary">＋ Carico Merce</a>
    </div>
</div>

{{-- STATS --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">

    <div class="card" style="padding:16px 20px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Totale Movimenti</div>
        <div style="font-size:26px;font-weight:700;color:var(--dark)">{{ $movements->count() }}</div>
    </div>

    <div class="card" style="padding:16px 20px;border-left:3px solid var(--green-l)">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">↓ Carichi (IN)</div>
        <div style="font-size:26px;font-weight:700;color:var(--green)">{{ $totaleCarichi }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">unità totali in ingresso</div>
    </div>

    <div class="card" style="padding:16px 20px;border-left:3px solid #c0392b">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">↑ Uscite (OUT)</div>
        <div style="font-size:26px;font-weight:700;color:#c0392b">{{ $totaleUscite }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">unità totali in uscita</div>
    </div>

    <div class="card" style="padding:16px 20px;border-left:3px solid var(--accent)">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Movimenti Oggi</div>
        <div style="font-size:26px;font-weight:700;color:var(--dark)">{{ $totaleOggi }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">{{ now()->format('d/m/Y') }}</div>
    </div>

</div>

{{-- TABELLA --}}
<div class="card" style="padding:0;overflow:hidden">

    {{-- FILTRI --}}
    <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <input type="text" id="searchInput" placeholder="🔍 Cerca prodotto o documento..." style="max-width:280px;margin:0">
        <select id="filterType" style="max-width:160px;margin:0">
            <option value="">Tutti i movimenti</option>
            <option value="IN">↓ Carichi (IN)</option>
            <option value="OUT">↑ Uscite (OUT)</option>
        </select>
        <select id="filterDate" style="max-width:160px;margin:0">
            <option value="">Tutte le date</option>
            <option value="oggi">Solo oggi</option>
            <option value="settimana">Questa settimana</option>
            <option value="mese">Questo mese</option>
        </select>
        <span id="countLabel" style="font-size:12px;color:var(--muted);margin-left:auto"></span>
    </div>

    <table id="movTable">
        <thead>
            <tr>
                <th style="width:90px">Data</th>
                <th style="width:60px">Ora</th>
                <th>Prodotto</th>
                <th style="width:110px;text-align:center">Tipo</th>
                <th style="width:100px;text-align:right">Quantità</th>
                <th>Documento</th>
            </tr>
        </thead>
        <tbody>
        @forelse($movements as $movement)
            <tr class="mov-row"
                data-prodotto="{{ strtolower($movement->product->name ?? '') }}"
                data-tipo="{{ $movement->type }}"
                data-doc="{{ strtolower($movement->document->number ?? '') }}"
                data-date="{{ $movement->movement_date ?? $movement->created_at->toDateString() }}">

                <td style="font-size:12px;color:var(--muted);font-family:'DM Mono',monospace">
                    {{ $movement->created_at->format('d/m/Y') }}
                </td>

                <td style="font-size:12px;color:var(--muted);font-family:'DM Mono',monospace">
                    {{ $movement->created_at->format('H:i') }}
                </td>

                <td style="font-weight:600">
                    {{ $movement->product->name ?? '—' }}
                </td>

                <td style="text-align:center">
                    @if($movement->type == 'OUT')
                        <span style="background:#fde8e8;color:#c0392b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">↑ USCITA</span>
                    @elseif($movement->type == 'IN')
                        <span style="background:var(--green-xl);color:var(--green);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">↓ CARICO</span>
                    @else
                        <span style="background:var(--bg);color:var(--muted);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">{{ $movement->type }}</span>
                    @endif
                </td>

                <td style="text-align:right;font-family:'DM Mono',monospace;font-weight:600;
                    color:{{ $movement->type == 'IN' ? 'var(--green)' : '#c0392b' }}">
                    {{ $movement->type == 'OUT' ? '-' : '+' }}{{ $movement->qty }}
                    <span style="font-size:11px;font-weight:400;color:var(--muted)">
                        {{ $movement->product->unit ?? '' }}
                    </span>
                </td>

                <td style="color:var(--muted);font-size:13px">
                    @if($movement->document)
                        <a href="{{ url('/documents/'.$movement->document->id) }}"
                           style="color:var(--green);text-decoration:none;font-family:'DM Mono',monospace;font-size:12px">
                            {{ $movement->document->number }}
                        </a>
                    @else
                        <span style="color:var(--border)">—</span>
                    @endif
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:48px;color:var(--muted)">
                    Nessun movimento registrato.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

</div>

<script>
const searchInput = document.getElementById('searchInput');
const filterType  = document.getElementById('filterType');
const filterDate  = document.getElementById('filterDate');
const countLabel  = document.getElementById('countLabel');
const rows        = document.querySelectorAll('.mov-row');

const today = new Date().toISOString().split('T')[0];
const weekAgo = new Date(Date.now() - 7 * 86400000).toISOString().split('T')[0];
const monthAgo = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];

function filterRows() {
    const q  = searchInput.value.toLowerCase();
    const t  = filterType.value;
    const d  = filterDate.value;
    let visible = 0;

    rows.forEach(row => {
        const matchQ = !q || row.dataset.prodotto.includes(q) || row.dataset.doc.includes(q);
        const matchT = !t || row.dataset.tipo === t;
        let matchD = true;
        if (d === 'oggi')      matchD = row.dataset.date === today;
        if (d === 'settimana') matchD = row.dataset.date >= weekAgo;
        if (d === 'mese')      matchD = row.dataset.date >= monthAgo;

        const show = matchQ && matchT && matchD;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    countLabel.textContent = visible + ' movimenti';
}

searchInput.addEventListener('input', filterRows);
filterType.addEventListener('change', filterRows);
filterDate.addEventListener('change', filterRows);

filterRows(); // inizializza contatore
</script>

@endsection