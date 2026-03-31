@extends('layouts.app')

@section('page-title', 'Clienti')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">👥 Clienti</div>
        <div class="page-sub">Anagrafica e situazione crediti</div>
    </div>
    <a href="{{ url('/clients/create') }}" class="btn btn-primary">+ Nuovo Cliente</a>
</div>

<div class="card" style="padding:0;overflow:hidden">

    <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:center;flex-wrap:wrap">
        <input type="text" id="searchInput" placeholder="🔍 Cerca cliente..." style="max-width:260px;margin:0">
        <label style="display:flex;align-items:center;gap:7px;font-size:13px;color:var(--muted);cursor:pointer;user-select:none">
            <input type="checkbox" id="filterCrediti" style="width:15px;height:15px;accent-color:var(--green);cursor:pointer">
            Solo con crediti aperti
        </label>
        <span id="countLabel" style="margin-left:auto;font-size:12px;color:var(--muted)"></span>
    </div>

    <table id="clientsTable">
        <thead>
            <tr>
                <th class="sortable" data-col="0" style="cursor:pointer;user-select:none">
                    Cliente <span class="sort-icon">↕</span>
                </th>
                <th>P.IVA</th>
                <th>Città</th>
                <th>Telefono</th>
                <th class="sortable" data-col="4" style="text-align:right;cursor:pointer;user-select:none">
                    Venduto <span class="sort-icon">↕</span>
                </th>
                <th class="sortable" data-col="5" style="text-align:right;cursor:pointer;user-select:none">
                    Pagato <span class="sort-icon">↕</span>
                </th>
                <th class="sortable" data-col="6" style="text-align:right;cursor:pointer;user-select:none">
                    Da Incassare <span class="sort-icon">↕</span>
                </th>
                <th style="text-align:center;width:90px">Azioni</th>
            </tr>
        </thead>
        <tbody id="clientsBody">
        @forelse($clients as $client)
        <tr class="client-row clickable-row"
            data-name="{{ strtolower($client->company_name) }}"
            data-crediti="{{ ($client->da_incassare ?? 0) > 0 ? '1' : '0' }}"
            data-href="{{ url('/clients/' . $client->id) }}"
            style="cursor:pointer">

            <td style="font-weight:700;color:var(--dark)">
                {{ $client->company_name }}
                @if($client->stato === 'inattivo')
                    <span style="font-size:10px;background:#fef3cd;color:#856404;padding:1px 6px;border-radius:3px;margin-left:6px;font-weight:600">INATTIVO</span>
                @endif
            </td>

            <td style="color:var(--muted);font-size:13px">{{ $client->vat_number ?? '—' }}</td>

            <td style="color:var(--muted);font-size:13px">{{ $client->city ?? '—' }}</td>

            <td style="font-size:13px">{{ $client->phone ?? '—' }}</td>

            <td style="text-align:right;font-family:'DM Mono',monospace"
                data-value="{{ $client->totale_venduto ?? 0 }}">
                € {{ number_format($client->totale_venduto ?? 0, 2, ',', '.') }}
            </td>

            <td style="text-align:right;font-family:'DM Mono',monospace"
                data-value="{{ $client->pagato ?? 0 }}">
                € {{ number_format($client->pagato ?? 0, 2, ',', '.') }}
            </td>

            <td style="text-align:right" data-value="{{ $client->da_incassare ?? 0 }}">
                @if(($client->da_incassare ?? 0) > 0)
                    <span style="color:#e74c3c;font-weight:700;font-family:'DM Mono',monospace">
                        € {{ number_format($client->da_incassare, 2, ',', '.') }}
                    </span>
                @else
                    <span style="color:var(--muted);font-family:'DM Mono',monospace">€ 0,00</span>
                @endif
            </td>

            <td style="text-align:center" onclick="event.stopPropagation()">
                <div style="display:flex;gap:6px;justify-content:center">
                    <a href="{{ url('/clients/' . $client->id . '/edit') }}"
                       class="btn btn-secondary"
                       style="padding:5px 10px;font-size:12px"
                       title="Modifica">✏️</a>
                </div>
            </td>

        </tr>
        @empty
        <tr id="emptyRow">
            <td colspan="8" style="text-align:center;padding:48px;color:var(--muted)">
                <div style="font-size:32px;margin-bottom:10px">👥</div>
                <div style="font-weight:600;margin-bottom:6px">Nessun cliente ancora</div>
                <a href="{{ url('/clients/create') }}" style="color:var(--green)">Aggiungi il primo →</a>
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
th.sortable:hover {
    background: var(--green-xl, #f0faf4);
}
th.sortable.asc .sort-icon::after { content: ' ↑'; }
th.sortable.desc .sort-icon::after { content: ' ↓'; }
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
const searchInput   = document.getElementById('searchInput');
const filterCrediti = document.getElementById('filterCrediti');
const countLabel    = document.getElementById('countLabel');
const noResults     = document.getElementById('noResults');

function applyFilters() {
    const q        = searchInput.value.toLowerCase().trim();
    const soloDebt = filterCrediti.checked;
    const rows     = document.querySelectorAll('.client-row');
    let visible    = 0;

    rows.forEach(row => {
        const matchName   = row.dataset.name.includes(q);
        const matchCredit = !soloDebt || row.dataset.crediti === '1';
        const show        = matchName && matchCredit;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    countLabel.textContent = visible + ' client' + (visible === 1 ? 'e' : 'i');
    noResults.style.display = visible === 0 ? 'block' : 'none';
}

searchInput.addEventListener('input', applyFilters);
filterCrediti.addEventListener('change', applyFilters);

// Contatore iniziale
applyFilters();

// ── Ordinamento colonne ───────────────────────────────────
let sortCol = -1, sortDir = 1;

document.querySelectorAll('th.sortable').forEach(th => {
    th.addEventListener('click', function() {
        const col = parseInt(this.dataset.col);
        if (sortCol === col) {
            sortDir *= -1;
        } else {
            sortCol = col;
            sortDir = 1;
        }

        document.querySelectorAll('th.sortable').forEach(t => {
            t.classList.remove('asc', 'desc');
        });
        this.classList.add(sortDir === 1 ? 'asc' : 'desc');

        const tbody = document.getElementById('clientsBody');
        const rows  = Array.from(tbody.querySelectorAll('.client-row'));

        rows.sort((a, b) => {
            const cellA = a.cells[col];
            const cellB = b.cells[col];

            // Colonne numeriche usano data-value
            const valA = cellA.dataset.value;
            const valB = cellB.dataset.value;

            if (valA !== undefined && valB !== undefined) {
                return (parseFloat(valA) - parseFloat(valB)) * sortDir;
            }

            // Colonne testo
            const textA = cellA.textContent.trim().toLowerCase();
            const textB = cellB.textContent.trim().toLowerCase();
            return textA.localeCompare(textB) * sortDir;
        });

        rows.forEach(row => tbody.appendChild(row));
    });
});
</script>

@endsection