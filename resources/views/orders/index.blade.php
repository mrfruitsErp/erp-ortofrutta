@extends('layouts.app')

@section('page-title','Ordini')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">📦 Ordini</div>
        <div class="page-sub">Ordini clienti</div>
    </div>
    <a href="/orders/create" class="btn btn-primary">+ Nuovo Ordine</a>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px;padding:12px 16px;border-radius:8px;background:#d4edda;color:#155724;font-size:14px">
        ✓ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="margin-bottom:16px;padding:12px 16px;border-radius:8px;background:#fde8e8;color:#8b0000;font-size:14px">
        ⚠ {{ session('error') }}
    </div>
@endif

<div class="card" style="padding:0;overflow:hidden">

    {{-- FILTRI --}}
    <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;gap:10px;flex-wrap:wrap;align-items:center">

        <input type="text" id="searchInput" placeholder="🔍 Cerca numero o cliente..."
               style="max-width:230px;margin:0">

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

    <table>
        <thead>
            <tr>
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
            $isEvaso   = $order->status === 'invoiced';
            $isBozza   = $order->status === 'draft';
            $isWeb     = $order->status === 'web';
            $dataRaw   = \Carbon\Carbon::parse($order->date)->format('Y-m-d');
        @endphp
        <tr class="order-row clickable-row"
            data-number="{{ strtolower($order->number ?? '') }}"
            data-client="{{ strtolower($order->client->company_name ?? '') }}"
            data-stato="{{ $order->status }}"
            data-date="{{ $dataRaw }}"
            data-href="/orders/{{ $order->id }}"
            style="cursor:pointer">

            <td style="color:var(--muted);font-size:12px">{{ $order->id }}</td>

            <td>
                @if($order->number)
                    <span style="font-weight:600;color:var(--green);font-family:'DM Mono',monospace;font-size:13px">
                        {{ $order->number }}
                    </span>
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
                <span style="color:var(--muted);font-size:11px;margin-left:4px">
                    {{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}
                </span>
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

            {{-- AZIONI: coerenti per stato --}}
            <td style="text-align:center" onclick="event.stopPropagation()">
                <div style="display:flex;gap:5px;justify-content:center;align-items:center">

                    @if(!$isEvaso)
                        {{-- Modifica: solo se non evaso --}}
                        <a href="/orders/{{ $order->id }}/edit"
                           class="btn btn-secondary"
                           style="padding:4px 9px;font-size:12px"
                           title="Modifica">✏️</a>
                    @endif

                    @if($isBozza || $isWeb)
                        {{-- Elimina: solo se bozza o web --}}
                        <form method="POST" action="/orders/{{ $order->id }}"
                              style="margin:0"
                              onsubmit="return confirm('Eliminare l\'ordine {{ addslashes($order->number ?? 'questo ordine') }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-secondary"
                                    style="padding:4px 9px;font-size:12px;color:#c0392b;border-color:#fcc"
                                    title="Elimina">🗑</button>
                        </form>
                    @endif

                    @if($isEvaso)
                        {{-- Evaso: mostra link al DDT se disponibile --}}
                        @if($order->documents && $order->documents->first())
                            <a href="/documents/{{ $order->documents->first()->id }}"
                               class="btn btn-secondary"
                               style="padding:4px 9px;font-size:11px;color:var(--green)"
                               title="Apri DDT">📄 DDT</a>
                        @else
                            <span style="font-size:11px;color:var(--muted)">—</span>
                        @endif
                    @endif

                </div>
            </td>

        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;padding:48px;color:var(--muted)">
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

<style>
.clickable-row:hover td { background: var(--green-xl, #f0faf4) !important; }
.clickable-row:hover td:nth-child(2) span:first-child { color: var(--green); }
</style>

<script>
// ── Riga cliccabile ───────────────────────────────────────
document.querySelectorAll('.clickable-row').forEach(row => {
    row.addEventListener('click', function() {
        window.location.href = this.dataset.href;
    });
});

// ── Filtri ────────────────────────────────────────────────
function getDateBounds(periodo) {
    const now   = new Date();
    const today = now.toISOString().slice(0, 10);

    if (periodo === 'oggi') {
        return { from: today, to: today };
    }
    if (periodo === 'settimana') {
        const day  = now.getDay() || 7;
        const mon  = new Date(now); mon.setDate(now.getDate() - day + 1);
        const sun  = new Date(mon); sun.setDate(mon.getDate() + 6);
        return { from: mon.toISOString().slice(0,10), to: sun.toISOString().slice(0,10) };
    }
    if (periodo === 'mese') {
        const from = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().slice(0,10);
        const to   = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().slice(0,10);
        return { from, to };
    }
    if (periodo === 'mese_scorso') {
        const from = new Date(now.getFullYear(), now.getMonth() - 1, 1).toISOString().slice(0,10);
        const to   = new Date(now.getFullYear(), now.getMonth(), 0).toISOString().slice(0,10);
        return { from, to };
    }
    return null;
}

function filterRows() {
    const q       = document.getElementById('searchInput').value.toLowerCase().trim();
    const stato   = document.getElementById('filterStato').value;
    const periodo = document.getElementById('filterPeriodo').value;
    const bounds  = getDateBounds(periodo);
    let visible   = 0;

    document.querySelectorAll('.order-row').forEach(row => {
        const matchText  = !q     || row.dataset.number.includes(q) || row.dataset.client.includes(q);
        const matchStato = !stato || row.dataset.stato === stato;
        let matchDate    = true;

        if (bounds) {
            const d = row.dataset.date;
            matchDate = d >= bounds.from && d <= bounds.to;
        }

        const show = matchText && matchStato && matchDate;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const total = document.querySelectorAll('.order-row').length;
    document.getElementById('countLabel').textContent =
        visible === total ? total + ' ordini' : visible + ' di ' + total + ' ordini';
    document.getElementById('noResults').style.display =
        visible === 0 && total > 0 ? 'block' : 'none';
}

function resetFiltri() {
    document.getElementById('searchInput').value  = '';
    document.getElementById('filterStato').value  = '';
    document.getElementById('filterPeriodo').value = '';
    filterRows();
}

document.getElementById('searchInput').addEventListener('input', filterRows);
document.getElementById('filterStato').addEventListener('change', filterRows);
document.getElementById('filterPeriodo').addEventListener('change', filterRows);

filterRows();
</script>

@endsection