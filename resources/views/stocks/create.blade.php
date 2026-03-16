@extends('layouts.app')

@section('page-title', 'Carico Merce')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Carico Merce</div>
        <div class="page-sub">Registra un ingresso merce in magazzino</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ url('/magazzino') }}" class="btn btn-secondary">← Magazzino</a>
        <a href="{{ url('/movimenti-magazzino') }}" class="btn btn-secondary">🔄 Movimenti</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start">

    {{-- FORM --}}
    <div class="card">

        <div style="font-weight:700;font-size:14px;color:var(--dark);margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border)">
            📦 Dati Carico
        </div>

        <form method="POST" action="{{ url('/stock') }}">
            @csrf

            <div class="form-group">
                <label>Prodotto</label>
                <select name="product_id" id="productSelect" required>
                    <option value="">— Seleziona prodotto —</option>
                    @foreach($products as $product)
                        <option
                            value="{{ $product->id }}"
                            data-unit="{{ $product->unit }}"
                            data-stock="{{ $product->stock ?? 0 }}"
                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <div style="color:#c0392b;font-size:12px;margin-top:4px">{{ $message }}</div>
                @enderror
            </div>

            {{-- INFO STOCK ATTUALE --}}
            <div id="stockInfo" style="display:none;background:var(--green-xl);border:1px solid #b7e4c7;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:var(--green)">
                <strong>Stock attuale:</strong> <span id="currentStock">0</span> <span id="currentUnit"></span>
            </div>

            <div class="form-group">
                <label>Quantità da caricare</label>
                <div style="display:flex;align-items:center;gap:10px">
                    <input type="number" step="0.01" min="0.01" name="qty" id="qtyInput"
                           value="{{ old('qty') }}" required placeholder="0.00"
                           style="margin:0">
                    <span id="unitLabel" style="font-size:13px;font-weight:600;color:var(--muted);white-space:nowrap;min-width:40px"></span>
                </div>
                @error('qty')
                    <div style="color:#c0392b;font-size:12px;margin-top:4px">{{ $message }}</div>
                @enderror
            </div>

            {{-- ANTEPRIMA STOCK DOPO --}}
            <div id="stockAfter" style="display:none;background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="color:var(--muted)">Stock dopo il carico:</span>
                    <span id="stockAfterVal" style="font-weight:700;font-family:'DM Mono',monospace;font-size:16px;color:var(--green)"></span>
                </div>
            </div>

            <div class="form-group">
                <label>Note (opzionale)</label>
                <input type="text" name="note" value="{{ old('note') }}" placeholder="Es: Fornitura del mattino, DDT fornitore n. 123...">
            </div>

            <div style="display:flex;gap:10px;margin-top:24px">
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">
                    ✅ Registra Carico
                </button>
                <a href="{{ url('/magazzino') }}" class="btn btn-secondary">Annulla</a>
            </div>

        </form>

    </div>

    {{-- PANNELLO LATERALE: ultimi movimenti --}}
    <div>

        <div class="card" style="padding:0;overflow:hidden">
            <div style="padding:14px 18px;border-bottom:1px solid var(--border);font-weight:700;font-size:13px;color:var(--dark)">
                Ultimi carichi registrati
            </div>
            <table style="font-size:12px">
                <thead>
                    <tr>
                        <th>Prodotto</th>
                        <th style="text-align:right">Qty</th>
                        <th style="text-align:right">Data</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $ultimi = \App\Models\StockMovement::with('product')
                        ->where('type','IN')
                        ->orderBy('created_at','desc')
                        ->limit(10)
                        ->get();
                @endphp
                @forelse($ultimi as $mov)
                    <tr>
                        <td style="font-weight:600">{{ $mov->product->name ?? '—' }}</td>
                        <td style="text-align:right;font-family:'DM Mono',monospace;color:var(--green)">+{{ $mov->qty }}</td>
                        <td style="text-align:right;color:var(--muted)">{{ $mov->created_at->format('d/m') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center;padding:20px;color:var(--muted)">Nessun carico</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

<script>
const productSelect = document.getElementById('productSelect');
const qtyInput      = document.getElementById('qtyInput');
const stockInfo     = document.getElementById('stockInfo');
const currentStock  = document.getElementById('currentStock');
const currentUnit   = document.getElementById('currentUnit');
const unitLabel     = document.getElementById('unitLabel');
const stockAfter    = document.getElementById('stockAfter');
const stockAfterVal = document.getElementById('stockAfterVal');

function updateInfo() {
    const opt = productSelect.selectedOptions[0];
    if (!opt || !opt.value) {
        stockInfo.style.display = 'none';
        stockAfter.style.display = 'none';
        unitLabel.textContent = '';
        return;
    }

    const unit  = opt.dataset.unit || '';
    const stock = parseFloat(opt.dataset.stock) || 0;
    const qty   = parseFloat(qtyInput.value) || 0;

    unitLabel.textContent = unit;
    currentStock.textContent = stock.toFixed(2);
    currentUnit.textContent  = unit;
    stockInfo.style.display  = 'block';

    if (qty > 0) {
        stockAfterVal.textContent = (stock + qty).toFixed(2) + ' ' + unit;
        stockAfter.style.display  = 'block';
    } else {
        stockAfter.style.display  = 'none';
    }
}

productSelect.addEventListener('change', updateInfo);
qtyInput.addEventListener('input', updateInfo);
</script>

@endsection