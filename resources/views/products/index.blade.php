@extends('layouts.app')

@section('page-title', 'Prodotti')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">🏷️ Prodotti</div>
        <div class="page-sub">Editing inline su tutto — desktop e mobile</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
        <button onclick="apriImport()" class="btn btn-secondary" style="padding:7px 14px;font-size:13px" title="Importa da CSV/Excel">📤 Importa</button>
        <button onclick="stampaProdotti()" class="btn btn-secondary" style="padding:7px 14px;font-size:13px" title="Stampa lista prodotti">🖨️ Stampa</button>
        <a href="{{ route('products.export') }}" class="btn btn-secondary" style="padding:7px 14px;font-size:13px" title="Esporta in Excel">📥 Excel</a>
        <a href="{{ url('/products/create') }}" class="btn btn-primary">+ Nuovo</a>
    </div>
</div>

{{-- Toast success/warning --}}
@if(session('success'))
    <div style="margin-bottom:14px;padding:10px 16px;border-radius:8px;background:#d4edda;color:#155724;font-size:14px">✓ {{ session('success') }}</div>
@endif
@if(session('warning'))
    <div style="margin-bottom:14px;padding:10px 16px;border-radius:8px;background:#fef3cd;color:#856404;font-size:14px">⚠ {{ session('warning') }}</div>
@endif
@if(session('error'))
    <div style="margin-bottom:14px;padding:10px 16px;border-radius:8px;background:#fde8e8;color:#8b0000;font-size:14px">✗ {{ session('error') }}</div>
@endif

{{-- ── MODALE IMPORT ──────────────────────────────────── --}}
<div id="importModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
    <div style="background:var(--color-background-primary,#fff);border-radius:12px;padding:28px 32px;max-width:520px;width:90%;box-shadow:0 8px 40px rgba(0,0,0,.18)">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <span style="font-size:16px;font-weight:700;color:var(--dark)">📤 Importa Prodotti da CSV</span>
            <button onclick="chiudiImport()" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--muted);line-height:1">×</button>
        </div>

        {{-- Info box --}}
        <div style="background:#e8f5ee;border:1px solid var(--green);border-radius:8px;padding:12px 14px;margin-bottom:20px;font-size:13px;color:#155724;line-height:1.6">
            <strong>Come funziona:</strong><br>
            1. Esporta il file con il bottone <strong>📥 Excel</strong><br>
            2. Modifica i valori in Excel (non cambiare la colonna SKU né i nomi delle intestazioni)<br>
            3. Salva come <strong>.csv con separatore ;</strong> (CSV UTF-8)<br>
            4. Carica il file qui sotto<br><br>
            <strong>Campi aggiornabili:</strong> Prezzi, Costo, Disponibilità, Origine, Stock, Scorta min, Pesi, Ordini min/max<br>
            <strong>Campi ignorati:</strong> Nome, Categoria, Modalità vendita
        </div>

        <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--dark);margin-bottom:6px">File CSV</label>
                <input type="file"
                       name="csv_file"
                       accept=".csv,.txt"
                       required
                       id="csvFileInput"
                       style="width:100%;padding:10px;border:2px dashed var(--border);border-radius:8px;font-size:13px;background:var(--green-xl,#f0faf4);cursor:pointer">
                <div id="fileInfo" style="font-size:12px;color:var(--muted);margin-top:4px"></div>
            </div>

            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-primary" style="flex:1;padding:10px">
                    📤 Avvia Importazione
                </button>
                <button type="button" onclick="chiudiImport()" class="btn btn-secondary" style="padding:10px 16px">
                    Annulla
                </button>
            </div>
        </form>

    </div>
</div>

