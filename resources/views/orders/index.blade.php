@extends('layouts.app')

@section('page-title','Ordini')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">📦 Ordini</div>
        <div class="page-sub">Ordini clienti</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">

        {{-- DROPDOWN STAMPA --}}
        <div style="position:relative" id="printMenu">
            <button onclick="toggleMenu('printMenu')" class="btn btn-secondary" style="padding:7px 14px;font-size:13px">🖨️ Stampa ▾</button>
            <div class="tool-dropdown" id="printMenuDrop" style="display:none;min-width:260px">
                <div class="drop-label">Stampa con i filtri attivi</div>
                <a href="#" onclick="stampaLista(); return false" class="drop-item">
                    <span class="drop-icon">📋</span>
                    <div>
                        <div class="drop-title">Lista ordini</div>
                        <div class="drop-sub">Tabella riepilogativa A4 landscape</div>
                    </div>
                </a>
                <a href="#" onclick="stampaOrdiniDettaglio(); return false" class="drop-item">
                    <span class="drop-icon">📄</span>
                    <div>
                        <div class="drop-title">Ordini con dettaglio prodotti</div>
                        <div class="drop-sub">1 pagina per ordine, prodotti inclusi</div>
                    </div>
                </a>
            </div>
        </div>

        {{-- DROPDOWN EXCEL --}}
        <div style="position:relative" id="exportMenu">
            <button onclick="toggleMenu('exportMenu')" class="btn btn-secondary" style="padding:7px 14px;font-size:13px">📥 Excel ▾</button>
            <div class="tool-dropdown" id="exportMenuDrop" style="display:none;min-width:280px">
                <div class="drop-label">I filtri attivi vengono applicati</div>
                <a href="#" onclick="doExport('orders'); return false" class="drop-item">
                    <span class="drop-icon">📦</span>
                    <div>
                        <div class="drop-title">Ordini — riepilogo</div>
                        <div class="drop-sub">1 riga per ordine</div>
                    </div>
                </a>
                <a href="#" onclick="doExport('items'); return false" class="drop-item">
                    <span class="drop-icon">🥦</span>
                    <div>
                        <div class="drop-title">Prodotti ordinati — dettaglio</div>
                        <div class="drop-sub">1 riga per prodotto per ordine</div>
                    </div>
                </a>
                <a href="#" onclick="doExport('summary'); return false" class="drop-item" style="border-bottom:none">
                    <span class="drop-icon">📊</span>
                    <div>
                        <div class="drop-title">Prodotti — totali aggregati</div>
                        <div class="drop-sub">Colli + Kg + Importo totale per prodotto</div>
                    </div>
                </a>
            </div>
        </div>

        <a href="/orders/create" class="btn btn-primary">+ Nuovo Ordine</a>
    </div>
</div>

@if(session('success'))
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:8px;background:#d4edda;color:#155724;font-size:14px">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="margin-bottom:16px;padding:12px 16px;border-radius:8px;background:#fde8e8;color:#8b0000;font-size:14px">⚠ {{ session('error') }}</div>
@endif

{{-- BARRA MASSIVA --}}
<div id="massiveBar" style="display:none;margin-bottom:12px;padding:10px 16px;background:var(--green-xl,#f0faf4);border:1px solid var(--green);border-radius:8px;gap:12px;align-items:center;flex-wrap:wrap">
    <span id="massiveCount" style="font-size:13px;font-weight:600;color:var(--green)"></span>
    <button onclick="massiveAction('confirm')" class="btn btn-secondary" style="font-size:13px;padding:6px 12px;color:#1a56a0">✓ Conferma selezionati</button>
    <button onclick="massiveAction('delete')"  class="btn btn-secondary" style="font-size:13px;padding:6px 12px;color:#c0392b">🗑 Elimina bozze</button>
    <button onclick="exportSelected()"         class="btn btn-secondary" style="font-size:13px;padding:6px 12px">📥 Esporta selezionati</button>
    <button onclick="deselezionaTutti()"        class="btn btn-secondary" style="font-size:13px;padding:6px 10px;margin-left:auto">✕ Deseleziona</button>
