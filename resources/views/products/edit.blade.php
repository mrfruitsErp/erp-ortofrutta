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

    {{-- COLONNA SINISTRA: DATI PRODOTTO --}}
    <div class="card">

        <div style="font-weight:700;font-size:14px;color:var(--dark);margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border)">
            📋 Dati Prodotto
        </div>

        <div class="form-group">
            <label>Nome Prodotto</label>
            <input type="text" name="name" value="{{ $product->name }}" required>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">

            <div class="form-group">
                <label>Origine</label>
                <input type="text" name="origin" value="{{ $product->origin ?? '' }}" placeholder="Es. IT">
            </div>

            <div class="form-group">
                <label>Unità di Misura</label>
                <select name="unit">

                    <option value="kg" {{ $product->unit == 'kg' ? 'selected' : '' }}>kg</option>

                    <option value="cassa" {{ $product->unit == 'cassa' ? 'selected' : '' }}>Cassa</option>

                    <option value="pz" {{ $product->unit == 'pz' ? 'selected' : '' }}>Pezzi</option>

                </select>
            </div>

        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">

            <div class="form-group">
                <label>Tara Unitaria (kg)</label>
                <input type="number" step="0.001" name="tara" value="{{ $product->tara ?? 0 }}">
            </div>

            <div class="form-group">
                <label>Peso Medio Cassa (kg)</label>
                <input type="number" step="0.001" name="avg_box_weight" value="{{ $product->avg_box_weight ?? 0 }}">
            </div>

        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">

            <div class="form-group">
                <label>Prezzo Vendita €/kg</label>
                <input type="number" step="0.01" name="price" value="{{ $product->price ?? 0 }}" id="price_input" oninput="calcMargine()">
            </div>

            <div class="form-group">
                <label>Prezzo Costo €/kg</label>
                <input type="number" step="0.01" name="cost_price" value="{{ $product->cost_price ?? 0 }}" id="cost_input" oninput="calcMargine()">
            </div>

        </div>

        {{-- ALIQUOTA IVA --}}
        <div class="form-group">

            <label>Aliquota IVA</label>

            <select name="vat_rate">

                <option value="4" {{ ($product->vat_rate ?? 4) == 4 ? 'selected' : '' }}>IVA 4%</option>

                <option value="5" {{ ($product->vat_rate ?? 4) == 5 ? 'selected' : '' }}>IVA 5%</option>

                <option value="10" {{ ($product->vat_rate ?? 4) == 10 ? 'selected' : '' }}>IVA 10%</option>

                <option value="22" {{ ($product->vat_rate ?? 4) == 22 ? 'selected' : '' }}>IVA 22%</option>

            </select>

        </div>

        {{-- MARGINE LIVE --}}
        <div style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:12px 14px;margin-bottom:16px">

            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:4px">

                Margine calcolato

            </div>

            <div id="margine_display" style="font-size:20px;font-weight:700;font-family:'DM Mono',monospace;color:var(--green)">

                {{ $product->price > 0 ? number_format((($product->price - $product->cost_price) / $product->price) * 100, 1, ',', '.') : '0,0' }}%

            </div>

        </div>

    </div>

    {{-- COLONNA DESTRA: STOCK --}}
    <div class="card">

        <div style="font-weight:700;font-size:14px;color:var(--dark);margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border)">
            📦 Gestione Stock
        </div>

        {{-- STOCK ATTUALE --}}
        <div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:14px 16px;margin-bottom:20px">

            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:4px">

                Stock attuale

            </div>

            <div style="font-size:26px;font-weight:700;font-family:'DM Mono',monospace;color:var(--dark)">

                {{ number_format($stock->quantity ?? 0, 3, ',', '.') }}

                <span style="font-size:14px;color:var(--muted)">{{ $product->unit ?? 'kg' }}</span>

            </div>

        </div>

        <div class="form-group">

            <label>Imposta nuova quantità</label>

            <input type="number" step="0.001" min="0" name="new_stock_qty" placeholder="Es. 150.000">

        </div>

        <div class="form-group">

            <label>Scorta minima</label>

            <input type="number" step="0.001" min="0" name="min_stock" value="{{ $stock->min_stock ?? 0 }}">

        </div>

    </div>

</div>

<div style="margin-top:16px;display:flex;gap:10px">

<button type="submit" class="btn btn-primary">

💾 Salva modifiche

</button>

<a href="{{ url('/products') }}" class="btn btn-secondary">

Annulla

</a>

</div>

</form>

<script>

function calcMargine(){

const price = parseFloat(document.getElementById('price_input').value) || 0

const cost  = parseFloat(document.getElementById('cost_input').value) || 0

const pct   = price > 0 ? ((price - cost) / price * 100) : 0

const el = document.getElementById('margine_display')

el.textContent = pct.toFixed(1).replace('.',',') + '%'

}

</script>

@endsection