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
                <label>Categoria</label>
                <select name="category">
                    <option value="">— Nessuna —</option>
                    @foreach(['Frutta','Verdura','Erbe Aromatiche','Funghi','Frutta Secca','Legumi Secchi','Insalata 4a Gamma'] as $cat)
                        <option value="{{ $cat }}" {{ ($product->category ?? '') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Codice SKU</label>
                <input type="text" value="{{ $product->sku ?? '— auto —' }}" readonly
                       style="background:var(--bg);color:var(--muted);font-family:monospace;font-weight:700">
                <div style="font-size:10px;color:#999;margin-top:3px">Generato automaticamente dalla categoria</div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
                <label>Origine</label>
                <input type="text" name="origin" value="{{ $product->origin }}">
            </div>
            <div class="form-group">
                <label>Aliquota IVA</label>
                <select name="vat_rate">
                    <option value="4"  {{ ($product->vat_rate ?? 4) == 4  ? 'selected' : '' }}>4%</option>
                    <option value="5"  {{ ($product->vat_rate ?? 4) == 5  ? 'selected' : '' }}>5%</option>
                    <option value="10" {{ ($product->vat_rate ?? 4) == 10 ? 'selected' : '' }}>10%</option>
                    <option value="22" {{ ($product->vat_rate ?? 4) == 22 ? 'selected' : '' }}>22%</option>
                </select>
            </div>
        </div>

        <div style="border-top:1px solid var(--border);margin:16px 0"></div>

        {{-- MODALITÀ VENDITA --}}
        <div style="font-weight:700;font-size:11px;text-transform:uppercase;color:#7a9e8e;letter-spacing:0.5px;margin-bottom:12px">
            📦 Modalità Vendita
        </div>

        <div class="form-group">
            <label>Come vendi questo prodotto?</label>
            <select name="modalita_vendita" id="modalita_vendita" onchange="onModalitaChange()">
                <option value="cassa_kg"    {{ ($product->modalita_vendita ?? 'cassa_kg') == 'cassa_kg'    ? 'selected' : '' }}>📦 A cassa — prezzo al kg (peso indicativo)</option>
                <option value="cassa_collo" {{ ($product->modalita_vendita ?? '')         == 'cassa_collo' ? 'selected' : '' }}>📦 A cassa — prezzo fisso per collo</option>
                <option value="kg_liberi"   {{ ($product->modalita_vendita ?? '')         == 'kg_liberi'   ? 'selected' : '' }}>⚖️ A kg liberi</option>
                <option value="pezzo"       {{ ($product->modalita_vendita ?? '')         == 'pezzo'       ? 'selected' : '' }}>🌿 A pezzo / mazzo</option>
                <option value="peso_step"   {{ ($product->modalita_vendita ?? '')         == 'peso_step'   ? 'selected' : '' }}>🧂 A peso con step (es. zenzero, spezie)</option>
            </select>
            <div style="font-size:11px;color:#999;margin-top:4px" id="modalita_help"></div>
        </div>

        {{-- PREZZI --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
                <label>Prezzo Vendita</label>
                <input type="number" step="0.01" name="price" id="field_price"
                       value="{{ $product->price }}" oninput="updateRiepilogo()">
                <div style="font-size:10px;color:#999;margin-top:3px" id="unit_price">€/kg</div>
            </div>
            <div class="form-group">
                <label>Prezzo Costo</label>
                <input type="number" step="0.01" name="cost_price" id="field_cost"
                       value="{{ $product->cost_price }}">
                <div style="font-size:10px;color:#999;margin-top:3px" id="unit_cost">€/kg</div>
            </div>
        </div>

        {{-- ORDINE: MINIMO + STEP + MAX --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
            <div class="form-group">
                <label>Ordine minimo</label>
                <input type="number" step="0.001" name="ordine_min" id="field_ordine_min"
                       value="{{ $product->ordine_min ?? 1 }}" oninput="updateRiepilogo()">
                <div style="font-size:10px;color:#999;margin-top:3px" id="unit_minimo">casse</div>
            </div>
            <div class="form-group" id="wrap_step_grammi" style="display:none">
                <label>Step ordinabile</label>
                <input type="number" step="10" min="10" name="step_grammi" id="field_step_grammi"
                       value="{{ $product->step_grammi ?? 100 }}" oninput="updateRiepilogo()">
                <div style="font-size:10px;color:#999;margin-top:3px">grammi</div>
            </div>
            <div class="form-group">
                <label>Ordine massimo</label>
                <input type="number" step="0.001" name="ordine_max" id="field_ordine_max"
                       value="{{ $product->ordine_max }}"
                       placeholder="Nessun limite">
                <div style="font-size:10px;color:#999;margin-top:3px" id="unit_massimo">casse</div>
            </div>
        </div>

        {{-- MIN KG PER CLIENTI FLESSIBILI (solo per cassa_kg) --}}
        <div id="wrap_flessibile" style="display:none;background:#fff8e1;border:1px solid #ffe082;border-radius:6px;padding:12px 14px;margin-bottom:12px">
            <div style="font-size:11px;color:#f57f17;font-weight:700;margin-bottom:8px">
                ⚡ Clienti con ordine a kg (listino HoReCa)
            </div>
            <div style="font-size:12px;color:#666;margin-bottom:8px">
                I clienti il cui listino permette di ordinare a kg vedranno anche l'opzione kg
                per questo prodotto. Imposta il minimo kg.
            </div>
            <div class="form-group" style="margin-bottom:0">
                <label>Minimo kg per ordini a kg</label>
                <input type="number" step="0.1" name="ordine_min_kg" id="field_ordine_min_kg"
                       value="{{ $product->ordine_min_kg }}"
                       placeholder="es. 2 (= minimo 2 kg)">
                <div style="font-size:10px;color:#999;margin-top:3px">
                    Lascia vuoto = non ordinabile a kg nemmeno per clienti con listino flessibile
                </div>
            </div>
        </div>

        <div style="border-top:1px solid var(--border);margin:16px 0"></div>

        {{-- DATI TECNICI CASSA --}}
        <div id="wrap_dati_cassa">
            <button type="button" onclick="toggleCassa()"
                style="background:none;border:none;color:#7a9e8e;font-size:11px;cursor:pointer;padding:0;text-decoration:underline">
                📐 Dati tecnici cassa (opzionale) ▾
            </button>
            <div id="dati_cassa" style="display:{{ ($product->avg_box_weight || $product->pieces_per_box) ? '' : 'none' }};margin-top:12px">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
                    <div class="form-group">
                        <label>Pezzi / Mazzi per Cassa</label>
                        <input type="number" step="1" name="pieces_per_box" id="field_pieces"
                               value="{{ $product->pieces_per_box }}" oninput="calcPesoPerPezzo();updateRiepilogo()">
                    </div>
                    <div class="form-group">
                        <label>Peso Medio Cassa (kg)</label>
                        <input type="number" step="0.001" name="avg_box_weight" id="field_weight"
                               value="{{ $product->avg_box_weight }}" oninput="calcPesoPerPezzo();updateRiepilogo()">
                    </div>
                    <div class="form-group">
                        <label>Tara per Cassa (kg)</label>
                        <input type="number" step="0.001" name="tara"
                               value="{{ $product->tara ?? 0 }}">
                    </div>
                </div>
                <div id="box_peso_pezzo" style="background:#f0faf4;border:1px solid #c3e6cb;border-radius:6px;padding:10px 14px;margin-top:8px;display:none">
                    <div style="font-size:10px;color:#2d6a4f;font-weight:700;text-transform:uppercase;margin-bottom:4px">Peso per pezzo / mazzo</div>
                    <div style="font-size:18px;font-weight:700;color:#2d6a4f">
                        <span id="peso_pezzo_display">—</span> kg
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- COLONNA DESTRA --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        {{-- STOCK --}}
        <div class="card">
            <div style="font-weight:700;margin-bottom:16px">Gestione Stock</div>
            <div style="margin-bottom:16px">
                <div style="font-size:11px;color:#999;text-transform:uppercase;font-weight:600;margin-bottom:4px">Stock attuale</div>
                <div style="font-size:22px;font-weight:700">
                    {{ number_format($stock->quantity ?? 0, 3, ',', '.') }}
                    <span style="font-size:14px;color:#999" id="stock_unit_label">{{ $product->unita_stock ?? 'pz' }}</span>
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

        {{-- DISPONIBILITÀ --}}
        <div class="card">
            <div style="font-weight:700;margin-bottom:16px">📋 Disponibilità</div>
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

            {{-- RIEPILOGO VENDITA --}}
            <div style="background:#f8f9fa;border:1px solid var(--border);border-radius:6px;padding:12px 14px;margin-top:16px">
                <div style="font-size:10px;color:#666;font-weight:700;text-transform:uppercase;margin-bottom:8px">Riepilogo vendita</div>
                <div id="riepilogo" style="font-size:13px;line-height:1.7"></div>
            </div>
        </div>

    </div>

</div>

<div style="margin-top:20px;display:flex;gap:10px">
    <button type="submit" class="btn btn-primary">💾 Salva</button>
    <a href="{{ url('/products') }}" class="btn btn-secondary">Annulla</a>
</div>

</form>

<script>
const config = {
    cassa_kg: {
        unitPrezzo: '€/kg', unitMinimo: 'casse', unitMax: 'casse', unitStock: 'casse',
        help: 'Il cliente ordina per casse. Prezzo finale = €/kg × peso reale alla consegna (indicativo).',
        showStep: false, showPesoPezzo: false, showFlessibile: true,
    },
    cassa_collo: {
        unitPrezzo: '€/collo', unitMinimo: 'casse', unitMax: 'casse', unitStock: 'casse',
        help: 'Il cliente ordina per casse. Prezzo fisso per collo, indipendente dal peso.',
        showStep: false, showPesoPezzo: false, showFlessibile: false,
    },
    kg_liberi: {
        unitPrezzo: '€/kg', unitMinimo: 'kg', unitMax: 'kg', unitStock: 'kg',
        help: 'Il cliente inserisce i kg desiderati liberamente.',
        showStep: false, showPesoPezzo: false, showFlessibile: false,
    },
    pezzo: {
        unitPrezzo: '€/pz', unitMinimo: 'pezzi', unitMax: 'pezzi', unitStock: 'pz',
        help: 'Il cliente ordina per pezzi o mazzi (erbe aromatiche, insalate, angurie, meloni...).',
        showStep: false, showPesoPezzo: true, showFlessibile: false,
    },
    peso_step: {
        unitPrezzo: '€/kg', unitMinimo: 'grammi', unitMax: 'grammi', unitStock: 'kg',
        help: 'Il cliente ordina in multipli di grammi (es. zenzero a step di 100g).',
        showStep: true, showPesoPezzo: false, showFlessibile: false,
    }
};

function onModalitaChange() {
    const mode = document.getElementById('modalita_vendita').value;
    const c = config[mode];
    if (!c) return;

    document.getElementById('unit_price').textContent   = c.unitPrezzo;
    document.getElementById('unit_cost').textContent     = c.unitPrezzo;
    document.getElementById('unit_minimo').textContent   = c.unitMinimo;
    document.getElementById('unit_massimo').textContent  = c.unitMax;
    document.getElementById('modalita_help').textContent = c.help;
    document.getElementById('stock_unit_label').textContent = c.unitStock;

    document.getElementById('wrap_step_grammi').style.display = c.showStep ? '' : 'none';
    document.getElementById('box_peso_pezzo').style.display   = c.showPesoPezzo ? '' : 'none';
    document.getElementById('wrap_flessibile').style.display  = c.showFlessibile ? '' : 'none';

    const cassaWrap = document.getElementById('wrap_dati_cassa');
    cassaWrap.style.opacity = (mode === 'kg_liberi' || mode === 'peso_step') ? '0.5' : '1';

    updateRiepilogo();
}

function toggleCassa() {
    const box = document.getElementById('dati_cassa');
    box.style.display = box.style.display === 'none' ? '' : 'none';
}

function calcPesoPerPezzo() {
    const peso  = parseFloat(document.getElementById('field_weight').value) || 0;
    const pezzi = parseInt(document.getElementById('field_pieces').value) || 0;
    const el    = document.getElementById('peso_pezzo_display');
    if (peso > 0 && pezzi > 0) {
        el.textContent = (peso / pezzi).toLocaleString('it-IT', {minimumFractionDigits:3, maximumFractionDigits:3});
    } else {
        el.textContent = '—';
    }
}

function updateRiepilogo() {
    const mode   = document.getElementById('modalita_vendita').value;
    const prezzo = document.getElementById('field_price').value || '—';
    const minimo = document.getElementById('field_ordine_min').value || '1';
    const peso   = document.getElementById('field_weight').value || '0';
    const step   = document.getElementById('field_step_grammi').value || '100';
    const max    = document.getElementById('field_ordine_max').value;
    const minKg  = document.getElementById('field_ordine_min_kg').value;
    const el     = document.getElementById('riepilogo');

    let html = '';
    const c = config[mode];
    const maxLabel = max ? `<br><strong>Massimo:</strong> ${max} ${c ? c.unitMax : ''}` : '';

    switch (mode) {
        case 'cassa_kg':
            const stima = (parseFloat(prezzo) * parseFloat(peso)).toFixed(2);
            html = `<strong>Prezzo:</strong> ${prezzo} €/kg<br>
                    <strong>Peso cassa:</strong> ≈ ${peso} kg<br>
                    <strong>Stima per cassa:</strong> ≈ € ${stima}<br>
                    <strong>Minimo:</strong> ${minimo} cassa/e${maxLabel}`;
            if (minKg) {
                html += `<br><span style="color:#f57f17">⚡ <strong>Clienti kg:</strong> min ${minKg} kg</span>`;
            }
            break;
        case 'cassa_collo':
            html = `<strong>Prezzo:</strong> ${prezzo} €/collo (fisso)<br>
                    <strong>Minimo:</strong> ${minimo} cassa/e${maxLabel}`;
            break;
        case 'kg_liberi':
            html = `<strong>Prezzo:</strong> ${prezzo} €/kg<br>
                    <strong>Minimo:</strong> ${minimo} kg${maxLabel}`;
            break;
        case 'pezzo':
            html = `<strong>Prezzo:</strong> ${prezzo} €/pz<br>
                    <strong>Minimo:</strong> ${minimo} pezzo/i${maxLabel}`;
            break;
        case 'peso_step':
            html = `<strong>Prezzo:</strong> ${prezzo} €/kg<br>
                    <strong>Step:</strong> ${step}g<br>
                    <strong>Minimo:</strong> ${minimo}g${maxLabel}`;
            break;
    }
    el.innerHTML = html;
}

window.addEventListener('load', function() {
    onModalitaChange();
    calcPesoPerPezzo();
});
</script>

@endsection