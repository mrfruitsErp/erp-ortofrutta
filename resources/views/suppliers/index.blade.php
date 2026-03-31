@extends('layouts.app')

@section('page-title', 'Fornitori')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">🏭 Fornitori</div>
        <div class="page-sub">Gestione anagrafica fornitori</div>
    </div>
    <a href="{{ url('/suppliers/create') }}" class="btn btn-primary">+ Nuovo Fornitore</a>
</div>

<div class="card" style="padding:0;overflow:hidden">

    <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:center">
        <input type="text" id="searchInput" placeholder="🔍 Cerca fornitore..." style="max-width:300px;margin:0">
        <span id="countLabel" style="margin-left:auto;font-size:12px;color:var(--muted)"></span>
    </div>

    <table id="suppliersTable">
        <thead>
            <tr>
                <th class="sortable" data-col="0" style="cursor:pointer;user-select:none">
                    Ragione Sociale <span class="sort-icon">↕</span>
                </th>
                <th>P.IVA</th>
                <th>Città</th>
                <th>Telefono</th>
                <th>Email</th>
                <th style="text-align:right;cursor:pointer;user-select:none" class="sortable" data-col="5">
                    Acquistato <span class="sort-icon">↕</span>
                </th>
                <th style="text-align:center;width:90px">Azioni</th>
            </tr>
        </thead>
        <tbody id="suppliersBody">
        @forelse($suppliers as $supplier)
        <tr class="supplier-row clickable-row"
            data-name="{{ strtolower($supplier->company_name) }}"
            data-href="{{ url('/suppliers/' . $supplier->id) }}"
            style="cursor:pointer">

            <td style="font-weight:700;color:var(--dark)">{{ $supplier->company_name }}</td>

            <td style="color:var(--muted);font-size:13px;font-family:'DM Mono',monospace">
                {{ $supplier->vat_number ?? '—' }}
            </td>

            <td style="color:var(--muted);font-size:13px">{{ $supplier->city ?? '—' }}</td>

            <td style="font-size:13px">{{ $supplier->phone ?? '—' }}</td>

            <td style="font-size:13px;color:var(--muted)">{{ $supplier->email ?? '—' }}</td>

            <td style="text-align:right;font-family:'DM Mono',monospace" data-value="{{ $supplier->totale_acquistato ?? 0 }}">
                @if(($supplier->totale_acquistato ?? 0) > 0)
                    € {{ number_format($supplier->totale_acquistato, 2, ',', '.') }}
                @else
                    <span style="color:var(--muted)">—</span>
                @endif
            </td>

            <td style="text-align:center" onclick="event.stopPropagation()">
                <div style="display:flex;gap:6px;justify-content:center">
                    <a href="{{ url('/suppliers/' . $supplier->id . '/edit') }}"
                       class="btn btn-secondary"
                       style="padding:5px 10px;font-size:12px"
                       title="Modifica">✏️</a>
                </div>
            </td>

        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;padding:48px;color:var(--muted)">
                <div style="font-size:32px;margin-bottom:10px">🏭</div>
                <div style="font-weight:600;margin-bottom:6px">Nessun fornitore ancora</div>
                <a href="{{ url('/suppliers/create') }}" style="color:var(--green)">Aggiungi il primo →</a>
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>

    <div id="noResults" style="display:none;text-align:center;padding:40px;color:var(--muted)">
        Nessun risultato per la ricerca corrente.
    </div>

</div>

<style>
.clickable-row:hover td {
    background: var(--green-xl, #f0faf4) !important;
}
.clickable-row:hover td:first-child {
    color: var(--green) !important;
}
th.sortable:hover { background: var(--green-xl, #f0faf4); }
.sort-icon { opacity: 0.4; font-size: 11px; }
th.sortable.asc .sort-icon,
th.sortable.desc .sort-icon { opacity: 1; color: var(--green); }
</style>

<script>
// ── Riga cliccabile ──────────────────────────────────────
document.querySelectorAll('.clickable-row').forEach(row => {
    row.addEventListener('click', function() {
        window.location.href = this.dataset.href;
    });
});

// ── Ricerca ──────────────────────────────────────────────
const searchInput = document.getElementById('searchInput');
const countLabel  = document.getElementById('countLabel');
const noResults   = document.getElementById('noResults');

function applyFilters() {
    const q    = searchInput.value.toLowerCase().trim();
    const rows = document.querySelectorAll('.supplier-row');
    let visible = 0;

    rows.forEach(row => {
        const show = row.dataset.name.includes(q);
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    countLabel.textContent = visible + ' fornitor' + (visible === 1 ? 'e' : 'i');
    noResults.style.display = visible === 0 ? 'block' : 'none';
}

searchInput.addEventListener('input', applyFilters);
applyFilters();

// ── Ordinamento ───────────────────────────────────────────
let sortCol = -1, sortDir = 1;

document.querySelectorAll('th.sortable').forEach(th => {
    th.addEventListener('click', function() {
        const col = parseInt(this.dataset.col);
        sortDir   = (sortCol === col) ? sortDir * -1 : 1;
        sortCol   = col;

        document.querySelectorAll('th.sortable').forEach(t => t.classList.remove('asc','desc'));
        this.classList.add(sortDir === 1 ? 'asc' : 'desc');

        const tbody = document.getElementById('suppliersBody');
        const rows  = Array.from(tbody.querySelectorAll('.supplier-row'));

        rows.sort((a, b) => {
            const cellA = a.cells[col];
            const cellB = b.cells[col];
            const valA  = cellA.dataset.value;
            const valB  = cellB.dataset.value;
            if (valA !== undefined && valB !== undefined) {
                return (parseFloat(valA) - parseFloat(valB)) * sortDir;
            }
            return cellA.textContent.trim().localeCompare(cellB.textContent.trim()) * sortDir;
        });

        rows.forEach(row => tbody.appendChild(row));
    });
});
</script>

@endsection