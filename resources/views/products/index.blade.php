@extends('layouts.app')

@section('page-title', 'Prodotti')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">🛒 Prodotti</div>
        <div class="page-sub">Catalogo prodotti e prezzi listino</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ url('/products/create') }}" class="btn btn-primary">+ Nuovo Prodotto</a>
    </div>
</div>

{{-- FILTRI --}}
<div class="card" style="padding:12px 16px;margin-bottom:16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
    <input type="text" id="searchInput" placeholder="🔍 Cerca per nome..." style="max-width:220px;margin:0">
    <select id="filterOrigin" style="max-width:140px;margin:0">
        <option value="">Tutte le origin</option>
        @foreach($origins as $o)
            <option value="{{ $o }}">{{ $o }}</option>
        @endforeach
    </select>
    <select id="filterCat" style="max-width:160px;margin:0">
        <option value="">Tutte le categorie</option>
        @foreach($categories as $cat)
            <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
        @endforeach
    </select>
    <select id="filterUM" style="max-width:120px;margin:0">
        <option value="">Tutte UM</option>
        <option value="€/kg">€/kg</option>
        <option value="€/collo">€/collo</option>
        <option value="€/pz">€/pz</option>
    </select>
    <select id="filterDisp" style="max-width:180px;margin:0">
        <option value="">Tutte le disponibilità</option>
        <option value="disponibile">Disponibile</option>
        <option value="su_richiesta">Su richiesta</option>
        <option value="non_disponibile">Non disponibile</option>
    </select>
    <button onclick="resetFilters()" style="margin:0;padding:6px 12px;font-size:12px">✕ Reset</button>
    <span id="countLabel" style="font-size:12px;color:var(--muted);margin-left:auto"></span>
</div>

{{-- AZIONI MASSIVE --}}
<div id="massiveBar" style="display:none;background:var(--green-xl);border:1px solid var(--green-l);border-radius:8px;padding:10px 16px;margin-bottom:12px;display:none;align-items:center;gap:12px;flex-wrap:wrap">
    <span id="selectedCount" style="font-size:13px;font-weight:600;color:var(--green)">0 selezionati</span>
    <select id="massAction" style="margin:0;max-width:200px;font-size:12px">
        <option value="">— Azione massiva —</option>
        <option value="disp_set|disponibile">✓ Imposta disponibile</option>
        <option value="disp_set|su_richiesta">? Imposta su richiesta</option>
        <option value="disp_set|non_disponibile">✗ Imposta non disponibile</option>
        <option value="price_percent|">% Modifica prezzo base %</option>
        <option value="price_set|">€ Imposta prezzo base fisso</option>
    </select>
    <input type="number" id="massValue" placeholder="valore" step="0.01" style="max-width:100px;margin:0;font-size:12px;display:none">
    <button onclick="applyMassive()" style="margin:0;padding:6px 14px;font-size:12px;background:var(--green);color:#fff;border:none;border-radius:6px;cursor:pointer">Applica</button>
    <button onclick="deselectAll()" style="margin:0;padding:6px 10px;font-size:12px">Annulla</button>
</div>

