@extends('layouts.app')

@section('page-title','Nuovo Prodotto')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">➕ Nuovo Prodotto</div>
        <div class="page-sub">Aggiungi un prodotto al catalogo</div>
    </div>
    <a href="{{ url('/products') }}" class="btn btn-secondary">← Torna ai prodotti</a>
</div>

<form method="POST" action="{{ url('/products') }}">
@csrf

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

    {{-- COLONNA SINISTRA --}}
    <div class="card">

        <div style="font-weight:700;margin-bottom:16px">Dati Prodotto</div>

        <div class="form-group">
            <label>Nome Prodotto *</label>
            <input type="text" name="name" required placeholder="es. Carciofi, Basilico...">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
                <label>Origine</label>
                <input type="text" name="origin" placeholder="IT, ES, EC...">
            </div>
            <div class="form-group">
                <label>Aliquota IVA</label>
                <select name="vat_rate">
                    <option value="4"  selected>4%</option>
                    <option value="5">5%</option>
                    <option value="10">10%</option>
                    <option value="22">22%</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Tipo Vendita *</label>
            <select name="sale_type" id="sale_type" onchange="onSaleTypeChange()">
                <option value="kg">A Kg</option>
                <option value="unit">A Pezzi / Mazzi</option>
            </select>
        </div>

        <div style="border-top:1px solid var(--border);margin:16px 0"></div>

        {{-- ── SEZIONE KG ── --}}
        <div id="section-kg">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Prezzo Vendita</label>
                    <input type="number" step="0.01" name="price" placeholder="0.00">
                    <div style="font-size:10px;color:#999;margin-top:3px">€/kg</div>
                </div>
                <div class="form-group">
                    <label>Prezzo Costo</label>
                    <input type="number" step="0.01" name="cost_price" placeholder="0.00">
                    <div style="font-size:10px;color:#999;margin-top:3px">€/kg</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Peso Medio Cassa (kg)</label>
                    <input type="number" step="0.001" name="avg_box_weight" placeholder="es. 9.000">
                </div>
                <div class="form-group">
                    <label>Tara per Cassa (kg)</label>
                    <input type="number" step="0.001" name="tara" value="0">
                </div>
            </div>

        </div>

        {{-- ── SEZIONE PEZZI ── --}}
        <div id="section-unit" style="display:none">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Prezzo Vendita</label>
                    <input type="number" step="0.01" id="price_unit" placeholder="0.00"
                           oninput="syncPriceUnit()">
                    <div style="font-size:10px;color:#999;margin-top:3px">€/pz</div>
                </div>
                <div class="form-group">
                    <label>Prezzo Costo</label>
                    <input type="number" step="0.01" id="cost_unit" placeholder="0.00"
                           oninput="syncCostUnit()">
                    <div style="font-size:10px;color:#999;margin-top:3px">€/pz</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Pezzi / Mazzi per Cassa *</label>
                    <input type="number" step="1" name="pieces_per_box"
                           id="pieces_per_box" placeholder="es. 10"
                           oninput="calcUnitWeight()">
                </div>
                <div class="form-group">
                    <label>Peso Medio Cassa (kg)</label>
                    <input type="number" step="0.001" name="avg_box_weight"
                           id="avg_box_weight" placeholder="es. 2.000"
                           oninput="calcUnitWeight()">
                </div>
            </div>

            {{-- Peso unitario calcolato --}}
            <div id="unit-weight-box" style="background:#f0faf4;border:1px solid #c3e6cb;border-radius:6px;padding:10px 14px;margin-bottom:12px;display:none">
                <div style="font-size:10px;color:#2d6a4f;font-weight:700;text-transform:uppercase;margin-bottom:4px">
                    Peso per pezzo / mazzo
                </div>
                <div style="font-size:18px;font-weight:700;color:#2d6a4f">
                    <span id="unit-weight-display">—</span> kg
                </div>
            </div>

            {{-- Tara opzionale --}}
            <div>
                <button type="button" onclick="toggleTara()"
                    style="background:none;border:none;color:#7a9e8e;font-size:11px;cursor:pointer;padding:0;text-decoration:underline">
                    ⚙️ Aggiungi tara cassa (opzionale)
                </button>
                <div id="tara-box" style="display:none;margin-top:10px">
                    <div class="form-group">
                        <label>Tara per Cassa (kg)</label>
                        <input type="number" step="0.001" name="tara" value="0">
                    </div>
                </div>
            </div>

            {{-- Hidden fields --}}
            <input type="hidden" name="price"      id="price_hidden">
            <input type="hidden" name="cost_price" id="cost_hidden">

        </div>

    </div>

    {{-- COLONNA DESTRA: STOCK INIZIALE --}}
    <div class="card">

        <div style="font-weight:700;margin-bottom:16px">Stock Iniziale</div>

        <div class="form-group">
            <label>Quantità iniziale</label>
            <input type="number" step="0.001" name="new_stock_qty"
                   placeholder="0 — lascia vuoto se non hai stock">
        </div>

        <div class="form-group">
            <label>Scorta minima</label>
            <input type="number" step="0.001" name="min_stock" value="0">
        </div>

        <input type="hidden" name="unit" id="unit_hidden" value="kg">

    </div>

</div>

<div style="margin-top:20px;display:flex;gap:10px">
    <button type="submit" class="btn btn-primary">💾 Salva Prodotto</button>
    <a href="{{ url('/products') }}" class="btn btn-secondary">Annulla</a>
</div>

</form>

<script>

function onSaleTypeChange(){
    const sale   = document.getElementById('sale_type').value;
    const isUnit = sale === 'unit';

    document.getElementById('section-kg').style.display   = isUnit ? 'none' : '';
    document.getElementById('section-unit').style.display = isUnit ? '' : 'none';
    document.getElementById('unit_hidden').value           = isUnit ? 'pz' : 'kg';
}

function toggleTara(){
    const box = document.getElementById('tara-box');
    box.style.display = box.style.display === 'none' ? '' : 'none';
}

function syncPriceUnit(){
    document.getElementById('price_hidden').value =
        document.getElementById('price_unit').value;
}

function syncCostUnit(){
    document.getElementById('cost_hidden').value =
        document.getElementById('cost_unit').value;
}

function calcUnitWeight(){
    const weight  = parseFloat(document.getElementById('avg_box_weight').value) || 0;
    const pieces  = parseFloat(document.getElementById('pieces_per_box').value) || 0;
    const box     = document.getElementById('unit-weight-box');
    const display = document.getElementById('unit-weight-display');

    if(weight > 0 && pieces > 0){
        box.style.display = '';
        display.textContent = (weight / pieces)
            .toLocaleString('it-IT', {minimumFractionDigits:3, maximumFractionDigits:3});
    } else {
        box.style.display = 'none';
    }
}

</script>

@endsection