{{-- FILTRI --}}
<div class="card" style="padding:12px 16px;margin-bottom:16px">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <input type="text" id="searchInput" placeholder="🔍 Nome..." style="max-width:200px;margin:0">
        <select id="filterOrigine" style="max-width:150px;margin:0">
            <option value="">Tutte orig.</option>
            @foreach($origins as $o)
                <option value="{{ strtolower($o) }}">{{ $o }}</option>
            @endforeach
        </select>
        <select id="filterCategoria" style="max-width:160px;margin:0">
            <option value="">Tutte categ.</option>
            @foreach($categories as $c)
                <option value="{{ strtolower($c) }}">{{ $c }}</option>
            @endforeach
        </select>
        <select id="filterStato" style="max-width:160px;margin:0">
            <option value="">Tutti stati</option>
            <option value="disponibile">Disponibile</option>
            <option value="su_richiesta">Su richiesta</option>
            <option value="non_disponibile">Non disponibile</option>
        </select>
        <select id="filterDisp" style="max-width:150px;margin:0">
            <option value="">Tutte disp.</option>
            <option value="cassa_kg">Cassa kg</option>
            <option value="cassa_collo">Cassa collo</option>
            <option value="kg_liberi">Kg liberi</option>
            <option value="pezzo">Pezzo</option>
            <option value="peso_step">Peso step</option>
        </select>
        <button onclick="resetFiltri()" class="btn btn-secondary" style="padding:6px 12px;font-size:12px">✕ Reset</button>
        <span id="countLabel" style="margin-left:auto;font-size:12px;color:var(--muted)"></span>
    </div>
</div>

{{-- AZIONI MASSIVE --}}
<div id="massiveBar" style="display:none;margin-bottom:12px;padding:10px 16px;background:var(--green-xl,#f0faf4);border:1px solid var(--green);border-radius:8px;gap:12px;align-items:center;flex-wrap:wrap">
    <span id="massiveCount" style="font-size:13px;font-weight:600;color:var(--green)"></span>
    <select id="massiveAction" style="max-width:200px;margin:0;font-size:13px">
        <option value="">— Azione massiva —</option>
        <option value="disp_set:disponibile">Imposta: Disponibile</option>
        <option value="disp_set:su_richiesta">Imposta: Su richiesta</option>
        <option value="disp_set:non_disponibile">Imposta: Non disponibile</option>
        <option value="price_percent">Modifica prezzo base %</option>
        <option value="cost_percent">Modifica costo %</option>
    </select>
    <input type="number" id="massiveValue" placeholder="Valore %" style="max-width:100px;margin:0;font-size:13px;display:none">
    <button onclick="eseguiMassivo()" class="btn btn-primary" style="font-size:13px;padding:6px 14px">Applica</button>
    <button onclick="deselezionaTutti()" class="btn btn-secondary" style="font-size:13px;padding:6px 12px">✕ Deseleziona</button>
</div>

