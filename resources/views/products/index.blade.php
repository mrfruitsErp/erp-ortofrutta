@extends('layouts.app')
@section('page-title', 'Prodotti')
@section('content')

<style>
@media (max-width: 768px) {
    .desktop-only { display: none !important; }
    .prod-table { font-size: 12px; }
    .price-col { width: 64px !important; }
}
.editable-cell {
    cursor: pointer;
    border-bottom: 1px dashed currentColor;
    transition: background .15s;
    border-radius: 3px;
    padding: 1px 3px;
}
.editable-cell:hover { background: rgba(0,0,0,.04); }
.editable-cell input {
    width: 72px; text-align: right; font-family: inherit;
    font-size: inherit; font-weight: inherit; color: inherit;
    border: none; border-bottom: 2px solid currentColor;
    background: transparent; outline: none; padding: 0;
}
.flash-ok { animation: flashGreen .6s ease; }
@keyframes flashGreen { 0%,100%{background:transparent} 50%{background:#d4edda} }
.massive-bar {
    display: none; background: var(--dark); color: #fff;
    padding: 12px 18px; border-radius: 10px; margin-bottom: 14px;
    align-items: center; gap: 10px; flex-wrap: wrap;
}
.massive-bar.active { display: flex; }
.massive-bar input { background:rgba(255,255,255,.12); border-color:rgba(255,255,255,.25); color:#fff; margin:0; width:90px; }
.massive-bar button { padding: 6px 13px; font-size: 12px; }
.sku-badge {
    font-family: 'DM Mono', monospace; font-size: 10px; font-weight: 700;
    background: #f0f4f1; border: 1px solid #c3e6cb;
    padding: 1px 6px; border-radius: 5px; color: #2d6a4f;
}
.stato-ok    { background: var(--green-xl); color: var(--green); }
.stato-esau  { background: #fde8e8; color: #c0392b; }
.stato-sotto { background: #fff3e0; color: #e65100; }
.disp-badge  { padding: 2px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; cursor: pointer; }
.marg-badge  { padding: 2px 9px; border-radius: 20px; font-size: 12px; font-weight: 700; font-family: 'DM Mono', monospace; }
.sort-th     { cursor: pointer; user-select: none; white-space: nowrap; }
.sort-th:hover { color: var(--green); }
</style>

<div class="page-header">
    <div>
        <div class="page-title">🛒 Prodotti</div>
        <div class="page-sub">Catalogo prodotti — editing inline su tutto</div>
    </div>
    <a href="{{ url('/products/create') }}" class="btn btn-primary">+ Nuovo</a>
</div>

{{-- MASSIVE BAR --}}
<div id="massiveBar" class="massive-bar">
    <span id="selCount" style="font-weight:700;font-size:14px;min-width:90px">0 selezionati</span>
    <span style="font-size:12px;color:rgba(255,255,255,.6)">Prezzo %</span>
    <input type="number" id="prezzoPerc" step="0.1" placeholder="±%">
    <button onclick="massiveAction('price_percent','prezzoPerc')" class="btn btn-primary">Applica</button>
    <span style="font-size:12px;color:rgba(255,255,255,.6)">Costo %</span>
    <input type="number" id="costoPerc" step="0.1" placeholder="±%">
    <button onclick="massiveAction('cost_percent','costoPerc')" class="btn btn-primary">Applica</button>
    <span style="font-size:12px;color:rgba(255,255,255,.6)">Scorta min</span>
    <input type="number" id="minStVal" step="0.001" min="0" placeholder="qty">
    <button onclick="massiveAction('min_stock','minStVal')" class="btn btn-primary">Imposta</button>
    <button onclick="exportCSV()" class="btn btn-secondary">📥 CSV</button>
    <button onclick="deselectAll()" class="btn btn-secondary" style="margin-left:auto">✕</button>
</div>

{{-- FILTRI --}}
<div class="card" style="padding:10px 14px;margin-bottom:14px;display:flex;gap:8px;flex-wrap:wrap;align-items:center">
    <input type="text" id="fSearch" placeholder="🔍 Nome..." style="max-width:200px;margin:0">
    <select id="fOrigine" style="max-width:110px;margin:0">
        <option value="">Tutte origini</option>
        @foreach($products->pluck('origin')->unique()->filter()->sort() as $o)
            <option value="{{ strtolower($o) }}">{{ $o }}</option>
        @endforeach
    </select>
    <select id="fCat" style="max-width:150px;margin:0">
        <option value="">Tutte categorie</option>
        @foreach(['Frutta','Verdura','Erbe Aromatiche','Funghi','Frutta Secca','Legumi Secchi','Insalata 4a Gamma'] as $c)
            <option value="{{ strtolower($c) }}">{{ $c }}</option>
        @endforeach
    </select>
    <select id="fStato" style="max-width:140px;margin:0">
        <option value="">Tutti stati</option>
        <option value="ok">✓ OK</option>
        <option value="esaurito">⚠ Esaurito</option>
        <option value="sottocosto">⚠ Sotto costo</option>
    </select>
    <select id="fDisp" style="max-width:160px;margin:0">
        <option value="">Tutte disp.</option>
        <option value="disponibile">✅ Disponibile</option>
        <option value="su_richiesta">🔶 Su richiesta</option>
        <option value="non_disponibile">❌ Non disp.</option>
    </select>
    <button onclick="resetFiltri()" class="btn btn-secondary" style="padding:6px 12px;font-size:12px">✕ Reset</button>
    <span id="countLabel" style="font-size:12px;color:var(--muted);margin-left:auto"></span>
</div>

{{-- TABELLA --}}
<div class="card" style="padding:0;overflow-x:auto">
<table id="prodTable" class="prod-table" style="width:100%;min-width:900px">
<thead>
<tr>
    <th style="width:36px;text-align:center">
        <input type="checkbox" id="selAll" onchange="toggleAll(this.checked)">
    </th>
    <th class="sort-th" onclick="sortBy('name')">Nome / SKU ↕</th>
    <th class="sort-th desktop-only" onclick="sortBy('category')" style="width:90px">Categoria ↕</th>
    <th class="sort-th desktop-only" onclick="sortBy('origin')" style="width:55px">Orig. ↕</th>
    <th class="sort-th desktop-only" style="width:50px;text-align:center">UM</th>
    <th class="sort-th desktop-only" onclick="sortBy('tara')" style="width:65px;text-align:right">Tara ↕</th>
    <th class="sort-th desktop-only" onclick="sortBy('peso')" style="width:70px;text-align:right">P.Cassa ↕</th>
    <th class="sort-th" onclick="sortBy('cost')" style="width:72px;text-align:right">Costo ↕</th>
    <th class="sort-th" onclick="sortBy('price')" style="width:78px;text-align:right;color:#e67e22">Base € ↕</th>
    <th class="sort-th price-col" onclick="sortBy('horeca')" style="width:78px;text-align:right;color:#2980b9">HoReCa ↕</th>
    <th class="sort-th price-col desktop-only" onclick="sortBy('dett')" style="width:78px;text-align:right;color:#27ae60">Dett. ↕</th>
    <th class="sort-th price-col desktop-only" onclick="sortBy('gdo')" style="width:78px;text-align:right;color:#8e44ad">GDO ↕</th>
    <th class="sort-th" onclick="sortBy('margin')" style="width:72px;text-align:center">Marg. ↕</th>
    <th class="sort-th" onclick="sortBy('stock')" style="width:78px;text-align:right">Stock ↕</th>
    <th style="width:72px;text-align:center">Disp.</th>
    <th class="desktop-only" style="width:65px;text-align:center">Mod.</th>
    <th style="width:60px;text-align:center">Stato</th>
    <th style="width:75px;text-align:center">Azioni</th>
</tr>
</thead>
<tbody>
@forelse($products as $p)
@php
    $cost     = (float)($p->cost_price ?? 0);
    $price    = (float)($p->price ?? 0);
    $horeca   = (float)($p->price_horeca ?? $price);
    $dett     = (float)($p->price_dettaglio ?? $price);
    $gdo      = (float)($p->price_gdo ?? $price);
    $margin   = $price > 0 ? (($price - $cost) / $price) * 100 : 0;
    $stockQty = (float)($p->stock->quantity ?? 0);
    $minStock = (float)($p->stock->min_stock ?? 0);
    $stato    = $stockQty <= 0 ? 'esaurito' : ($price < $cost ? 'sottocosto' : 'ok');
    $disp     = $p->disponibilita ?? 'disponibile';
    $margColor = $margin >= 40 ? '#27ae60' : ($margin >= 20 ? '#f39c12' : '#c0392b');
    $margBg    = $margin >= 40 ? 'var(--green-xl)' : ($margin >= 20 ? '#fff3e0' : '#fde8e8');
@endphp
<tr class="product-row"
    data-id="{{ $p->id }}"
    data-name="{{ $p->name }}"
    data-name-lower="{{ strtolower($p->name) }}"
    data-category="{{ strtolower($p->category ?? '') }}"
    data-origin="{{ strtolower($p->origin ?? '') }}"
    data-cost="{{ $cost }}"
    data-price="{{ $price }}"
    data-horeca="{{ $horeca }}"
    data-dett="{{ $dett }}"
    data-gdo="{{ $gdo }}"
    data-margin="{{ round($margin,1) }}"
    data-stock="{{ $stockQty }}"
    data-stato="{{ $stato }}"
    data-disp="{{ $disp }}"
    data-tara="{{ $p->tara ?? 0 }}"
    data-peso="{{ $p->avg_box_weight ?? 0 }}">

    {{-- CHECK --}}
    <td style="text-align:center">
        <input type="checkbox" class="row-check" data-id="{{ $p->id }}" onchange="updateSel()">
    </td>

    {{-- NOME --}}
    <td>
        <div style="font-weight:700;font-size:13px;color:var(--dark)">{{ $p->name }}</div>
        <div style="display:flex;gap:5px;margin-top:2px;align-items:center;flex-wrap:wrap">
            <span class="sku-badge">{{ $p->sku ?? '—' }}</span>
            <span class="editable-cell" style="font-size:11px;color:var(--muted)"
                data-field="origin" data-id="{{ $p->id }}" data-type="text">{{ $p->origin ?? '—' }}</span>
        </div>
    </td>

    {{-- CATEGORIA --}}
    <td class="desktop-only" style="font-size:12px;color:var(--muted)">{{ $p->category }}</td>

    {{-- ORIGINE --}}
    <td class="desktop-only" style="font-size:12px;color:var(--muted);text-align:center">{{ $p->origin ?? '—' }}</td>

    {{-- UM --}}
    <td class="desktop-only" style="text-align:center">
        <span style="background:var(--bg);border:1px solid var(--border);padding:1px 6px;border-radius:5px;font-size:11px;font-weight:600">
            {{ $p->unita_prezzo }}
        </span>
    </td>

    {{-- TARA --}}
    <td class="desktop-only" style="text-align:right;font-size:12px;color:var(--muted);font-family:'DM Mono',monospace">
        {{ number_format($p->tara ?? 0, 3, ',', '.') }}
    </td>

    {{-- PESO CASSA --}}
    <td class="desktop-only" style="text-align:right;font-size:12px;color:var(--muted);font-family:'DM Mono',monospace">
        {{ number_format($p->avg_box_weight ?? 0, 3, ',', '.') }}
    </td>

    {{-- COSTO --}}
    <td style="text-align:right">
        <span class="editable-cell" style="font-size:12px;color:var(--muted);font-family:'DM Mono',monospace"
            data-field="cost_price" data-id="{{ $p->id }}" data-type="price" data-val="{{ $cost }}">
            {{ number_format($cost, 2, ',', '.') }}
        </span>
    </td>

    {{-- BASE --}}
    <td style="text-align:right">
        <span class="editable-cell price-display" style="font-family:'DM Mono',monospace;font-size:13px;font-weight:700;color:#e67e22"
            data-field="price" data-id="{{ $p->id }}" data-type="price" data-val="{{ $price }}">
            {{ number_format($price, 2, ',', '.') }}
        </span>
    </td>

    {{-- HORECA --}}
    <td style="text-align:right">
        <span class="editable-cell price-col" style="font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:#2980b9"
            data-field="price_horeca" data-id="{{ $p->id }}" data-type="price" data-val="{{ $horeca }}">
            {{ number_format($horeca, 2, ',', '.') }}
        </span>
    </td>

    {{-- DETTAGLIO --}}
    <td class="desktop-only" style="text-align:right">
        <span class="editable-cell price-col" style="font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:#27ae60"
            data-field="price_dettaglio" data-id="{{ $p->id }}" data-type="price" data-val="{{ $dett }}">
            {{ number_format($dett, 2, ',', '.') }}
        </span>
    </td>

    {{-- GDO --}}
    <td class="desktop-only" style="text-align:right">
        <span class="editable-cell price-col" style="font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:#8e44ad"
            data-field="price_gdo" data-id="{{ $p->id }}" data-type="price" data-val="{{ $gdo }}">
            {{ number_format($gdo, 2, ',', '.') }}
        </span>
    </td>

    {{-- MARGINE --}}
    <td style="text-align:center" data-val="{{ round($margin,1) }}">
        <span class="marg-badge" style="background:{{ $margBg }};color:{{ $margColor }}">
            {{ number_format($margin, 1, ',', '.') }}%
        </span>
    </td>

    {{-- STOCK --}}
    <td style="text-align:right;cursor:pointer" data-val="{{ $stockQty }}"
        onclick="editStock(this, {{ $p->id }})">
        <span class="stock-display" style="font-family:'DM Mono',monospace;font-size:13px;
            color:{{ $stockQty > $minStock ? 'var(--dark)' : '#c0392b' }};font-weight:{{ $stockQty > $minStock ? '400' : '700' }}">
            {{ number_format($stockQty, 2, ',', '.') }}
        </span>
        <div style="font-size:10px;color:var(--muted)">{{ $p->unita_stock }}</div>
    </td>

    {{-- DISPONIBILITÀ --}}
    <td style="text-align:center;cursor:pointer" data-disp="{{ $disp }}"
        onclick="toggleDisp(this, {{ $p->id }})">
        @if($disp=='disponibile')
            <span class="disp-badge" style="background:#d4edda;color:#2d6a4f">✅ OK</span>
        @elseif($disp=='su_richiesta')
            <span class="disp-badge" style="background:#fff3e0;color:#e65100">🔶 SR</span>
        @else
            <span class="disp-badge" style="background:#fde8e8;color:#c0392b">❌ No</span>
        @endif
    </td>

    {{-- MODALITÀ --}}
    <td class="desktop-only" style="text-align:center">
        <span style="font-size:10px;background:var(--bg);color:var(--muted);padding:2px 6px;border-radius:6px">
            {{ $p->modalita_vendita }}
        </span>
    </td>

    {{-- STATO --}}
    <td style="text-align:center">
        @if($stato=='ok')
            <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700" class="stato-ok">✓ OK</span>
        @elseif($stato=='esaurito')
            <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700" class="stato-esau">⚠ Esau.</span>
        @else
            <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700" class="stato-sotto">⚠ Costo</span>
        @endif
    </td>

    {{-- AZIONI --}}
    <td style="text-align:center">
        <a href="{{ url('/products/'.$p->id.'/edit') }}"
           style="font-size:11px;background:var(--green-xl);color:var(--green);padding:3px 9px;border-radius:6px;text-decoration:none;font-weight:600;white-space:nowrap">
            ✏️ Edit
        </a>
    </td>

</tr>
@empty
<tr><td colspan="18" style="text-align:center;padding:48px;color:var(--muted)">
    Nessun prodotto. <a href="{{ url('/products/create') }}">Aggiungi il primo →</a>
</td></tr>
@endforelse
</tbody>
</table>
</div>

{{-- LEGENDA --}}
<div style="display:flex;gap:14px;margin-top:10px;font-size:11px;color:var(--muted);flex-wrap:wrap">
    <span>💡 Clicca su qualsiasi prezzo, costo, origine o stock per modificare inline — TAB per campo successivo</span>
    <span style="color:#e67e22">■ Base</span>
    <span style="color:#2980b9">■ HoReCa</span>
    <span style="color:#27ae60">■ Dettaglio</span>
    <span style="color:#8e44ad">■ GDO</span>
    <span>Disp. = click per ciclare stato</span>
</div>

<script>
const CSRF = '{{ csrf_token() }}';

// ── FILTRI ───────────────────────────────────────────────
function filterRows() {
    const q    = document.getElementById('fSearch').value.toLowerCase();
    const orig = document.getElementById('fOrigine').value;
    const cat  = document.getElementById('fCat').value;
    const st   = document.getElementById('fStato').value;
    const dp   = document.getElementById('fDisp').value;
    let n = 0;
    document.querySelectorAll('.product-row').forEach(r => {
        const ok = (!q    || r.dataset.nameLower.includes(q))
                && (!orig || r.dataset.origin === orig)
                && (!cat  || r.dataset.category === cat)
                && (!st   || r.dataset.stato === st)
                && (!dp   || r.dataset.disp === dp);
        r.style.display = ok ? '' : 'none';
        if (ok) n++;
    });
    document.getElementById('countLabel').textContent = n + ' prodotti';
}
function resetFiltri() {
    ['fSearch','fOrigine','fCat','fStato','fDisp'].forEach(id => {
        const el = document.getElementById(id);
        el.tagName === 'INPUT' ? el.value = '' : el.value = '';
    });
    filterRows();
}
['fSearch'].forEach(id => document.getElementById(id).addEventListener('input', filterRows));
['fOrigine','fCat','fStato','fDisp'].forEach(id => document.getElementById(id).addEventListener('change', filterRows));
filterRows();

// ── SELEZIONE ────────────────────────────────────────────
function getSelected() {
    return [...document.querySelectorAll('.row-check:checked')].map(c => c.dataset.id);
}
function toggleAll(checked) {
    document.querySelectorAll('.product-row:not([style*="display: none"]) .row-check').forEach(c => c.checked = checked);
    updateSel();
}
function deselectAll() {
    document.querySelectorAll('.row-check').forEach(c => c.checked = false);
    document.getElementById('selAll').checked = false;
    updateSel();
}
function updateSel() {
    const ids = getSelected();
    const bar = document.getElementById('massiveBar');
    bar.classList.toggle('active', ids.length > 0);
    document.getElementById('selCount').textContent = ids.length + ' selezionati';
}

// ── AZIONI MASSIVE ───────────────────────────────────────
function massiveAction(action, inputId) {
    const ids = getSelected();
    const val = parseFloat(document.getElementById(inputId).value);
    if (!ids.length || isNaN(val)) return;
    fetch('/products/massive-update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ ids, action, value: val })
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); else alert(d.message); });
}

// ── ESPORTA CSV ──────────────────────────────────────────
function exportCSV() {
    const rows = [...document.querySelectorAll('.product-row')].filter(r => r.style.display !== 'none');
    const lines = [['Nome','Origine','Categoria','Costo','Base','HoReCa','Dettaglio','GDO','Margine%','Stock','Disp'].join(';')];
    rows.forEach(r => lines.push([
        '"'+r.dataset.name+'"', r.dataset.origin, r.dataset.category,
        r.dataset.cost, r.dataset.price, r.dataset.horeca,
        r.dataset.dett, r.dataset.gdo, r.dataset.margin,
        r.dataset.stock, r.dataset.disp
    ].join(';')));
    const a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob(['\uFEFF'+lines.join('\n')], {type:'text/csv;charset=utf-8;'}));
    a.download = 'prodotti_'+new Date().toISOString().slice(0,10)+'.csv';
    a.click();
}

// ── ORDINAMENTO ──────────────────────────────────────────
let sortDir = {};
function sortBy(key) {
    sortDir[key] = !sortDir[key];
    const tbody = document.querySelector('#prodTable tbody');
    const rows = [...tbody.querySelectorAll('.product-row')];
    const map = { name:'nameLower', category:'category', origin:'origin', cost:'cost', price:'price',
                  horeca:'horeca', dett:'dett', gdo:'gdo', margin:'margin', stock:'stock', tara:'tara', peso:'peso' };
    rows.sort((a, b) => {
        const ak = map[key], av = a.dataset[ak], bv = b.dataset[ak];
        const an = parseFloat(av), bn = parseFloat(bv);
        if (!isNaN(an) && !isNaN(bn)) return sortDir[key] ? an - bn : bn - an;
        return sortDir[key] ? av.localeCompare(bv) : bv.localeCompare(av);
    });
    rows.forEach(r => tbody.appendChild(r));
}

// ── INLINE EDITING GENERICO ──────────────────────────────
document.querySelectorAll('.editable-cell').forEach(el => {
    el.addEventListener('click', function() {
        if (this.querySelector('input')) return;
        const span    = this;
        const field   = span.dataset.field;
        const id      = span.dataset.id;
        const isPrice = span.dataset.type === 'price';
        const original = span.textContent.trim();

        const input = document.createElement('input');
        input.type  = isPrice ? 'number' : 'text';
        if (isPrice) { input.step = '0.01'; input.min = '0'; }
        input.value = isPrice ? parseFloat(original.replace(',','.')) : original;
        input.style.cssText = 'width:'+(isPrice?'72':'90')+'px;text-align:'+(isPrice?'right':'left')+';font-family:inherit;font-size:inherit;font-weight:inherit;color:inherit;border:none;border-bottom:2px solid currentColor;background:transparent;outline:none;padding:0';
        span.textContent = '';
        span.appendChild(input);
        input.focus(); input.select();

        const save = async () => {
            const val = input.value.trim();
            if (val === '' || val === original) { span.textContent = original; return; }
            span.textContent = '…';
            try {
                const res  = await fetch(`/products/${id}/inline-update`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ field, value: val })
                });
                const data = await res.json();
                if (data.success) {
                    const v = isPrice ? parseFloat(data.value).toFixed(2).replace('.',',') : data.value;
                    span.textContent = v;
                    span.classList.add('flash-ok');
                    setTimeout(() => span.classList.remove('flash-ok'), 700);
                    // aggiorna data-val sulla riga per sort/csv
                    const row = span.closest('tr');
                    if (field === 'price')           { row.dataset.price  = data.value; }
                    if (field === 'cost_price')      { row.dataset.cost   = data.value; }
                    if (field === 'price_horeca')    { row.dataset.horeca = data.value; }
                    if (field === 'price_dettaglio') { row.dataset.dett   = data.value; }
                    if (field === 'price_gdo')       { row.dataset.gdo    = data.value; }
                } else { span.textContent = original; alert(data.message || 'Errore'); }
            } catch(e) { span.textContent = original; }
        };

        input.addEventListener('blur', save);
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter')  { e.preventDefault(); input.blur(); }
            if (e.key === 'Escape') { span.textContent = original; }
            if (e.key === 'Tab') {
                e.preventDefault(); input.blur();
                const row = span.closest('tr');
                const all = [...row.querySelectorAll('.editable-cell')];
                const next = all[all.indexOf(span) + (e.shiftKey ? -1 : 1)];
                if (next) setTimeout(() => next.click(), 60);
            }
        });
    });
});

