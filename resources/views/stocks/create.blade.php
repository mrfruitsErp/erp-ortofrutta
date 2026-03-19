@extends('layouts.app')

@section('page-title', 'Carico Merce')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">🚛 Carico Merce</div>
        <div class="page-sub">Carico singolo o massivo per tutti i prodotti</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ url('/magazzino') }}" class="btn btn-secondary">← Magazzino</a>
        <a href="{{ url('/movimenti-magazzino') }}" class="btn btn-secondary">🔄 Movimenti</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">

    {{-- CARICO SINGOLO --}}
    <div class="card">
        <div style="font-weight:700;font-size:14px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border)">
            📦 Carico Singolo
        </div>
        <form method="POST" action="{{ url('/stock') }}">
            @csrf
            <div class="form-group">
                <label>Prodotto</label>
                <select name="product_id" id="productSelect" required>
                    <option value="">— Seleziona prodotto —</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            data-stock="{{ $product->stock->quantity ?? 0 }}"
                            data-sale="{{ $product->sale_type }}">
                            {{ $product->name }}
                            ({{ number_format($product->stock->quantity ?? 0, 2, ',', '.') }} kg)
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="stockInfo" style="display:none;background:var(--green-xl);border:1px solid #b7e4c7;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:var(--green)">
                <strong>Stock attuale:</strong> <span id="currentStock">0</span> kg
            </div>

            <div id="unitNotice" style="display:none;background:#fff8e1;border:1px solid #ffe082;border-radius:8px;padding:8px 12px;margin-bottom:14px;font-size:12px;color:#b8860b">
                ℹ️ Prodotto venduto a pezzi — inserisci il peso in <strong>kg</strong>
            </div>

            <div class="form-group">
                <label>Quantità (kg)</label>
                <div style="display:flex;align-items:center;gap:10px">
                    <input type="number" step="0.001" min="0.001" name="qty" id="qtyInput"
                           required placeholder="0.000" style="margin:0">
                    <span style="font-size:13px;font-weight:600;color:var(--muted)">kg</span>
                </div>
            </div>

            <div id="stockAfter" style="display:none;background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px">
                <div style="display:flex;justify-content:space-between">
                    <span style="color:var(--muted)">Stock dopo:</span>
                    <span id="stockAfterVal" style="font-weight:700;color:var(--green)"></span>
                </div>
            </div>

            <div class="form-group">
                <label>Note (opzionale)</label>
                <input type="text" name="note" placeholder="Es: DDT fornitore n. 123...">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
                ✅ Registra Carico
            </button>
        </form>
    </div>

    {{-- ULTIMI CARICHI --}}
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:14px 18px;border-bottom:1px solid var(--border);font-weight:700;font-size:13px">
            Ultimi carichi registrati
        </div>
        <table style="font-size:12px">
            <thead>
                <tr>
                    <th>Prodotto</th>
                    <th style="text-align:right">Kg</th>
                    <th style="text-align:right">Data</th>
                </tr>
            </thead>
            <tbody>
            @php
                $ultimi = \App\Models\StockMovement::with('product')
                    ->where('type','IN')
                    ->orderBy('created_at','desc')
                    ->limit(12)
                    ->get();
            @endphp
            @forelse($ultimi as $mov)
                <tr>
                    <td style="font-weight:600">{{ $mov->product->name ?? '—' }}</td>
                    <td style="text-align:right;font-family:monospace;color:var(--green)">+{{ number_format($mov->qty,3,',','.') }}</td>
                    <td style="text-align:right;color:var(--muted)">{{ $mov->created_at->format('d/m H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align:center;padding:20px;color:var(--muted)">Nessun carico</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- CARICO MASSIVO --}}
<form method="POST" action="{{ url('/stock/bulk') }}">
@csrf

<div class="card" style="padding:0;overflow:hidden">

    <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;gap:12px;flex-wrap:wrap;align-items:center">
        <div style="font-weight:700;font-size:14px">📋 Carico Massivo</div>
        <input type="text" id="searchInput" placeholder="🔍 Cerca prodotto..."
               style="max-width:220px;margin:0" oninput="filterRows()">
        <select id="filterStato" style="max-width:200px;margin:0" onchange="filterRows()">
            <option value="">Tutti i prodotti</option>
            <option value="esaurito">⚠ Solo esauriti</option>
            <option value="sottoscorta">⚠ Solo sotto scorta</option>
            <option value="esaurito,sottoscorta">⚠ Esauriti + Sotto scorta</option>
        </select>
        <span id="countLabel" style="font-size:12px;color:var(--muted);margin-left:auto"></span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Prodotto</th>
                <th style="text-align:center;width:50px">Tipo</th>
                <th style="text-align:right;width:130px">Stock attuale</th>
                <th style="text-align:right;width:110px">Scorta min.</th>
                <th style="text-align:center;width:80px">Stato</th>
                <th style="text-align:center;width:150px">+ Quantità (kg)</th>
                <th style="text-align:right;width:130px;padding-right:16px">Stock dopo</th>
            </tr>
        </thead>
        <tbody id="bulkTableBody">
        @foreach($products as $product)
        @php
            $qty    = $product->stock->quantity ?? 0;
            $min    = $product->stock->min_stock ?? 0;
            $isUnit = $product->sale_type === 'unit';
            if ($qty <= 0)        $stato = 'esaurito';
            elseif ($qty <= $min) $stato = 'sottoscorta';
            else                  $stato = 'ok';
        @endphp
        <tr class="bulk-row"
            data-name="{{ strtolower($product->name) }}"
            data-stato="{{ $stato }}"
            data-current="{{ $qty }}">

            <td style="font-weight:700">{{ $product->name }}</td>

            <td style="text-align:center">
                @if($isUnit)
                    <span style="background:#e8f5e9;color:#2d6a4f;padding:2px 7px;border-radius:10px;font-size:11px;font-weight:700">PZ</span>
                @else
                    <span style="background:#e3f0ff;color:#1a56a0;padding:2px 7px;border-radius:10px;font-size:11px;font-weight:700">KG</span>
                @endif
            </td>

            <td style="text-align:right;font-family:monospace;font-weight:700">
                {{ number_format($qty, 3, ',', '.') }} kg
            </td>

            <td style="text-align:right;font-family:monospace;color:var(--muted)">
                {{ number_format($min, 3, ',', '.') }} kg
            </td>

            <td style="text-align:center">
                @if($stato == 'esaurito')
                    <span style="background:#fde8e8;color:#c0392b;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700">⚠ Esaurito</span>
                @elseif($stato == 'sottoscorta')
                    <span style="background:#fff3e0;color:#e65100;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700">⚠ Sotto</span>
                @else
                    <span style="background:var(--green-xl);color:var(--green);padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700">✓ OK</span>
                @endif
            </td>

            <td style="text-align:center">
                <input type="number"
                       name="qty[{{ $product->id }}]"
                       step="0.001" min="0"
                       placeholder="—"
                       style="width:110px;text-align:right;margin:0"
                       oninput="updatePreview(this, {{ $qty }})">
            </td>

            <td style="text-align:right;font-family:monospace;font-weight:700;padding-right:16px" class="preview-cell">
                <span style="color:var(--muted)">—</span>
            </td>

        </tr>
        @endforeach
        </tbody>
    </table>

    <div style="padding:16px 18px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
        <span style="font-size:13px;color:var(--muted)">
            Vengono salvati solo i prodotti con una quantità inserita.
        </span>
        <button type="submit" class="btn btn-primary" style="padding:10px 28px;font-size:14px">
            ✅ Salva carico massivo
        </button>
    </div>

</div>

</form>

<script>
// ── CARICO SINGOLO ───────────────────────────────────────
const productSelect = document.getElementById('productSelect');
const qtyInput      = document.getElementById('qtyInput');

function updateSingleInfo() {
    const opt = productSelect.selectedOptions[0];
    if (!opt || !opt.value) {
        document.getElementById('stockInfo').style.display  = 'none';
        document.getElementById('stockAfter').style.display = 'none';
        document.getElementById('unitNotice').style.display = 'none';
        return;
    }
    const stock  = parseFloat(opt.dataset.stock) || 0;
    const isUnit = opt.dataset.sale === 'unit';
    const qty    = parseFloat(qtyInput.value) || 0;

    document.getElementById('currentStock').textContent = stock.toFixed(3);
    document.getElementById('stockInfo').style.display  = 'block';
    document.getElementById('unitNotice').style.display = isUnit ? 'block' : 'none';

    if (qty > 0) {
        document.getElementById('stockAfterVal').textContent = (stock + qty).toFixed(3) + ' kg';
        document.getElementById('stockAfter').style.display  = 'block';
    } else {
        document.getElementById('stockAfter').style.display = 'none';
    }
}

productSelect.addEventListener('change', updateSingleInfo);
qtyInput.addEventListener('input', updateSingleInfo);

// ── CARICO MASSIVO ───────────────────────────────────────
function updatePreview(input, currentQty) {
    const cell = input.closest('tr').querySelector('.preview-cell');
    const val  = parseFloat(input.value);
    if (!isNaN(val) && val > 0) {
        const newQty = currentQty + val;
        cell.innerHTML = '<span style="color:var(--green)">' +
            newQty.toLocaleString('it-IT', {minimumFractionDigits:3, maximumFractionDigits:3}) +
            ' kg</span>';
    } else {
        cell.innerHTML = '<span style="color:var(--muted)">—</span>';
    }
}

function filterRows() {
    const q     = document.getElementById('searchInput').value.toLowerCase();
    const stato = document.getElementById('filterStato').value;
    const stati = stato ? stato.split(',') : [];
    let visible = 0;

    document.querySelectorAll('.bulk-row').forEach(row => {
        const match =
            (!q          || row.dataset.name.includes(q)) &&
            (!stati.length || stati.includes(row.dataset.stato));
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    document.getElementById('countLabel').textContent = visible + ' prodotti';
}

filterRows();
</script>

@endsection