<div class="card" style="padding:0;overflow:hidden" id="productsCard">
    <table id="productsTable" style="font-size:13px">
        <thead>
            <tr>
                <th style="width:36px">
                    <input type="checkbox" id="selectAll" style="width:14px;height:14px;accent-color:var(--green);cursor:pointer">
                </th>
                <th>Nome / SKU / Origine</th>
                <th style="text-align:right">Costo</th>
                <th style="text-align:right">Base</th>
                <th style="text-align:right">HoReCa</th>
                <th style="text-align:right">Dett.</th>
                <th style="text-align:right">GDO</th>
                <th style="text-align:right">Marg.</th>
                <th style="text-align:right">Stock</th>
                <th style="text-align:center">Disp.</th>
                <th style="text-align:center">Stato</th>
                <th style="text-align:center;width:80px">Azioni</th>
            </tr>
        </thead>
        <tbody id="productsBody">
        @php $lastCat = null; @endphp
        @forelse($products as $product)
        @php
            $stock    = $product->stock;
            $qty      = $stock?->quantity ?? 0;
            $minStock = $stock?->min_stock ?? 0;
            $margin   = $product->price > 0 && $product->cost_price > 0
                        ? (($product->price - $product->cost_price) / $product->price) * 100
                        : 0;
            $isLow    = $qty <= $minStock && $minStock > 0;
            $isOut    = $product->disponibilita === 'non_disponibile';
            $isReq    = $product->disponibilita === 'su_richiesta';
            $catChanged = $lastCat !== $product->category;
            $lastCat  = $product->category;
        @endphp

        @if($catChanged)
        <tr class="cat-header" data-cat="{{ strtolower($product->category) }}">
            <td colspan="12" style="background:var(--green-xl,#f0faf4);padding:7px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--green);border-top:2px solid var(--border)">
                {{ $product->category }}
            </td>
        </tr>
        @endif

        <tr class="product-row"
            data-name="{{ strtolower($product->name) }}"
            data-cat="{{ strtolower($product->category) }}"
            data-origine="{{ strtolower($product->origin ?? '') }}"
            data-stato="{{ $product->disponibilita }}"
            data-disp="{{ $product->modalita_vendita }}"
            data-id="{{ $product->id }}">

            <td>
                <input type="checkbox" class="row-check" data-id="{{ $product->id }}"
                       style="width:14px;height:14px;accent-color:var(--green);cursor:pointer">
            </td>

            <td>
                <div style="font-weight:600;color:var(--dark)">{{ $product->name }}</div>
                <div style="display:flex;gap:5px;margin-top:2px;flex-wrap:wrap">
                    <span style="font-size:10px;background:#f3f4f6;color:#6b7280;padding:1px 5px;border-radius:3px;font-family:'DM Mono',monospace">{{ $product->sku }}</span>
                    @if($product->origin)
                    <span style="font-size:10px;background:#e8f5ee;color:var(--green);padding:1px 5px;border-radius:3px">{{ $product->origin }}</span>
                    @endif
                    @if($product->modalita_vendita)
                    <span style="font-size:10px;background:#e3f0ff;color:#1a56a0;padding:1px 5px;border-radius:3px">{{ str_replace('_',' ',$product->modalita_vendita) }}</span>
                    @endif
                </div>
            </td>

            <td style="text-align:right;font-family:'DM Mono',monospace" class="editable" data-field="cost_price" data-id="{{ $product->id }}">
                {{ number_format($product->cost_price ?? 0, 2, ',', '') }}
            </td>
            <td style="text-align:right;font-family:'DM Mono',monospace;font-weight:600" class="editable" data-field="price" data-id="{{ $product->id }}">
                {{ number_format($product->price ?? 0, 2, ',', '') }}
            </td>
            <td style="text-align:right;font-family:'DM Mono',monospace;color:#e65100" class="editable" data-field="price_horeca" data-id="{{ $product->id }}">
                {{ number_format($product->price_horeca ?? 0, 2, ',', '') }}
            </td>
            <td style="text-align:right;font-family:'DM Mono',monospace;color:#1a56a0" class="editable" data-field="price_dettaglio" data-id="{{ $product->id }}">
                {{ number_format($product->price_dettaglio ?? 0, 2, ',', '') }}
            </td>
            <td style="text-align:right;font-family:'DM Mono',monospace;color:#6b3fa0" class="editable" data-field="price_gdo" data-id="{{ $product->id }}">
                {{ number_format($product->price_gdo ?? 0, 2, ',', '') }}
            </td>

            <td style="text-align:right;font-family:'DM Mono',monospace;font-size:12px">
                @php
                    $mc = $margin >= 40 ? '#155724' : ($margin >= 20 ? '#856404' : '#8b0000');
                    $mb = $margin >= 40 ? '#d4edda' : ($margin >= 20 ? '#fef3cd' : '#fde8e8');
                @endphp
                <span style="background:{{ $mb }};color:{{ $mc }};padding:2px 6px;border-radius:3px;font-size:11px">
                    {{ number_format($margin, 1, ',', '') }}%
                </span>
            </td>

            <td style="text-align:right;font-family:'DM Mono',monospace;font-size:12px">
                @if($isLow)
                    <span style="color:#c0392b;font-weight:700">{{ number_format($qty, 0, ',', '.') }}</span>
                    <span style="font-size:10px;color:#c0392b"> ↓</span>
                @else
                    {{ number_format($qty, 0, ',', '.') }}
                @endif
                <div style="font-size:10px;color:var(--muted)">min {{ number_format($minStock,0,',','.') }}</div>
            </td>

            <td style="text-align:center">
                @if($isOut)
                    <span style="font-size:10px;background:#fde8e8;color:#8b0000;padding:2px 6px;border-radius:20px;font-weight:600">✗ No</span>
                @elseif($isReq)
                    <span style="font-size:10px;background:#fff3e0;color:#e65100;padding:2px 6px;border-radius:20px;font-weight:600">~ Req</span>
                @else
                    <span style="font-size:10px;background:#d4edda;color:#155724;padding:2px 6px;border-radius:20px;font-weight:600">✓ OK</span>
                @endif
            </td>

            <td style="text-align:center">
                @if($isLow)
                    <span style="font-size:10px;background:#fde8e8;color:#8b0000;padding:2px 6px;border-radius:20px;font-weight:600">↓ Basso</span>
                @else
                    <span style="font-size:10px;background:#d4edda;color:#155724;padding:2px 6px;border-radius:20px;font-weight:600">✓ OK</span>
                @endif
            </td>

            <td style="text-align:center">
                <div style="display:flex;gap:4px;justify-content:center">
                    <a href="{{ url('/products/' . $product->id . '/edit') }}"
                       class="btn btn-secondary" style="padding:4px 8px;font-size:11px" title="Modifica">✏️</a>
                    <form method="POST" action="{{ url('/products/' . $product->id) }}" style="margin:0"
                          onsubmit="return confirm('Eliminare {{ addslashes($product->name) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-secondary"
                                style="padding:4px 8px;font-size:11px;color:#c0392b" title="Elimina">🗑</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="12" style="text-align:center;padding:48px;color:var(--muted)">
                <div style="font-size:32px;margin-bottom:10px">🏷️</div>
                <div style="font-weight:600;margin-bottom:6px">Nessun prodotto</div>
                <a href="{{ url('/products/create') }}" style="color:var(--green)">Aggiungi il primo →</a>
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Header stampa (visibile solo in print) --}}
<div id="print-header">
    <div style="display:flex;justify-content:space-between;margin-bottom:8px">
        <strong style="font-size:14px">Mr. Fruits ERP — Lista Prodotti</strong>
        <span style="font-size:11px;color:#666" id="printDate"></span>
    </div>
    <hr style="border:1px solid #ccc;margin-bottom:8px">
