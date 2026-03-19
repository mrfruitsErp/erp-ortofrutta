@extends('layouts.app')
@section('page-title', 'Magazzino')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">📦 Magazzino</div>
        <div class="page-sub">Situazione giacenze prodotti</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ url('/carico-magazzino') }}" class="btn btn-primary">🚛 Carico Merce</a>
        <a href="{{ url('/movimenti-magazzino') }}" class="btn btn-secondary">🔄 Movimenti</a>
    </div>
</div>

<div class="card" style="padding:0;overflow:hidden">

    {{-- FILTRI --}}
    <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;gap:12px;flex-wrap:wrap;align-items:center">
        <input type="text" id="searchInput" placeholder="🔍 Cerca prodotto..." style="max-width:240px;margin:0">
        <select id="filterStato" style="max-width:160px;margin:0">
            <option value="">Tutti gli stati</option>
            <option value="ok">✓ OK</option>
            <option value="sottoscorta">⚠ Sotto scorta</option>
            <option value="esaurito">⚠ Esaurito</option>
        </select>
        <select id="filterTipo" style="max-width:140px;margin:0">
            <option value="">Tutti i tipi</option>
            <option value="kg">Vendita a Kg</option>
            <option value="unit">Vendita a Pezzi</option>
        </select>
        <button onclick="resetFiltri()" class="btn btn-secondary" style="padding:7px 14px;font-size:13px">✕ Reset</button>
        <span id="countLabel" style="font-size:12px;color:var(--muted);margin-left:auto"></span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="cursor:pointer" onclick="sortTable(0)">Prodotto ↕</th>
                <th style="text-align:center">Tipo</th>
                <th style="text-align:right;cursor:pointer" onclick="sortTable(2)">Stock (kg) ↕</th>
                <th style="text-align:right">Pezzi stimati</th>
                <th style="text-align:right;cursor:pointer" onclick="sortTable(4)">Scorta Min ↕</th>
                <th style="text-align:right;cursor:pointer" onclick="sortTable(5)">Costo €/kg ↕</th>
                <th style="text-align:center">Stato</th>
                <th style="text-align:center;width:100px">Azioni</th>
            </tr>
        </thead>
        <tbody id="stockTableBody">
        @forelse($products as $product)
        @php
            $qty     = $product->stock->quantity ?? 0;
            $min     = $product->stock->min_stock ?? 0;
            $isUnit  = $product->sale_type === 'unit';
            $weight  = (float)($product->avg_box_weight ?? 0);
            $pieces  = (int)($product->pieces_per_box ?? 0);

            // Pezzi stimati per prodotti a pezzo
            $pezziStimati = null;
            if($isUnit && $weight > 0 && $pieces > 0){
                $casseStime   = $qty / $weight;
                $pezziStimati = floor($casseStime * $pieces);
            }

            if ($qty <= 0) $stato = 'esaurito';
            elseif ($qty <= $min) $stato = 'sottoscorta';
            else $stato = 'ok';
        @endphp
        <tr class="stock-row"
            data-name="{{ strtolower($product->name) }}"
            data-stato="{{ $stato }}"
            data-tipo="{{ $product->sale_type ?? 'kg' }}">

            <td style="font-weight:700">{{ $product->name }}</td>

            <td style="text-align:center">
                @if($isUnit)
                    <span style="background:#e8f5e9;color:#2d6a4f;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:700">PZ</span>
                @else
                    <span style="background:#e3f0ff;color:#1a56a0;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:700">KG</span>
                @endif
            </td>

            <td style="text-align:right;font-family:'DM Mono',monospace;font-weight:700;font-size:15px" data-val="{{ $qty }}">
                {{ number_format($qty, 3, ',', '.') }}
            </td>

            <td style="text-align:right;font-family:'DM Mono',monospace;color:var(--muted);font-size:13px">
                @if($pezziStimati !== null)
                    ≈ {{ number_format($pezziStimati, 0, ',', '.') }} pz
                @else
                    —
                @endif
            </td>

            <td style="text-align:right;font-family:'DM Mono',monospace;color:var(--muted)" data-val="{{ $min }}">
                {{ number_format($min, 3, ',', '.') }}
            </td>

            <td style="text-align:right;font-family:'DM Mono',monospace" data-val="{{ $product->cost_price ?? 0 }}">
                € {{ number_format($product->cost_price ?? 0, 2, ',', '.') }}
            </td>

            <td style="text-align:center">
                @if($stato == 'esaurito')
                    <span style="background:#fde8e8;color:#c0392b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">⚠ Esaurito</span>
                @elseif($stato == 'sottoscorta')
                    <span style="background:#fff3e0;color:#e65100;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">⚠ Sotto scorta</span>
                @else
                    <span style="background:var(--green-xl);color:var(--green);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">✓ OK</span>
                @endif
            </td>

            <td style="text-align:center">
                <a href="{{ url('/products/' . $product->id . '/edit') }}" class="btn btn-secondary" style="padding:5px 12px;font-size:12px">✏️ Modifica</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center;padding:40px;color:var(--muted)">Nessun prodotto in magazzino</td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

<script>
let sortDir = {};

function filterRows() {
    const q     = document.getElementById('searchInput').value.toLowerCase();
    const stato = document.getElementById('filterStato').value;
    const tipo  = document.getElementById('filterTipo').value;
    let visible = 0;

    document.querySelectorAll('.stock-row').forEach(row => {
        const match =
            (!q     || row.dataset.name.includes(q)) &&
            (!stato || row.dataset.stato === stato) &&
            (!tipo  || row.dataset.tipo === tipo);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    document.getElementById('countLabel').textContent = visible + ' prodotti';
}

function resetFiltri() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterStato').value = '';
    document.getElementById('filterTipo').value  = '';
    filterRows();
}

function sortTable(colIdx) {
    const tbody = document.getElementById('stockTableBody');
    const rows  = Array.from(tbody.querySelectorAll('.stock-row'));
    sortDir[colIdx] = !sortDir[colIdx];

    rows.sort((a, b) => {
        const aCell = a.cells[colIdx];
        const bCell = b.cells[colIdx];
        const aVal  = aCell.dataset.val !== undefined ? parseFloat(aCell.dataset.val) : aCell.textContent.trim().toLowerCase();
        const bVal  = bCell.dataset.val !== undefined ? parseFloat(bCell.dataset.val) : bCell.textContent.trim().toLowerCase();

        if (!isNaN(aVal) && !isNaN(bVal)) return sortDir[colIdx] ? aVal - bVal : bVal - aVal;
        return sortDir[colIdx] ? String(aVal).localeCompare(String(bVal)) : String(bVal).localeCompare(String(aVal));
    });

    rows.forEach(r => tbody.appendChild(r));
}

document.getElementById('searchInput').addEventListener('input', filterRows);
document.getElementById('filterStato').addEventListener('change', filterRows);
document.getElementById('filterTipo').addEventListener('change', filterRows);

filterRows();
</script>

@endsection