{{-- TABELLA --}}
<div class="card" style="padding:0;overflow-x:auto">
<table id="prodTable" style="table-layout:auto;width:100%;min-width:1100px">
    <thead>
        <tr>
            <th style="width:32px"><input type="checkbox" id="checkAll" onchange="toggleAll(this.checked)"></th>
            <th>Nome / SKU / Origine</th>
            <th style="width:75px;text-align:right">Costo</th>
            <th style="width:80px;text-align:right" title="Prezzo base">Base €</th>
            <th style="width:80px;text-align:right;color:#2980b9" title="HoReCa">HoReCa €</th>
            <th style="width:80px;text-align:right;color:#27ae60" title="Dettaglio">Dett. €</th>
            <th style="width:80px;text-align:right;color:#8e44ad" title="GDO">GDO €</th>
            <th style="width:70px;text-align:center">Margine</th>
            <th style="width:80px;text-align:right">Stock</th>
            <th style="width:80px;text-align:center">Disp.</th>
            <th style="width:65px;text-align:center">Modalità</th>
            <th style="width:60px;text-align:center">Stato</th>
            <th style="width:90px;text-align:center">Azioni</th>
        </tr>
    </thead>
    <tbody>
    @forelse($products as $p)
    @php
        $price     = $p->price ?? 0;
        $cost      = $p->cost_price ?? 0;
        $margine   = $price > 0 ? round((($price - $cost) / $price) * 100, 1) : 0;
        $margColor = $margine >= 40 ? '#27ae60' : ($margine >= 20 ? '#f39c12' : '#e74c3c');
        $stock     = $p->stock;
        $stockQty  = $stock ? $stock->quantity : 0;
        $minStock  = $stock ? $stock->min_stock : 0;
        $stockOk   = $stockQty > $minStock;
    @endphp
    <tr class="prod-row"
        data-id="{{ $p->id }}"
        data-nome="{{ strtolower($p->name) }}"
        data-cat="{{ $p->category }}"
        data-origin="{{ $p->origin }}"
        data-um="{{ $p->unita_prezzo }}"
        data-disp="{{ $p->disponibilita }}">

        {{-- CHECKBOX --}}
        <td><input type="checkbox" class="row-check" value="{{ $p->id }}" onchange="updateSelected()"></td>

        {{-- NOME / SKU / ORIGINE --}}
        <td>
            <div style="font-weight:600;font-size:13px">{{ $p->name }}</div>
            <div style="display:flex;gap:6px;margin-top:2px;align-items:center">
                <span style="background:var(--green-xl);color:var(--green);font-size:10px;font-weight:700;padding:1px 6px;border-radius:4px;font-family:'DM Mono',monospace">{{ $p->sku }}</span>
                <span class="editable" data-field="origin" data-id="{{ $p->id }}"
                    style="cursor:pointer;font-size:11px;color:var(--muted);border-bottom:1px dashed var(--border)">
                    {{ $p->origin ?? '—' }}
                </span>
            </div>
        </td>

        {{-- COSTO --}}
        <td style="text-align:right">
            <span class="editable price-cell" data-field="cost_price" data-id="{{ $p->id }}"
                style="cursor:pointer;font-size:12px;color:var(--muted);font-family:'DM Mono',monospace;border-bottom:1px dashed var(--border)">
                {{ number_format($cost, 2) }}
            </span>
        </td>

        {{-- PREZZO BASE --}}
        <td style="text-align:right">
            <span class="editable price-cell" data-field="price" data-id="{{ $p->id }}"
                style="cursor:pointer;font-family:'DM Mono',monospace;font-size:13px;font-weight:700;color:#e67e22;border-bottom:1px dashed #e67e22">
                {{ number_format($price, 2) }}
            </span>
        </td>

        {{-- HORECA --}}
        <td style="text-align:right">
            <span class="editable price-cell" data-field="price_horeca" data-id="{{ $p->id }}"
                style="cursor:pointer;font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:#2980b9;border-bottom:1px dashed #2980b9">
                {{ number_format($p->price_horeca ?? $price, 2) }}
            </span>
        </td>

        {{-- DETTAGLIO --}}
        <td style="text-align:right">
            <span class="editable price-cell" data-field="price_dettaglio" data-id="{{ $p->id }}"
                style="cursor:pointer;font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:#27ae60;border-bottom:1px dashed #27ae60">
                {{ number_format($p->price_dettaglio ?? $price, 2) }}
            </span>
        </td>

        {{-- GDO --}}
        <td style="text-align:right">
            <span class="editable price-cell" data-field="price_gdo" data-id="{{ $p->id }}"
                style="cursor:pointer;font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:#8e44ad;border-bottom:1px dashed #8e44ad">
                {{ number_format($p->price_gdo ?? $price, 2) }}
            </span>
        </td>

        {{-- MARGINE --}}
        <td style="text-align:center">
            <span style="background:{{ $margColor }}22;color:{{ $margColor }};padding:2px 8px;border-radius:12px;font-size:12px;font-weight:700">
                {{ $margine }}%
            </span>
        </td>

        {{-- STOCK --}}
        <td style="text-align:right;font-size:12px;font-family:'DM Mono',monospace;color:{{ $stockOk ? 'var(--dark)' : '#e74c3c' }}">
            {{ number_format($stockQty, 2) }}<br>
            <span style="font-size:10px;color:var(--muted)">{{ $p->unita_stock }}</span>
        </td>

        {{-- DISPONIBILITÀ --}}
        <td style="text-align:center">
            @if($p->disponibilita === 'disponibile')
                <span style="background:#eafaf1;color:#27ae60;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600">✓ OK</span>
            @elseif($p->disponibilita === 'su_richiesta')
                <span style="background:#fef9e7;color:#f39c12;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600">SR</span>
            @else
                <span style="background:#fdedec;color:#e74c3c;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600">✗ No</span>
            @endif
        </td>

        {{-- MODALITÀ --}}
        <td style="text-align:center">
            <span style="font-size:10px;background:var(--bg);color:var(--muted);padding:2px 6px;border-radius:8px">
                {{ $p->modalita_vendita }}
            </span>
        </td>

        {{-- STATO --}}
        <td style="text-align:center">
            @if($stockOk)
                <span style="color:#27ae60;font-size:16px">✓</span><br>
                <span style="font-size:10px;color:#27ae60">OK</span>
            @else
                <span style="color:#e74c3c;font-size:16px">✗</span><br>
                <span style="font-size:10px;color:#e74c3c">{{ $stockQty <= 0 ? 'Esaurito' : 'Basso' }}</span>
            @endif
        </td>

        {{-- AZIONI --}}
        <td style="text-align:center;white-space:nowrap">
            <a href="{{ url('/products/'.$p->id.'/edit') }}"
               style="font-size:11px;background:var(--green-xl);color:var(--green);padding:3px 8px;border-radius:6px;text-decoration:none;font-weight:600">
                Modifica
            </a>
        </td>

    </tr>
    @empty
    <tr><td colspan="13" style="text-align:center;padding:48px;color:var(--muted)">Nessun prodotto trovato.</td></tr>
    @endforelse
    </tbody>
