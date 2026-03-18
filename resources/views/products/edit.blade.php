@extends('layouts.app')

@section('page-title', 'Modifica Prodotto')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">✏️ Modifica Prodotto</div>
        <div class="page-sub">{{ $product->name }}</div>
    </div>
    <a href="{{ url('/products') }}" class="btn btn-secondary">← Torna ai prodotti</a>
</div>

<form method="POST" action="{{ url('/products/' . $product->id) }}">
@csrf
@method('PUT')

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

    {{-- COLONNA SINISTRA --}}
    <div class="card">

        <div style="font-weight:700;margin-bottom:16px">Dati Prodotto</div>

        <div class="form-group">
            <label>Nome Prodotto</label>
            <input type="text" name="name" value="{{ $product->name }}" required>
        </div>

        <div class="form-group">
            <label>Origine</label>
            <input type="text" name="origin" value="{{ $product->origin }}" style="width:100px">
        </div>

        <div class="form-group">
            <label>Unità di Misura</label>
            <select name="unit">
                <option value="kg" {{ $product->unit == 'kg' ? 'selected' : '' }}>KG</option>
                <option value="pz" {{ $product->unit == 'pz' ? 'selected' : '' }}>Pezzi</option>
            </select>
        </div>

        <div class="form-group">
            <label>Tipo Vendita</label>
            <select name="sale_type" id="sale_type" onchange="updateLabels()">
                <option value="kg"   {{ $product->sale_type == 'kg'   ? 'selected' : '' }}>A Kg</option>
                <option value="unit" {{ $product->sale_type == 'unit' ? 'selected' : '' }}>A Pezzi / Mazzi</option>
            </select>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
                <label>Peso Medio Cassa (kg)</label>
                <input type="number" step="0.001" name="avg_box_weight"
                       id="avg_box_weight"
                       value="{{ $product->avg_box_weight }}"
                       oninput="calcUnitWeight()">
            </div>
            <div class="form-group">
                <label>Tara per Cassa (kg)</label>
                <input type="number" step="0.001" name="tara"
                       value="{{ $product->tara ?? 0 }}">
            </div>
        </div>

        <div class="form-group" id="pieces-field">
            <label id="pieces-label">Pezzi / Mazzi per Cassa</label>
            <input type="number" step="1" name="pieces_per_box"
                   id="pieces_per_box"
                   value="{{ $product->pieces_per_box }}"
                   placeholder="es. 10"
                   oninput="calcUnitWeight()">
        </div>

        {{-- Peso unitario calcolato (solo per pezzi) --}}
        <div id="unit-weight-box" style="background:#f0faf4;border:1px solid #c3e6cb;border-radius:6px;padding:10px 14px;margin-bottom:12px;display:none">
            <div style="font-size:10px;color:#2d6a4f;font-weight:700;text-transform:uppercase;margin-bottom:4px">
                Peso per pezzo / mazzo
            </div>
            <div style="font-size:18px;font-weight:700;color:#2d6a4f">
                <span id="unit-weight-display">—</span> kg
            </div>
        </div>

        <div style="border-top:1px solid var(--border);margin:16px 0"></div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">

            <div class="form-group">
                <label id="price-label">Prezzo Vendita</label>
                <input type="number" step="0.01" name="price"
                       id="price_field"
                       value="{{ $product->price }}"
                       oninput="calcUnitPrice()">
                <div style="font-size:10px;color:#999;margin-top:3px" id="price-unit-label">
                    {{ ($product->sale_type == 'unit') ? '€/pz' : '€/kg' }}
                </div>
            </div>

            <div class="form-group">
                <label>Prezzo Costo</label>
                <input type="number" step="0.01" name="cost_price"
                       value="{{ $product->cost_price }}">
                <div style="font-size:10px;color:#999;margin-top:3px" id="cost-unit-label">
                    {{ ($product->sale_type == 'unit') ? '€/pz' : '€/kg' }}
                </div>
            </div>

            <div class="form-group">
                <label>Aliquota IVA</label>
                <select name="vat_rate">
                    <option value="4"  {{ ($product->vat_rate ?? 4)  == 4  ? 'selected' : '' }}>4%</option>
                    <option value="5"  {{ ($product->vat_rate ?? 4)  == 5  ? 'selected' : '' }}>5%</option>
                    <option value="10" {{ ($product->vat_rate ?? 4)  == 10 ? 'selected' : '' }}>10%</option>
                    <option value="22" {{ ($product->vat_rate ?? 4)  == 22 ? 'selected' : '' }}>22%</option>
                </select>
            </div>

        </div>

        {{-- Prezzo al mazzo/pezzo calcolato (visibile solo per pezzi con prezzo al kg) --}}
        <div id="unit-price-box" style="background:#fff8e1;border:1px solid #ffe082;border-radius:6px;padding:10px 14px;display:none">
            <div style="font-size:10px;color:#b8860b;font-weight:700;text-transform:uppercase;margin-bottom:4px">
                Prezzo per pezzo / mazzo (calcolato)
            </div>
            <div style="font-size:18px;font-weight:700;color:#b8860b">
                € <span id="unit-price-display">—</span>
            </div>
            <div style="font-size:10px;color:#999;margin-top:2px">
                = prezzo_kg × peso_pezzo
            </div>
        </div>

    </div>

    {{-- COLONNA DESTRA: STOCK --}}
    <div class="card">

        <div style="font-weight:700;margin-bottom:16px">Gestione Stock</div>

        <div style="margin-bottom:16px">
            <div style="font-size:11px;color:#999;text-transform:uppercase;font-weight:600;margin-bottom:4px">
                Stock attuale
            </div>
            <div style="font-size:22px;font-weight:700">
                {{ number_format($stock->quantity ?? 0, 3, ',', '.') }}
                <span style="font-size:14px;color:#999">{{ $product->unit }}</span>
            </div>
        </div>

        <div class="form-group">
            <label>Nuova quantità</label>
            <input type="number" step="0.001" name="new_stock_qty"
                   placeholder="Lascia vuoto per non modificare">
        </div>

        <div class="form-group">
            <label>Scorta minima</label>
            <input type="number" step="0.001" name="min_stock"
                   value="{{ $stock->min_stock ?? 0 }}">
        </div>

    </div>

