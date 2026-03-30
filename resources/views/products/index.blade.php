@extends('layouts.app')
@section('page-title', 'Prodotti')
@section('content')

<style>
/* ── BASE ── */
.sku-badge { font-family:'DM Mono',monospace;font-size:10px;font-weight:700;background:#f0f4f1;border:1px solid #c3e6cb;padding:1px 6px;border-radius:5px;color:#2d6a4f }
.marg-badge { padding:2px 9px;border-radius:20px;font-size:12px;font-weight:700;font-family:'DM Mono',monospace }
.disp-badge { padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap }
.stato-ok   { background:var(--green-xl);color:var(--green) }
.stato-esau { background:#fde8e8;color:#c0392b }
.stato-sotto{ background:#fff3e0;color:#e65100 }
.sort-th    { cursor:pointer;user-select:none;white-space:nowrap }
.sort-th:hover { color:var(--green) }
.ec { cursor:pointer;border-bottom:1px dashed currentColor;border-radius:3px;padding:1px 3px;transition:background .15s }
.ec:hover { background:rgba(0,0,0,.05) }
.ec input { text-align:right;font-family:inherit;font-size:inherit;font-weight:inherit;color:inherit;border:none;border-bottom:2px solid currentColor;background:transparent;outline:none;padding:0 }
@keyframes fg { 0%,100%{background:transparent}50%{background:#d4edda} }
.fok { animation:fg .6s ease }
.massive-bar { display:none;background:var(--dark);color:#fff;padding:11px 16px;border-radius:10px;margin-bottom:12px;align-items:center;gap:8px;flex-wrap:wrap }
.massive-bar.active { display:flex }
.massive-bar input { background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.25);color:#fff;margin:0;width:80px }
.massive-bar button { padding:5px 11px;font-size:12px }

/* ── DESKTOP TABLE ── */
.prod-table { width:100%;min-width:860px }
.prod-table th,
.prod-table td { padding:8px 7px;vertical-align:middle }
.prod-table thead tr { border-bottom:2px solid var(--border) }
.prod-table tbody tr:hover { background:var(--bg) }

/* ── MOBILE CARDS ── */
@media (max-width: 768px) {
    .table-wrap { display:none }
    .cards-wrap  { display:block }
    .filter-bar  { gap:6px }
    .filter-bar select,
    .filter-bar input { max-width:100% !important;flex:1 1 140px }
}
@media (min-width: 769px) {
    .table-wrap { display:block }
    .cards-wrap  { display:none }
}

/* ── PRODUCT CARD (mobile) ── */
.prod-card {
    background:var(--card);border:1px solid var(--border);border-radius:12px;
    padding:12px 14px;margin-bottom:10px;
}
.prod-card .card-head { display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px }
.prod-card .card-name { font-weight:700;font-size:14px;color:var(--dark) }
.prod-card .card-meta { font-size:11px;color:var(--muted);margin-top:2px;display:flex;gap:6px;flex-wrap:wrap;align-items:center }
.price-grid { display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:6px;margin:8px 0 }
.price-box { background:var(--bg);border-radius:8px;padding:6px 8px;text-align:center }
.price-box .lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px }
.price-box .val { font-family:'DM Mono',monospace;font-size:14px;font-weight:700;cursor:pointer;border-bottom:1px dashed currentColor;border-radius:2px;padding:0 2px }
.price-box .val:hover { background:rgba(0,0,0,.05) }
.card-footer { display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:8px;padding-top:8px;border-top:1px solid var(--border) }
</style>

<div class="page-header">
    <div>
        <div class="page-title">🛒 Prodotti</div>
        <div class="page-sub">Editing inline su tutto — desktop e mobile</div>
    </div>
    <a href="{{ url('/products/create') }}" class="btn btn-primary">+ Nuovo</a>
</div>

{{-- MASSIVE BAR --}}
<div id="massiveBar" class="massive-bar">
    <span id="selCount" style="font-weight:700;font-size:14px;min-width:90px">0 selezionati</span>
    <span style="font-size:11px;color:rgba(255,255,255,.6)">Prezzo %</span>
    <input type="number" id="prezzoPerc" step="0.1" placeholder="±%">
    <button onclick="massiveAction('price_percent','prezzoPerc')" class="btn btn-primary">OK</button>
    <span style="font-size:11px;color:rgba(255,255,255,.6)">Costo %</span>
    <input type="number" id="costoPerc" step="0.1" placeholder="±%">
    <button onclick="massiveAction('cost_percent','costoPerc')" class="btn btn-primary">OK</button>
    <span style="font-size:11px;color:rgba(255,255,255,.6)">Scorta min</span>
    <input type="number" id="minStVal" step="0.001" min="0" placeholder="qty">
    <button onclick="massiveAction('min_stock','minStVal')" class="btn btn-primary">OK</button>
    <button onclick="exportCSV()" class="btn btn-secondary">📥 CSV</button>
    <button onclick="deselectAll()" class="btn btn-secondary" style="margin-left:auto">✕</button>
</div>

{{-- FILTRI --}}
<div class="card filter-bar" style="padding:10px 14px;margin-bottom:12px;display:flex;gap:8px;flex-wrap:wrap;align-items:center">
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

{{-- ════════════════════════════════════════════
     DESKTOP TABLE
════════════════════════════════════════════ --}}
<div class="card table-wrap" style="padding:0;overflow-x:auto">
<table class="prod-table" id="prodTable">
<thead>
<tr>
    <th style="width:36px;text-align:center"><input type="checkbox" id="selAll" onchange="toggleAll(this.checked)"></th>
    <th class="sort-th" onclick="sortBy('name')">Nome / SKU / Origine ↕</th>
    <th class="sort-th" onclick="sortBy('cost')"  style="width:74px;text-align:right">Costo ↕</th>
    <th class="sort-th" onclick="sortBy('price')" style="width:78px;text-align:right;color:#e67e22">Base € ↕</th>
    <th class="sort-th" onclick="sortBy('horeca')" style="width:78px;text-align:right;color:#2980b9">HoReCa € ↕</th>
    <th class="sort-th" onclick="sortBy('dett')"  style="width:78px;text-align:right;color:#27ae60">Dett. € ↕</th>
    <th class="sort-th" onclick="sortBy('gdo')"   style="width:78px;text-align:right;color:#8e44ad">GDO € ↕</th>
    <th class="sort-th" onclick="sortBy('margin')" style="width:72px;text-align:center">Marg. ↕</th>
    <th class="sort-th" onclick="sortBy('stock')" style="width:80px;text-align:right">Stock ↕</th>
    <th style="width:72px;text-align:center">Disp.</th>
    <th style="width:65px;text-align:center">Mod.</th>
    <th style="width:62px;text-align:center">Stato</th>
    <th style="width:70px;text-align:center">Azioni</th>
</tr>
</thead>
<tbody>
@forelse($products as $p)
@php
    $cost   = (float)($p->cost_price ?? 0);
    $price  = (float)($p->price ?? 0);
    $horeca = (float)($p->price_horeca ?? $price);
    $dett   = (float)($p->price_dettaglio ?? $price);
    $gdo    = (float)($p->price_gdo ?? $price);
    $margin = $price > 0 ? (($price - $cost) / $price) * 100 : 0;
    $sqty   = (float)($p->stock->quantity ?? 0);
    $smin   = (float)($p->stock->min_stock ?? 0);
    $stato  = $sqty <= 0 ? 'esaurito' : ($price < $cost ? 'sottocosto' : 'ok');
    $disp   = $p->disponibilita ?? 'disponibile';
    $mc     = $margin >= 40 ? '#27ae60' : ($margin >= 20 ? '#f39c12' : '#c0392b');
    $mb     = $margin >= 40 ? 'var(--green-xl)' : ($margin >= 20 ? '#fff3e0' : '#fde8e8');
@endphp
<tr class="product-row"
    data-id="{{ $p->id }}"
    data-name="{{ $p->name }}" data-name-lower="{{ strtolower($p->name) }}"
    data-category="{{ strtolower($p->category ?? '') }}"
    data-origin="{{ strtolower($p->origin ?? '') }}"
    data-cost="{{ $cost }}" data-price="{{ $price }}"
    data-horeca="{{ $horeca }}" data-dett="{{ $dett }}" data-gdo="{{ $gdo }}"
    data-margin="{{ round($margin,1) }}" data-stock="{{ $sqty }}"
    data-stato="{{ $stato }}" data-disp="{{ $disp }}">

    <td style="text-align:center"><input type="checkbox" class="row-check" data-id="{{ $p->id }}" onchange="updateSel()"></td>

    {{-- NOME --}}
    <td>
        <div style="font-weight:700;font-size:13px;color:var(--dark)">{{ $p->name }}</div>
        <div style="display:flex;gap:5px;margin-top:2px;align-items:center;flex-wrap:wrap">
            <span class="sku-badge">{{ $p->sku ?? '—' }}</span>
            <span class="ec" style="font-size:11px;color:var(--muted)" data-field="origin" data-id="{{ $p->id }}" data-type="text">{{ $p->origin ?? '—' }}</span>
            <span style="font-size:11px;color:var(--muted)">· {{ $p->category }}</span>
        </div>
    </td>

    {{-- COSTO --}}
    <td style="text-align:right">
        <span class="ec" style="font-size:12px;color:var(--muted);font-family:'DM Mono',monospace"
            data-field="cost_price" data-id="{{ $p->id }}" data-type="price">
            {{ number_format($cost,2,',','.') }}
        </span>
    </td>

    {{-- BASE --}}
    <td style="text-align:right">
        <span class="ec" style="font-family:'DM Mono',monospace;font-size:13px;font-weight:700;color:#e67e22"
            data-field="price" data-id="{{ $p->id }}" data-type="price">
            {{ number_format($price,2,',','.') }}
        </span>
    </td>

    {{-- HORECA --}}
    <td style="text-align:right">
        <span class="ec" style="font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:#2980b9"
            data-field="price_horeca" data-id="{{ $p->id }}" data-type="price">
            {{ number_format($horeca,2,',','.') }}
        </span>
    </td>

    {{-- DETTAGLIO --}}
    <td style="text-align:right">
        <span class="ec" style="font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:#27ae60"
            data-field="price_dettaglio" data-id="{{ $p->id }}" data-type="price">
            {{ number_format($dett,2,',','.') }}
        </span>
    </td>

    {{-- GDO --}}
    <td style="text-align:right">
        <span class="ec" style="font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:#8e44ad"
            data-field="price_gdo" data-id="{{ $p->id }}" data-type="price">
            {{ number_format($gdo,2,',','.') }}
        </span>
    </td>

    {{-- MARGINE --}}
    <td style="text-align:center">
        <span class="marg-badge" style="background:{{ $mb }};color:{{ $mc }}">{{ number_format($margin,1,',','.') }}%</span>
    </td>

    {{-- STOCK --}}
    <td style="text-align:right;cursor:pointer" data-val="{{ $sqty }}" onclick="editStock(this,{{ $p->id }})">
        <span class="stock-display" style="font-family:'DM Mono',monospace;font-size:13px;color:{{ $sqty>$smin?'var(--dark)':'#c0392b' }};font-weight:{{ $sqty>$smin?'400':'700' }}">
            {{ number_format($sqty,2,',','.') }}
        </span>
        <div style="font-size:10px;color:var(--muted)">{{ $p->unita_stock }}</div>
    </td>

    {{-- DISP --}}
    <td style="text-align:center;cursor:pointer" data-disp="{{ $disp }}" onclick="toggleDisp(this,{{ $p->id }})">
        @if($disp=='disponibile')
            <span class="disp-badge" style="background:#d4edda;color:#2d6a4f">✅ OK</span>
        @elseif($disp=='su_richiesta')
            <span class="disp-badge" style="background:#fff3e0;color:#e65100">🔶 SR</span>
        @else
            <span class="disp-badge" style="background:#fde8e8;color:#c0392b">❌ No</span>
        @endif
    </td>

    {{-- MOD --}}
    <td style="text-align:center">
        <span style="font-size:10px;background:var(--bg);color:var(--muted);padding:2px 6px;border-radius:6px">{{ $p->modalita_vendita }}</span>
    </td>

    {{-- STATO --}}
    <td style="text-align:center">
        @if($stato=='ok')
            <span style="padding:2px 7px;border-radius:20px;font-size:11px;font-weight:700" class="stato-ok">✓ OK</span>
        @elseif($stato=='esaurito')
            <span style="padding:2px 7px;border-radius:20px;font-size:11px;font-weight:700" class="stato-esau">⚠ Esau.</span>
        @else
            <span style="padding:2px 7px;border-radius:20px;font-size:11px;font-weight:700" class="stato-sotto">⚠ Costo</span>
        @endif
    </td>

    {{-- AZIONI --}}
    <td style="text-align:center">
        <a href="{{ url('/products/'.$p->id.'/edit') }}"
           style="font-size:11px;background:var(--green-xl);color:var(--green);padding:3px 8px;border-radius:6px;text-decoration:none;font-weight:600">✏️ Edit</a>
    </td>
</tr>
@empty
<tr><td colspan="13" style="text-align:center;padding:48px;color:var(--muted)">
    Nessun prodotto. <a href="{{ url('/products/create') }}">Aggiungi →</a>
</td></tr>
@endforelse
</tbody>
</table>
</div>

{{-- ════════════════════════════════════════════
     MOBILE CARDS
════════════════════════════════════════════ --}}
<div class="cards-wrap" id="cardsWrap">
@foreach($products as $p)
@php
    $cost   = (float)($p->cost_price ?? 0);
    $price  = (float)($p->price ?? 0);
    $horeca = (float)($p->price_horeca ?? $price);
    $dett   = (float)($p->price_dettaglio ?? $price);
    $gdo    = (float)($p->price_gdo ?? $price);
    $margin = $price > 0 ? (($price - $cost) / $price) * 100 : 0;
    $sqty   = (float)($p->stock->quantity ?? 0);
    $smin   = (float)($p->stock->min_stock ?? 0);
    $stato  = $sqty <= 0 ? 'esaurito' : ($price < $cost ? 'sottocosto' : 'ok');
    $disp   = $p->disponibilita ?? 'disponibile';
    $mc     = $margin >= 40 ? '#27ae60' : ($margin >= 20 ? '#f39c12' : '#c0392b');
    $mb     = $margin >= 40 ? 'var(--green-xl)' : ($margin >= 20 ? '#fff3e0' : '#fde8e8');
@endphp
<div class="prod-card mobile-card"
    data-id="{{ $p->id }}"
    data-name-lower="{{ strtolower($p->name) }}"
    data-category="{{ strtolower($p->category ?? '') }}"
    data-origin="{{ strtolower($p->origin ?? '') }}"
    data-stato="{{ $stato }}"
    data-disp="{{ $disp }}">

    <div class="card-head">
        <div>
            <div class="card-name">{{ $p->name }}</div>
            <div class="card-meta">
                <span class="sku-badge">{{ $p->sku }}</span>
                <span>{{ $p->origin ?? '—' }}</span>
                <span>· {{ $p->category }}</span>
            </div>
        </div>
        <div style="display:flex;gap:6px;align-items:center">
            <span class="marg-badge" style="background:{{ $mb }};color:{{ $mc }}">{{ number_format($margin,1,',','.') }}%</span>
            <a href="{{ url('/products/'.$p->id.'/edit') }}"
               style="font-size:11px;background:var(--green-xl);color:var(--green);padding:3px 8px;border-radius:6px;text-decoration:none;font-weight:600">✏️</a>
        </div>
    </div>

    {{-- PRICE GRID --}}
    <div class="price-grid">
        <div class="price-box">
            <div class="lbl" style="color:#e67e22">Base</div>
            <div class="val ec" style="color:#e67e22" data-field="price" data-id="{{ $p->id }}" data-type="price">
                {{ number_format($price,2,',','.') }}
            </div>
        </div>
        <div class="price-box">
            <div class="lbl" style="color:#2980b9">HoReCa</div>
            <div class="val ec" style="color:#2980b9" data-field="price_horeca" data-id="{{ $p->id }}" data-type="price">
                {{ number_format($horeca,2,',','.') }}
            </div>
        </div>
        <div class="price-box">
            <div class="lbl" style="color:#27ae60">Dett.</div>
            <div class="val ec" style="color:#27ae60" data-field="price_dettaglio" data-id="{{ $p->id }}" data-type="price">
                {{ number_format($dett,2,',','.') }}
            </div>
        </div>
        <div class="price-box">
            <div class="lbl" style="color:#8e44ad">GDO</div>
            <div class="val ec" style="color:#8e44ad" data-field="price_gdo" data-id="{{ $p->id }}" data-type="price">
                {{ number_format($gdo,2,',','.') }}
            </div>
        </div>
    </div>

    <div class="card-footer">
        {{-- STOCK --}}
        <span style="font-size:12px;color:var(--muted)">Stock:</span>
        <span style="cursor:pointer;font-family:'DM Mono',monospace;font-size:13px;font-weight:700;color:{{ $sqty>$smin?'var(--green)':'#c0392b' }}"
            data-val="{{ $sqty }}" onclick="editStock(this,{{ $p->id }})">
            <span class="stock-display">{{ number_format($sqty,2,',','.') }}</span> {{ $p->unita_stock }}
        </span>

        {{-- DISP --}}
        <span style="margin-left:auto;cursor:pointer" data-disp="{{ $disp }}" onclick="toggleDisp(this,{{ $p->id }})">
            @if($disp=='disponibile')
                <span class="disp-badge" style="background:#d4edda;color:#2d6a4f">✅ OK</span>
            @elseif($disp=='su_richiesta')
                <span class="disp-badge" style="background:#fff3e0;color:#e65100">🔶 SR</span>
            @else
                <span class="disp-badge" style="background:#fde8e8;color:#c0392b">❌ No</span>
            @endif
        </span>

        {{-- STATO --}}
        @if($stato=='ok')
            <span style="padding:2px 7px;border-radius:20px;font-size:11px;font-weight:700" class="stato-ok">✓ OK</span>
        @elseif($stato=='esaurito')
            <span style="padding:2px 7px;border-radius:20px;font-size:11px;font-weight:700" class="stato-esau">⚠ Esau.</span>
        @else
            <span style="padding:2px 7px;border-radius:20px;font-size:11px;font-weight:700" class="stato-sotto">⚠ Costo</span>
        @endif
    </div>
</div>
@endforeach
</div>

{{-- LEGENDA --}}
<div style="display:flex;gap:12px;margin-top:10px;font-size:11px;color:var(--muted);flex-wrap:wrap">
    <span>💡 Clicca su qualsiasi prezzo per modificarlo — TAB per campo successivo</span>
    <span style="color:#e67e22">■ Base</span>
    <span style="color:#2980b9">■ HoReCa</span>
    <span style="color:#27ae60">■ Dett.</span>
    <span style="color:#8e44ad">■ GDO</span>
</div>

<script>
const CSRF = '{{ csrf_token() }}';

// ── FILTRI ───────────────────────────────────────────────
function filterRows() {
    const q  = document.getElementById('fSearch').value.toLowerCase();
    const o  = document.getElementById('fOrigine').value;
    const c  = document.getElementById('fCat').value;
    const st = document.getElementById('fStato').value;
    const dp = document.getElementById('fDisp').value;
    let n = 0;
    // tabella desktop
    document.querySelectorAll('.product-row').forEach(r => {
        const ok = (!q||r.dataset.nameLower.includes(q))&&(!o||r.dataset.origin===o)&&(!c||r.dataset.category===c)&&(!st||r.dataset.stato===st)&&(!dp||r.dataset.disp===dp);
        r.style.display = ok?'':'none';
        if(ok) n++;
    });
    // card mobile
    document.querySelectorAll('.mobile-card').forEach(r => {
        const ok = (!q||r.dataset.nameLower.includes(q))&&(!o||r.dataset.origin===o)&&(!c||r.dataset.category===c)&&(!st||r.dataset.stato===st)&&(!dp||r.dataset.disp===dp);
        r.style.display = ok?'':'none';
    });
    document.getElementById('countLabel').textContent = n + ' prodotti';
}
function resetFiltri() {
    ['fSearch','fOrigine','fCat','fStato','fDisp'].forEach(id=>{const el=document.getElementById(id);el.value='';});
    filterRows();
}
document.getElementById('fSearch').addEventListener('input',filterRows);
['fOrigine','fCat','fStato','fDisp'].forEach(id=>document.getElementById(id).addEventListener('change',filterRows));
filterRows();

// ── SELEZIONE ────────────────────────────────────────────
function getSelected(){return[...document.querySelectorAll('.row-check:checked')].map(c=>c.dataset.id);}
function toggleAll(checked){document.querySelectorAll('.product-row:not([style*="display: none"]) .row-check').forEach(c=>c.checked=checked);updateSel();}
function deselectAll(){document.querySelectorAll('.row-check').forEach(c=>c.checked=false);document.getElementById('selAll').checked=false;updateSel();}
function updateSel(){const ids=getSelected();document.getElementById('massiveBar').classList.toggle('active',ids.length>0);document.getElementById('selCount').textContent=ids.length+' selezionati';}

// ── MASSIVE ──────────────────────────────────────────────
function massiveAction(action,inputId){
    const ids=getSelected(),val=parseFloat(document.getElementById(inputId).value);
    if(!ids.length||isNaN(val))return;
    fetch('/products/massive-update',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({ids,action,value:val})})
    .then(r=>r.json()).then(d=>{if(d.success)location.reload();else alert(d.message);});
}

// ── CSV ──────────────────────────────────────────────────
function exportCSV(){
    const rows=[...document.querySelectorAll('.product-row')].filter(r=>r.style.display!=='none');
    const lines=[['Nome','Origine','Categoria','Costo','Base','HoReCa','Dettaglio','GDO','Margine%','Stock','Disp'].join(';')];
    rows.forEach(r=>lines.push(['"'+r.dataset.name+'"',r.dataset.origin,r.dataset.category,r.dataset.cost,r.dataset.price,r.dataset.horeca,r.dataset.dett,r.dataset.gdo,r.dataset.margin,r.dataset.stock,r.dataset.disp].join(';')));
    const a=document.createElement('a');
    a.href=URL.createObjectURL(new Blob(['\uFEFF'+lines.join('\n')],{type:'text/csv;charset=utf-8;'}));
    a.download='prodotti_'+new Date().toISOString().slice(0,10)+'.csv';a.click();
}

// ── SORT ─────────────────────────────────────────────────
let sortDir={};
function sortBy(key){
    sortDir[key]=!sortDir[key];
    const tbody=document.querySelector('#prodTable tbody');
    const rows=[...tbody.querySelectorAll('.product-row')];
    const map={name:'nameLower',category:'category',origin:'origin',cost:'cost',price:'price',horeca:'horeca',dett:'dett',gdo:'gdo',margin:'margin',stock:'stock'};
    rows.sort((a,b)=>{const ak=map[key],av=a.dataset[ak],bv=b.dataset[ak];const an=parseFloat(av),bn=parseFloat(bv);if(!isNaN(an)&&!isNaN(bn))return sortDir[key]?an-bn:bn-an;return sortDir[key]?av.localeCompare(bv):bv.localeCompare(av);});
    rows.forEach(r=>tbody.appendChild(r));
}

// ── INLINE EDIT ──────────────────────────────────────────
document.querySelectorAll('.ec').forEach(el=>{
    el.addEventListener('click',function(){
        if(this.querySelector('input'))return;
        const span=this,field=span.dataset.field,id=span.dataset.id,isP=span.dataset.type==='price';
        const original=span.textContent.trim();
        const input=document.createElement('input');
        input.type=isP?'number':'text';
        if(isP){input.step='0.01';input.min='0';}
        input.value=isP?parseFloat(original.replace(',','.')):original;
        input.style.cssText='width:'+(isP?'72':'90')+'px;text-align:'+(isP?'right':'left')+';font-family:inherit;font-size:inherit;font-weight:inherit;color:inherit;border:none;border-bottom:2px solid currentColor;background:transparent;outline:none;padding:0';
        span.textContent='';span.appendChild(input);input.focus();input.select();
        const save=async()=>{
            const val=input.value.trim();
            if(val===''||val===original){span.textContent=original;return;}
            span.textContent='…';
            try{
                const res=await fetch(`/products/${id}/inline-update`,{method:'PATCH',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({field,value:val})});
                const data=await res.json();
                if(data.success){
                    const v=isP?parseFloat(data.value).toFixed(2).replace('.',','):data.value;
                    span.textContent=v;span.classList.add('fok');setTimeout(()=>span.classList.remove('fok'),700);
                    // aggiorna data sul row per sort/csv
                    const row=span.closest('tr')||span.closest('.product-row');
                    if(row){
                        if(field==='price')row.dataset.price=data.value;
                        if(field==='cost_price')row.dataset.cost=data.value;
                        if(field==='price_horeca')row.dataset.horeca=data.value;
                        if(field==='price_dettaglio')row.dataset.dett=data.value;
                        if(field==='price_gdo')row.dataset.gdo=data.value;
                    }
                }else{span.textContent=original;alert(data.message||'Errore');}
            }catch(e){span.textContent=original;}
        };
        input.addEventListener('blur',save);
        input.addEventListener('keydown',e=>{
            if(e.key==='Enter'){e.preventDefault();input.blur();}
            if(e.key==='Escape'){span.textContent=original;}
            if(e.key==='Tab'){
                e.preventDefault();input.blur();
                const row=span.closest('tr')||span.closest('.prod-card');
                if(!row)return;
                const all=[...row.querySelectorAll('.ec')];
                const next=all[all.indexOf(span)+(e.shiftKey?-1:1)];
                if(next)setTimeout(()=>next.click(),60);
            }
        });
    });
});

// ── STOCK ────────────────────────────────────────────────
function editStock(cell,id){
    if(cell.querySelector('input'))return;
    const display=cell.querySelector('.stock-display'),current=parseFloat(cell.dataset.val)||0;
    display.style.display='none';
    const input=document.createElement('input');
    input.type='number';input.step='0.001';input.value=current.toFixed(3);
    input.style.cssText='width:80px;text-align:right;margin:0;font-size:13px;font-family:inherit';
    cell.appendChild(input);input.focus();input.select();
    const cancel=()=>{if(input.parentNode)input.remove();display.style.display='';cell.style.opacity='1';};
    const save=()=>{
        const v=parseFloat(input.value);if(isNaN(v)||v===current){cancel();return;}
        cell.style.opacity='0.5';
        fetch('/products/massive-update',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({ids:[String(id)],action:'stock_set',value:v})})
        .then(r=>r.json()).then(d=>{if(d.success){cell.dataset.val=v;display.textContent=v.toLocaleString('it-IT',{minimumFractionDigits:2,maximumFractionDigits:2});display.style.display='';cell.style.opacity='1';cell.classList.add('fok');setTimeout(()=>cell.classList.remove('fok'),700);}else alert(d.message);cancel();}).catch(()=>cancel());
    };
    input.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();save();}if(e.key==='Escape')cancel();});
    input.addEventListener('blur',save);
}