</table>
</div>

{{-- LEGENDA --}}
<div style="display:flex;gap:16px;margin-top:10px;font-size:11px;color:var(--muted);flex-wrap:wrap">
    <span>💡 Clicca su qualsiasi prezzo o origine per modificarlo inline — TAB per passare al campo successivo</span>
    <span style="color:#e67e22">■ Base</span>
    <span style="color:#2980b9">■ HoReCa</span>
    <span style="color:#27ae60">■ Dettaglio</span>
    <span style="color:#8e44ad">■ GDO</span>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

// FILTRI
const searchInput  = document.getElementById('searchInput');
const filterOrigin = document.getElementById('filterOrigin');
const filterCat    = document.getElementById('filterCat');
const filterUM     = document.getElementById('filterUM');
const filterDisp   = document.getElementById('filterDisp');
const countLabel   = document.getElementById('countLabel');

function filterRows() {
    const q = searchInput.value.toLowerCase();
    const o = filterOrigin.value;
    const c = filterCat.value;
    const u = filterUM.value;
    const d = filterDisp.value;
    let n = 0;
    document.querySelectorAll('.prod-row').forEach(row => {
        const ok = (!q || row.dataset.nome.includes(q))
                && (!o || row.dataset.origin === o)
                && (!c || row.dataset.cat === c)
                && (!u || row.dataset.um === u)
                && (!d || row.dataset.disp === d);
        row.style.display = ok ? '' : 'none';
        if (ok) n++;
    });
    countLabel.textContent = n + ' prodotti';
}
function resetFilters() {
    searchInput.value = '';
    filterOrigin.value = '';
    filterCat.value = '';
    filterUM.value = '';
    filterDisp.value = '';
    filterRows();
}
[searchInput, filterOrigin, filterCat, filterUM, filterDisp].forEach(el => el.addEventListener('input', filterRows));
[filterOrigin, filterCat, filterUM, filterDisp].forEach(el => el.addEventListener('change', filterRows));
filterRows();