</div>

<style>
.editable { cursor: pointer; }
.editable:hover { background: var(--green-xl, #f0faf4) !important; outline: 1px dashed var(--green); }
.editable input {
    width: 70px; text-align: right; border: none; background: transparent;
    font-family: 'DM Mono', monospace; font-size: 13px; outline: none;
    border-bottom: 2px solid var(--green);
}
.product-row:hover td { background: #fafafa; }
#print-header { display: none; }

@media print {
    nav, aside, .page-header, .card:not(#productsCard),
    #massiveBar, .btn, button,
    input[type=checkbox], input[type=text], select,
    th:first-child, td:first-child,
    th:last-child, td:last-child,
    #importModal { display: none !important; }
    #productsCard { border: none !important; overflow: visible !important; }
    body { font-size: 11px !important; color: #000 !important; }
    table { width: 100% !important; border-collapse: collapse !important; font-size: 10px !important; }
    th, td { border: 1px solid #ccc !important; padding: 4px 6px !important; }
    thead { background: #e8f5ee !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .cat-header td { background: #f0faf4 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    #print-header { display: block !important; }
    @page { size: A4 landscape; margin: 12mm; }
}
</style>

<script>
// ── Modale import ──────────────────────────────────────
function apriImport() {
    document.getElementById('importModal').style.display = 'flex';
}
function chiudiImport() {
    document.getElementById('importModal').style.display = 'none';
}
document.getElementById('importModal').addEventListener('click', function(e) {
    if (e.target === this) chiudiImport();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') chiudiImport();
});
document.getElementById('csvFileInput').addEventListener('change', function() {
    const f = this.files[0];
    if (f) {
        const kb = (f.size / 1024).toFixed(1);
        document.getElementById('fileInfo').textContent = f.name + ' (' + kb + ' KB)';
    }
});

// ── Filtri ─────────────────────────────────────────────
function applyFilters() {
    const q     = document.getElementById('searchInput').value.toLowerCase().trim();
    const orig  = document.getElementById('filterOrigine').value;
    const cat   = document.getElementById('filterCategoria').value;
    const stato = document.getElementById('filterStato').value;
    const disp  = document.getElementById('filterDisp').value;
    let visible = 0;
    const visibleCats = new Set();

    document.querySelectorAll('.product-row').forEach(row => {
        const show =
            (!q     || row.dataset.name.includes(q)) &&
            (!orig  || row.dataset.origine === orig) &&
            (!cat   || row.dataset.cat === cat) &&
            (!stato || row.dataset.stato === stato) &&
            (!disp  || row.dataset.disp === disp);
        row.style.display = show ? '' : 'none';
        if (show) { visible++; visibleCats.add(row.dataset.cat); }
    });

    document.querySelectorAll('.cat-header').forEach(h => {
        h.style.display = visibleCats.has(h.dataset.cat) ? '' : 'none';
    });

    const total = document.querySelectorAll('.product-row').length;
    document.getElementById('countLabel').textContent =
        visible === total ? total + ' prodotti' : visible + ' di ' + total;
}

function resetFiltri() {
    ['searchInput','filterOrigine','filterCategoria','filterStato','filterDisp']
        .forEach(id => document.getElementById(id).value = '');
    applyFilters();
}

['searchInput','filterOrigine','filterCategoria','filterStato','filterDisp']
    .forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener(el.tagName === 'SELECT' ? 'change' : 'input', applyFilters);
    });

applyFilters();

// ── Selezione massiva ──────────────────────────────────
const massiveBar = document.getElementById('massiveBar');

function aggiornaBar() {
    const checked = document.querySelectorAll('.row-check:checked');
    if (checked.length > 0) {
        massiveBar.style.display = 'flex';
        document.getElementById('massiveCount').textContent = checked.length + ' prodotti selezionati';
    } else {
        massiveBar.style.display = 'none';
    }
}

document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.row-check').forEach(cb => {
        if (cb.closest('tr').style.display !== 'none') cb.checked = this.checked;
    });
    aggiornaBar();
});

document.querySelectorAll('.row-check').forEach(cb => cb.addEventListener('change', aggiornaBar));

document.getElementById('massiveAction').addEventListener('change', function() {
    const needsValue = ['price_percent','cost_percent'].includes(this.value);
    document.getElementById('massiveValue').style.display = needsValue ? 'block' : 'none';
});

function deselezionaTutti() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    aggiornaBar();
}

