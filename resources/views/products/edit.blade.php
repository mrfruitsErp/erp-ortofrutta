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

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
                <label>Origine</label>
                <input type="text" name="origin" value="{{ $product->origin }}">
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

        <div class="form-group">
            <label>Tipo Vendita</label>
            <select name="sale_type" id="sale_type" onchange="onSaleTypeChange()">
                <option value="kg"   {{ $product->sale_type == 'kg'   ? 'selected' : '' }}>A Kg</option>
                <option value="unit" {{ $product->sale_type == 'unit' ? 'selected' : '' }}>A Pezzi / Mazzi</option>
            </select>
        </div>

        <div style="border-top:1px solid var(--border);margin:16px 0"></div>

        {{-- PREZZI — un solo set di campi, label e unità cambiano via JS --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
                <label id="label-price">Prezzo Vendita</label>
                <input type="number" step="0.01" name="price" id="field-price"
                       value="{{ $product->price }}">
                <div style="font-size:10px;color:#999;margin-top:3px" id="unit-price">
                    {{ $product->sale_type == 'unit' ? '€/pz' : '€/kg' }}
                </div>
            </div>
            <div class="form-group">
                <label id="label-cost">Prezzo Costo</label>
                <input type="number" step="0.01" name="cost_price" id="field-cost"
                       value="{{ $product->cost_price }}">
                <div style="font-size:10px;color:#999;margin-top:3px" id="unit-cost">
                    {{ $product->sale_type == 'unit' ? '€/pz' : '€/kg' }}
                </div>
            </div>
        </div>

        {{-- SEZIONE KG --}}
        <div id="section-kg">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Peso Medio Cassa (kg)</label>
                    <input type="number" step="0.001" name="avg_box_weight"
                           value="{{ $product->avg_box_weight }}">
                </div>
                <div class="form-group">
                    <label>Tara per Cassa (kg)</label>
                    <input type="number" step="0.001" name="tara"
                           value="{{ $product->tara ?? 0 }}">
                </div>
            </div>
        </div>

        {{-- SEZIONE PEZZI --}}
        <div id="section-unit" style="display:none">

            <div class="form-group">
                <label>Pezzi / Mazzi per Cassa</label>
                <input type="number" step="1" name="pieces_per_box"
                       id="pieces_per_box"
                       value="{{ $product->pieces_per_box }}"
                       placeholder="es. 10">
            </div>

            <div style="margin-top:8px">
                <button type="button" onclick="toggleAdvanced()"
                    style="background:none;border:none;color:#7a9e8e;font-size:11px;cursor:pointer;padding:0;text-decoration:underline">
                    ⚙️ Dati tecnici cassa (opzionale)
                </button>
                <div id="advanced-unit" style="display:none;margin-top:12px">
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
                    <div id="unit-weight-box" style="background:#f0faf4;border:1px solid #c3e6cb;border-radius:6px;padding:10px 14px;margin-bottom:12px">
                        <div style="font-size:10px;color:#2d6a4f;font-weight:700;text-transform:uppercase;margin-bottom:4px">Peso per pezzo / mazzo</div>
                        <div style="font-size:18px;font-weight:700;color:#2d6a4f">
                            <span id="unit-weight-display">—</span> kg
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- COLONNA DESTRA: STOCK + DISPONIBILITÀ --}}
    <div style="display:flex;flex-direction:column;gap:20px">

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

        <input type="hidden" name="unit" id="unit_hidden"
               value="{{ $product->unit }}">

    </div>

    {{-- DISPONIBILITÀ E MODALITÀ ORDINE --}}
    <div class="card">

        <div style="font-weight:700;margin-bottom:16px">🛒 Disponibilità & Ordine Cliente</div>

        <div class="form-group">
            <label>Disponibilità</label>
            <select name="disponibilita">
                <option value="disponibile"     {{ ($product->disponibilita ?? 'disponibile') == 'disponibile'     ? 'selected' : '' }}>✅ Disponibile</option>
                <option value="su_richiesta"    {{ ($product->disponibilita ?? 'disponibile') == 'su_richiesta'    ? 'selected' : '' }}>🔶 Su richiesta</option>
                <option value="non_disponibile" {{ ($product->disponibilita ?? 'disponibile') == 'non_disponibile' ? 'selected' : '' }}>❌ Non disponibile / Fuori stagione</option>
            </select>
            <div style="font-size:11px;color:#999;margin-top:4px">
                Su richiesta e Non disponibile appaiono nel link cliente con avviso — il cliente può comunque fare una richiesta.
            </div>
        </div>

        <div class="form-group">
            <label>Modalità ordine cliente</label>
            <select name="ordine_step" id="ordine_step" onchange="updateOrdineHint()">
                <option value="colli"       {{ ($product->ordine_step ?? 'colli') == 'colli'       ? 'selected' : '' }}>📦 Solo a colli interi</option>
                <option value="mezzo_collo" {{ ($product->ordine_step ?? 'colli') == 'mezzo_collo' ? 'selected' : '' }}>📦 Colli interi + mezza cassa</option>
                <option value="kg"          {{ ($product->ordine_step ?? 'colli') == 'kg'          ? 'selected' : '' }}>⚖️ A kg (input libero)</option>
                <option value="grammi"      {{ ($product->ordine_step ?? 'colli') == 'grammi'      ? 'selected' : '' }}>🔬 A grammi (es. zenzero, spezie)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Quantità minima ordinabile</label>
            <input type="number" step="0.001" name="ordine_min"
                   value="{{ $product->ordine_min ?? 1 }}"
                   placeholder="es. 0.100 per zenzero">
            <div style="font-size:11px;color:#999;margin-top:4px" id="ordine_min_hint"></div>
        </div>

    </div>

    </div>


<div style="margin-top:20px;display:flex;gap:10px">
    <button type="submit" class="btn btn-primary">💾 Salva</button>
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

    // Aggiorna label e unità dei campi prezzo
    document.getElementById('unit-price').textContent = isUnit ? '€/pz' : '€/kg';
    document.getElementById('unit-cost').textContent  = isUnit ? '€/pz' : '€/kg';
}

function toggleAdvanced(){
    const box = document.getElementById('advanced-unit');
    box.style.display = box.style.display === 'none' ? '' : 'none';
}

function calcUnitWeight(){
    const weightEl = document.getElementById('avg_box_weight');
    const piecesEl = document.getElementById('pieces_per_box');
    if(!weightEl || !piecesEl) return;

    const weight = parseFloat(weightEl.value) || 0;
    const pieces = parseFloat(piecesEl.value) || 0;

    if(weight > 0 && pieces > 0){
        document.getElementById('unit-weight-display').textContent =
            (weight / pieces).toLocaleString('it-IT', {minimumFractionDigits:3, maximumFractionDigits:3});
    } else {
        document.getElementById('unit-weight-display').textContent = '—';
    }
}

window.addEventListener('load', function(){
    onSaleTypeChange();
    updateOrdineHint();
});

function updateOrdineHint(){
    const el = document.getElementById('ordine_step');
    if(!el) return;
    const hint  = document.getElementById('ordine_min_hint');
    const hints = {
        'colli':       'Es. 1 = minimo 1 cassa intera',
        'mezzo_collo': 'Es. 0.5 = minimo mezza cassa',
        'kg':          'Es. 1 = minimo 1 kg',
        'grammi':      'Es. 0.100 = minimo 100 grammi',
    };
    if(hint) hint.textContent = hints[el.value] || '';
}

</script>

@endsection