</div>

<div style="margin-top:20px;display:flex;gap:10px">
    <button type="submit" class="btn btn-primary">💾 Salva</button>
    <a href="{{ url('/products') }}" class="btn btn-secondary">Annulla</a>
</div>

</form>

<script>

function updateLabels(){
    const sale = document.getElementById('sale_type').value;
    const isUnit = sale === 'unit';

    document.getElementById('price-unit-label').textContent = isUnit ? '€/pz' : '€/kg';
    document.getElementById('cost-unit-label').textContent  = isUnit ? '€/pz' : '€/kg';

    // Mostra/nascondi box peso unitario e prezzo calcolato
    document.getElementById('unit-weight-box').style.display = isUnit ? '' : 'none';
    document.getElementById('unit-price-box').style.display  = isUnit ? '' : 'none';

    calcUnitWeight();
}

function calcUnitWeight(){
    const weight = parseFloat(document.getElementById('avg_box_weight').value) || 0;
    const pieces = parseFloat(document.getElementById('pieces_per_box').value) || 0;

    if(weight > 0 && pieces > 0){
        const unitW = weight / pieces;
        document.getElementById('unit-weight-display').textContent =
            unitW.toLocaleString('it-IT', {minimumFractionDigits:3, maximumFractionDigits:3});
    } else {
        document.getElementById('unit-weight-display').textContent = '—';
    }

    calcUnitPrice();
}

function calcUnitPrice(){
    const weight = parseFloat(document.getElementById('avg_box_weight').value) || 0;
    const pieces = parseFloat(document.getElementById('pieces_per_box').value) || 0;
    const price  = parseFloat(document.getElementById('price_field').value)    || 0;

    if(weight > 0 && pieces > 0 && price > 0){
        const unitW     = weight / pieces;
        const unitPrice = unitW * price;
        document.getElementById('unit-price-display').textContent =
            unitPrice.toLocaleString('it-IT', {minimumFractionDigits:2, maximumFractionDigits:2});
    } else {
        document.getElementById('unit-price-display').textContent = '—';
    }
}

// Init al caricamento
window.addEventListener('load', function(){
    updateLabels();
});

</script>

@endsection