function eseguiMassivo() {
    const ids   = Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.dataset.id);
    const raw   = document.getElementById('massiveAction').value;
    const value = document.getElementById('massiveValue').value;
    if (!raw || ids.length === 0) return;

    let action = raw, val = value;
    if (raw.includes(':')) { [action, val] = raw.split(':'); }

    fetch('{{ route("products.massive-update") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ ids, action, value: val })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) window.location.reload();
        else alert('Errore: ' + (data.message || 'operazione fallita'));
    });
}

// ── Editing inline ─────────────────────────────────────
document.querySelectorAll('.editable').forEach(cell => {
    cell.addEventListener('dblclick', function() {
        if (this.querySelector('input')) return;
        const current = this.textContent.trim().replace(',', '.');
        const input = document.createElement('input');
        input.type = 'number'; input.step = '0.01';
        input.value = parseFloat(current) || 0;
        this.textContent = '';
        this.appendChild(input);
        input.focus(); input.select();

        const save = () => {
            const val = parseFloat(input.value);
            if (isNaN(val)) { location.reload(); return; }
            fetch(`/products/${cell.dataset.id}/inline-update`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ field: cell.dataset.field, value: val })
            })
            .then(r => r.json())
            .then(data => { cell.textContent = parseFloat(data.value || val).toFixed(2).replace('.', ','); })
            .catch(() => { cell.textContent = val.toFixed(2).replace('.', ','); });
        };

        input.addEventListener('blur', save);
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); input.blur(); }
            if (e.key === 'Escape') location.reload();
        });
    });
});

// ── Stampa ─────────────────────────────────────────────
function stampaProdotti() {
    document.getElementById('printDate').textContent =
        'Stampato il ' + new Date().toLocaleDateString('it-IT', {day:'2-digit',month:'2-digit',year:'numeric'});
    window.print();
}
</script>

@endsection