// ── TOGGLE DISP ──────────────────────────────────────────
const dispCycle=['disponibile','su_richiesta','non_disponibile'];
const dispLabels={disponibile:{label:'✅ OK',bg:'#d4edda',color:'#2d6a4f'},su_richiesta:{label:'🔶 SR',bg:'#fff3e0',color:'#e65100'},non_disponibile:{label:'❌ No',bg:'#fde8e8',color:'#c0392b'}};
function toggleDisp(cell,id){
    const cur=cell.dataset.disp||'disponibile';
    const next=dispCycle[(dispCycle.indexOf(cur)+1)%dispCycle.length];
    const cfg=dispLabels[next];
    cell.style.opacity='0.5';
    fetch('/products/massive-update',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({ids:[String(id)],action:'disp_set',value:next})})
    .then(r=>r.json()).then(d=>{
        if(d.success){cell.dataset.disp=next;cell.closest('tr')&&(cell.closest('tr').dataset.disp=next);cell.closest('.prod-card')&&(cell.closest('.prod-card').dataset.disp=next);const badge=cell.querySelector('.disp-badge');if(badge){badge.textContent=cfg.label;badge.style.background=cfg.bg;badge.style.color=cfg.color;}}
        cell.style.opacity='1';
    });
}
</script>

@endsection