// ── EDIT STOCK ───────────────────────────────────────────
function editStock(cell, id) {
    if (cell.querySelector('input')) return;
    const display  = cell.querySelector('.stock-display');
    const current  = parseFloat(cell.dataset.val) || 0;
    display.style.display = 'none';
    const input = document.createElement('input');
    input.type = 'number'; input.step = '0.001';
    input.value = current.toFixed(3);
    input.style.cssText = 'width:80px;text-align:right;margin:0;font-size:13px;font-family:inherit';
    cell.appendChild(input); input.focus(); input.select();

    const cancel = () => { if (input.parentNode) input.remove(); display.style.display = ''; cell.style.opacity='1'; };
    const save   = () => {
        const v = parseFloat(input.value);
        if (isNaN(v) || v === current) { cancel(); return; }
        cell.style.opacity = '0.5';
        fetch('/products/massive-update', {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({ids:[String(id)], action:'stock_set', value:v})
        }).then(r=>r.json()).then(d => {
            if (d.success) { cell.dataset.val = v; display.textContent = v.toLocaleString('it-IT',{minimumFractionDigits:2,maximumFractionDigits:2}); display.style.display=''; cell.style.opacity='1'; cell.classList.add('flash-ok'); setTimeout(()=>cell.classList.remove('flash-ok'),700); }
            else alert(d.message);
            cancel();
        }).catch(()=>cancel());
    };
    input.addEventListener('keydown', e => { if(e.key==='Enter'){e.preventDefault();save();} if(e.key==='Escape')cancel(); });
    input.addEventListener('blur', save);
}

// ── TOGGLE DISPONIBILITÀ ─────────────────────────────────
const dispCycle  = ['disponibile','su_richiesta','non_disponibile'];
const dispLabels = {
    'disponibile':     {label:'✅ OK',  bg:'#d4edda', color:'#2d6a4f'},
    'su_richiesta':    {label:'🔶 SR',  bg:'#fff3e0', color:'#e65100'},
    'non_disponibile': {label:'❌ No',  bg:'#fde8e8', color:'#c0392b'},
};
function toggleDisp(cell, id) {
    const cur  = cell.dataset.disp || 'disponibile';
    const next = dispCycle[(dispCycle.indexOf(cur)+1) % dispCycle.length];
    const cfg  = dispLabels[next];
    cell.style.opacity = '0.5';
    fetch('/products/massive-update', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({ids:[String(id)], action:'disp_set', value:next})
    }).then(r=>r.json()).then(d => {
        if (d.success) {
            cell.dataset.disp = next;
            cell.closest('tr').dataset.disp = next;
            const badge = cell.querySelector('.disp-badge');
            if (badge) { badge.textContent=cfg.label; badge.style.background=cfg.bg; badge.style.color=cfg.color; }
        }
        cell.style.opacity = '1';
    });
}
</script>

@endsection