</div>

<div class="card" style="padding:0;overflow:hidden" id="ordersCard">

    {{-- FILTRI --}}
    <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <input type="checkbox" id="selectAll" style="width:15px;height:15px;accent-color:var(--green);cursor:pointer" title="Seleziona tutti visibili">
        <input type="text" id="searchInput" placeholder="🔍 Cerca numero o cliente..." style="max-width:220px;margin:0">
        <select id="filterStato" style="max-width:150px;margin:0">
            <option value="">Tutti gli stati</option>
            <option value="draft">Bozza</option>
            <option value="web">Web</option>
            <option value="confirmed">Confermato</option>
            <option value="invoiced">Evaso</option>
        </select>
        <select id="filterPeriodo" style="max-width:160px;margin:0">
            <option value="">Tutte le date</option>
            <option value="oggi">Oggi</option>
            <option value="settimana">Questa settimana</option>
            <option value="mese">Questo mese</option>
            <option value="mese_scorso">Mese scorso</option>
        </select>
        <button onclick="resetFiltri()" class="btn btn-secondary" style="padding:7px 12px;font-size:13px">✕ Reset</button>
        <span id="countLabel" style="font-size:12px;color:var(--muted);margin-left:auto"></span>
    </div>

    <table id="ordersTable">
        <thead>
            <tr>
                <th style="width:36px"></th>
                <th style="width:40px">#</th>
                <th>Numero</th>
                <th>Cliente</th>
                <th>Data / Ora</th>
                <th style="text-align:center">Stato</th>
                <th style="text-align:right">Totale</th>
                <th style="text-align:center;width:120px">Azioni</th>
            </tr>
        </thead>
        <tbody id="ordersBody">
        @forelse($orders as $order)
        @php
            $isEvaso = $order->status === 'invoiced';
            $isBozza = $order->status === 'draft';
            $isWeb   = $order->status === 'web';
            $dataRaw = \Carbon\Carbon::parse($order->date)->format('Y-m-d');
        @endphp
        <tr class="order-row clickable-row"
            data-id="{{ $order->id }}"
            data-number="{{ strtolower($order->number ?? '') }}"
            data-client="{{ strtolower($order->client->company_name ?? '') }}"
            data-stato="{{ $order->status }}"
            data-date="{{ $dataRaw }}"
            data-href="/orders/{{ $order->id }}"
            style="cursor:pointer">

            <td onclick="event.stopPropagation()">
                <input type="checkbox" class="row-check" data-id="{{ $order->id }}"
                       style="width:14px;height:14px;accent-color:var(--green);cursor:pointer">
            </td>
            <td style="color:var(--muted);font-size:12px">{{ $order->id }}</td>
            <td>
                @if($order->number)
                    <span style="font-weight:600;color:var(--green);font-family:'DM Mono',monospace;font-size:13px">{{ $order->number }}</span>
                    @if($isWeb)
                        <span style="font-size:10px;background:#e3f0ff;color:#1a56a0;padding:1px 5px;border-radius:3px;margin-left:4px;font-weight:600">WEB</span>
                    @endif
                @else
                    <span style="color:#c0392b;font-size:12px;font-weight:600">⚠ N° mancante</span>
                @endif
            </td>
            <td style="font-weight:500">{{ $order->client->company_name ?? '—' }}</td>
            <td style="color:var(--muted);font-size:13px;white-space:nowrap">
                {{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}
                <span style="font-size:11px;margin-left:4px">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</span>
            </td>
            <td style="text-align:center">
                @if($isBozza)
                    <span style="background:#fff3e0;color:#e65100;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">● Bozza</span>
                @elseif($order->status === 'confirmed')
                    <span style="background:#e3f0ff;color:#1a56a0;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">● Confermato</span>
                @elseif($isEvaso)
                    <span style="background:var(--green-xl,#f0faf4);color:var(--green);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">● Evaso</span>
                @elseif($isWeb)
                    <span style="background:#f3f4f6;color:#6b7280;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">web</span>
                @else
                    <span style="background:#f3f4f6;color:#6b7280;padding:3px 10px;border-radius:20px;font-size:11px">{{ $order->status }}</span>
                @endif
            </td>
            <td style="text-align:right;font-weight:700;font-family:'DM Mono',monospace">
                € {{ number_format($order->total, 2, ',', '.') }}
            </td>
            <td style="text-align:center" onclick="event.stopPropagation()">
                <div style="display:flex;gap:5px;justify-content:center;align-items:center">
                    @if(!$isEvaso)
                        <a href="/orders/{{ $order->id }}/edit" class="btn btn-secondary" style="padding:4px 9px;font-size:12px" title="Modifica">✏️</a>
                    @endif
                    @if($isBozza || $isWeb)
                        <form method="POST" action="/orders/{{ $order->id }}" style="margin:0"
                              onsubmit="return confirm('Eliminare l\'ordine {{ addslashes($order->number ?? 'questo ordine') }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-secondary" style="padding:4px 9px;font-size:12px;color:#c0392b;border-color:#fcc">🗑</button>
                        </form>
                    @endif
                    @if($isEvaso)
                        @if($order->documents && $order->documents->first())
                            <a href="/documents/{{ $order->documents->first()->id }}" class="btn btn-secondary" style="padding:4px 9px;font-size:11px;color:var(--green)">📄 DDT</a>
                        @else
                            <span style="font-size:11px;color:var(--muted)">—</span>
                        @endif
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center;padding:48px;color:var(--muted)">
                <div style="font-size:32px;margin-bottom:10px">📦</div>
                <div style="font-weight:600;margin-bottom:6px">Nessun ordine trovato</div>
                <a href="/orders/create" style="color:var(--green)">Crea il primo ordine →</a>
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>
    <div id="noResults" style="display:none;text-align:center;padding:40px;color:var(--muted);font-size:14px">
        Nessun ordine corrisponde ai filtri selezionati.
    </div>
</div>

{{-- Header stampa lista --}}
<div id="print-header">
    <div style="display:flex;justify-content:space-between;margin-bottom:8px">
        <strong style="font-size:14px">Mr. Fruits ERP — Lista Ordini</strong>
        <span style="font-size:11px;color:#666" id="printDate"></span>
    </div>
    <div id="print-filters" style="font-size:11px;color:#555;margin-bottom:8px"></div>
    <hr style="border:1px solid #ccc;margin-bottom:8px">
</div>

<style>
/* Dropdown shared */
.tool-dropdown {
    position:absolute;right:0;top:38px;
    background:var(--color-background-primary,#fff);
    border:1px solid var(--border);border-radius:8px;
    box-shadow:0 4px 20px rgba(0,0,0,.13);z-index:200;overflow:hidden;
}
.drop-label {
    padding:7px 14px 5px;font-size:11px;font-weight:700;
    text-transform:uppercase;letter-spacing:.05em;
    color:var(--muted);border-bottom:1px solid var(--border);
}
.drop-item {
    display:flex;align-items:center;gap:10px;
    padding:11px 16px;font-size:13px;
    color:var(--dark);text-decoration:none;
    border-bottom:1px solid var(--border);
    transition:background .12s;
}
.drop-item:hover { background:var(--green-xl,#f0faf4); }
.drop-icon { font-size:18px;flex-shrink:0; }
.drop-title { font-weight:600; }
.drop-sub { font-size:11px;color:var(--muted); }

.clickable-row:hover td { background: var(--green-xl, #f0faf4) !important; }
#print-header { display: none; }

@media print {
    nav, aside, .page-header, #massiveBar,
    .btn, button, select, input,
    th:first-child, td:first-child,
    th:last-child, td:last-child { display: none !important; }
    #ordersCard { border: none !important; overflow: visible !important; }
    body { font-size: 11px !important; color: #000 !important; }
    table { width: 100% !important; border-collapse: collapse !important; font-size: 10px !important; }
    th, td { border: 1px solid #ccc !important; padding: 4px 6px !important; }
    thead { background: #e8f5ee !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    #print-header { display: block !important; }
    .order-row[style*="display: none"] { display: none !important; }
    @page { size: A4 landscape; margin: 12mm; }
}
</style>

<script>
// ── Dropdown generico ──────────────────────────────────────
function toggleMenu(id) {
    const drop = document.getElementById(id + 'Drop');
    const isOpen = drop.style.display !== 'none';
    // Chiudi tutti
    document.querySelectorAll('.tool-dropdown').forEach(d => d.style.display = 'none');
    if (!isOpen) drop.style.display = 'block';
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('[id$="Menu"]')) {
        document.querySelectorAll('.tool-dropdown').forEach(d => d.style.display = 'none');
    }
});

// ── Filtri ─────────────────────────────────────────────────
function getDateBounds(periodo) {
    const now = new Date(), today = now.toISOString().slice(0,10);
    if (periodo === 'oggi') return { from: today, to: today };
    if (periodo === 'settimana') {
        const day = now.getDay() || 7;
        const mon = new Date(now); mon.setDate(now.getDate() - day + 1);
        const sun = new Date(mon); sun.setDate(mon.getDate() + 6);
        return { from: mon.toISOString().slice(0,10), to: sun.toISOString().slice(0,10) };
    }
    if (periodo === 'mese') return {
        from: new Date(now.getFullYear(), now.getMonth(), 1).toISOString().slice(0,10),
        to:   new Date(now.getFullYear(), now.getMonth()+1, 0).toISOString().slice(0,10)
    };
    if (periodo === 'mese_scorso') return {
        from: new Date(now.getFullYear(), now.getMonth()-1, 1).toISOString().slice(0,10),
        to:   new Date(now.getFullYear(), now.getMonth(), 0).toISOString().slice(0,10)
    };
    return null;
}

function getActiveFilters() {
    return {
        q:       document.getElementById('searchInput').value.toLowerCase().trim(),
        stato:   document.getElementById('filterStato').value,
        periodo: document.getElementById('filterPeriodo').value,
    };
}

function filterRows() {
    const { q, stato, periodo } = getActiveFilters();
    const bounds = getDateBounds(periodo);
    let visible = 0;

    document.querySelectorAll('.order-row').forEach(row => {
        const show =
            (!q      || row.dataset.number.includes(q) || row.dataset.client.includes(q)) &&
            (!stato  || row.dataset.stato === stato) &&
            (!bounds || (row.dataset.date >= bounds.from && row.dataset.date <= bounds.to));
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const total = document.querySelectorAll('.order-row').length;
    document.getElementById('countLabel').textContent =
        visible === total ? total + ' ordini' : visible + ' di ' + total + ' ordini';
    document.getElementById('noResults').style.display = visible === 0 && total > 0 ? 'block' : 'none';
    document.querySelectorAll('.order-row[style*="display: none"] .row-check').forEach(cb => cb.checked = false);
    aggiornaBar();
}

function resetFiltri() {
    ['searchInput','filterStato','filterPeriodo'].forEach(id => document.getElementById(id).value = '');
    filterRows();
}

['searchInput','filterStato','filterPeriodo'].forEach(id => {
    const el = document.getElementById(id);
    el.addEventListener(el.tagName === 'SELECT' ? 'change' : 'input', filterRows);
});
filterRows();

// ── Riga cliccabile ────────────────────────────────────────
document.querySelectorAll('.clickable-row').forEach(row => {
    row.addEventListener('click', function() { window.location.href = this.dataset.href; });
});

// ── Export ─────────────────────────────────────────────────
function buildExportUrl(base) {
    const { stato, periodo } = getActiveFilters();
    const bounds = getDateBounds(periodo);
    const p = new URLSearchParams();
    if (stato)        p.set('stato', stato);
    if (bounds?.from) p.set('from', bounds.from);
    if (bounds?.to)   p.set('to',   bounds.to);
    return base + (p.toString() ? '?' + p.toString() : '');
}

function doExport(type) {
    document.querySelectorAll('.tool-dropdown').forEach(d => d.style.display = 'none');
    const routes = {
        orders:  '{{ route("orders.export") }}',
        items:   '{{ route("orders.export-items") }}',
        summary: '{{ route("orders.export-product-summary") }}',
    };
    window.location.href = buildExportUrl(routes[type]);
}

function exportSelected() {
    const ids = getSelectedIds();
    if (ids.length === 0) { alert('Seleziona almeno un ordine'); return; }
    window.location.href = '{{ route("orders.export") }}?ids=' + ids.join(',');
}

// ── Stampa ─────────────────────────────────────────────────
function buildPrintUrl() {
    const { stato, periodo } = getActiveFilters();
    const q = document.getElementById('searchInput').value.trim();
    const bounds = getDateBounds(periodo);
    const p = new URLSearchParams();
    if (stato)        p.set('stato', stato);
    if (q)            p.set('q', q);
    if (bounds?.from) p.set('from', bounds.from);
    if (bounds?.to)   p.set('to',   bounds.to);
    return p.toString();
}

function stampaLista() {
    document.querySelectorAll('.tool-dropdown').forEach(d => d.style.display = 'none');
    const { stato, periodo } = getActiveFilters();
    const q = document.getElementById('searchInput').value;
    const desc = [stato ? 'Stato: '+stato : '', periodo ? 'Periodo: '+periodo : '', q ? 'Ricerca: "'+q+'"' : ''].filter(Boolean);
    document.getElementById('printDate').textContent =
        'Stampato il ' + new Date().toLocaleDateString('it-IT', {day:'2-digit',month:'2-digit',year:'numeric'});
    document.getElementById('print-filters').textContent =
        desc.length ? 'Filtri: ' + desc.join(' · ') : 'Tutti gli ordini';
    window.print();
}

function stampaOrdiniDettaglio() {
    document.querySelectorAll('.tool-dropdown').forEach(d => d.style.display = 'none');
    const qs = buildPrintUrl();
    window.open('{{ route("orders.print") }}' + (qs ? '?' + qs : ''), '_blank');
}

// ── Selezione massiva ──────────────────────────────────────
const massiveBar = document.getElementById('massiveBar');

function aggiornaBar() {
    const n = document.querySelectorAll('.row-check:checked').length;
    massiveBar.style.display = n > 0 ? 'flex' : 'none';
    document.getElementById('massiveCount').textContent = n + ' ordini selezionati';
}

document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.order-row').forEach(row => {
        if (row.style.display !== 'none') row.querySelector('.row-check').checked = this.checked;
    });
    aggiornaBar();
});
document.querySelectorAll('.row-check').forEach(cb => cb.addEventListener('change', aggiornaBar));

function deselezionaTutti() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    aggiornaBar();
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.dataset.id);
}

function massiveAction(action) {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    const msg = action === 'delete'
        ? 'Eliminare ' + ids.length + ' ordini? Solo bozze/web verranno eliminati.'
        : 'Confermare ' + ids.length + ' ordini selezionati?';
    if (!confirm(msg)) return;

    fetch('{{ route("orders.massive-action") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ ids, action })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) window.location.reload();
        else alert('Errore: ' + (data.message || 'operazione fallita'));
    });
}
</script>

@endsection