// SELEZIONE MULTIPLA
function toggleAll(checked) {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = checked);
    updateSelected();
}
function deselectAll() {
    document.getElementById('checkAll').checked = false;
    toggleAll(false);
}
function updateSelected() {
    const sel = [...document.querySelectorAll('.row-check:checked')];
    const bar = document.getElementById('massiveBar');
    document.getElementById('selectedCount').textContent = sel.length + ' selezionati';
    bar.style.display = sel.length > 0 ? 'flex' : 'none';
}
document.getElementById('massAction').addEventListener('change', function() {
    const needsVal = this.value.includes('percent|') || this.value.includes('set|') && !this.value.includes('disp_set');
    document.getElementById('massValue').style.display = needsVal ? '' : 'none';
});
async function applyMassive() {
    const ids    = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
    const raw    = document.getElementById('massAction').value;
    if (!raw || ids.length === 0) return;
    const [action, val] = raw.split('|');
    const value  = val || document.getElementById('massValue').value;
    const res = await fetch('/products/massive-update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ ids, action, value })
    });
    const data = await res.json();
    if (data.success) location.reload();
    else alert('Errore: ' + (data.message || 'operazione fallita'));
}

// INLINE EDITING
document.querySelectorAll('.editable').forEach(el => {
    el.addEventListener('click', function () {
        if (this.querySelector('input')) return;
        const original = this.textContent.trim();
        const field    = this.dataset.field;
        const id       = this.dataset.id;
        const isPrice  = this.classList.contains('price-cell');
        const span     = this;

        const input = document.createElement('input');
        input.type  = isPrice ? 'number' : 'text';
        if (isPrice) { input.step = '0.01'; input.min = '0'; }
        input.value = isPrice ? parseFloat(original.replace(',', '.')) : original;
        input.style.cssText = 'width:68px;text-align:right;font-family:inherit;font-size:inherit;font-weight:inherit;color:inherit;border:none;border-bottom:2px solid currentColor;background:transparent;outline:none;padding:0';
        span.textContent = '';
        span.appendChild(input);
        input.focus();
        input.select();

        const save = async () => {
            const val = input.value.trim();
            if (val === '' || val === original) { span.textContent = original; return; }
            span.textContent = '...';
            try {
                const res = await fetch(`/products/${id}/inline-update`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ field, value: val })
                });
                const data = await res.json();
                if (data.success) {
                    const display = isPrice ? parseFloat(data.value).toFixed(2) : data.value;
                    span.textContent = display;
                    span.style.background = '#eafaf1';
                    setTimeout(() => span.style.background = '', 800);
                    // aggiorna margine se prezzo base cambiato
                    if (field === 'price' || field === 'cost_price') location.reload();
                } else {
                    span.textContent = original;
                    alert('Errore: ' + (data.message || 'salvataggio fallito'));
                }
            } catch(e) { span.textContent = original; }
        };

        input.addEventListener('blur', save);
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter')  { e.preventDefault(); input.blur(); }
            if (e.key === 'Escape') { span.textContent = original; }
            if (e.key === 'Tab') {
                e.preventDefault();
                input.blur();
                const row = span.closest('tr');
                const editables = [...row.querySelectorAll('.editable')];
                const idx = editables.indexOf(span);
                const next = editables[idx + (e.shiftKey ? -1 : 1)];
                if (next) setTimeout(() => next.click(), 80);
            }
        });
    });
});
</script>

@endsection