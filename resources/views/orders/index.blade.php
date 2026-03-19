@extends('layouts.app')

@section('page-title','Ordini')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Ordini</div>
        <div class="page-sub">Ordini clienti</div>
    </div>
    <a href="/orders/create" class="btn btn-primary">+ Nuovo Ordine</a>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px">{{ session('success') }}</div>
@endif

<div class="card" style="padding:0;overflow:hidden">

    {{-- FILTRI --}}
    <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;gap:12px;flex-wrap:wrap;align-items:center">
        <input type="text" id="searchInput" placeholder="🔍 Cerca numero o cliente..."
               style="max-width:260px;margin:0">
        <select id="filterStato" style="max-width:160px;margin:0">
            <option value="">Tutti gli stati</option>
            <option value="draft">Bozza</option>
            <option value="confirmed">Confermato</option>
            <option value="invoiced">Evaso</option>
        </select>
        <button onclick="resetFiltri()" class="btn btn-secondary" style="padding:7px 14px;font-size:13px">✕ Reset</button>
        <span id="countLabel" style="font-size:12px;color:var(--muted);margin-left:auto"></span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>Numero</th>
                <th>Cliente</th>
                <th>Data</th>
                <th style="text-align:center">Stato</th>
                <th style="text-align:right">Totale</th>
                <th style="text-align:center;width:160px">Azioni</th>
            </tr>
        </thead>
        <tbody id="ordersBody">
        @forelse($orders as $order)
        <tr class="order-row"
            data-number="{{ strtolower($order->number ?? '') }}"
            data-client="{{ strtolower($order->client->company_name ?? '') }}"
            data-stato="{{ $order->status }}">

            <td style="color:var(--muted);font-size:12px">{{ $order->id }}</td>

            <td>
                @if($order->number)
                    <a href="/orders/{{ $order->id }}"
                       style="font-weight:600;color:var(--green);text-decoration:none">
                        {{ $order->number }}
                    </a>
                @else
                    <span style="color:#c0392b;font-size:12px">⚠ Numero mancante</span>
                @endif
            </td>

            <td>{{ $order->client->company_name ?? '—' }}</td>

            <td>{{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}</td>

            <td style="text-align:center">
                @if($order->status == 'draft')
                    <span style="background:#fff3e0;color:#e65100;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">● Bozza</span>
                @elseif($order->status == 'confirmed')
                    <span style="background:#e3f0ff;color:#1a56a0;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">● Confermato</span>
                @elseif($order->status == 'invoiced')
                    <span style="background:var(--green-xl);color:var(--green);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">● Evaso</span>
                @else
                    <span style="background:#f3f4f6;color:#6b7280;padding:3px 10px;border-radius:20px;font-size:11px">{{ $order->status }}</span>
                @endif
            </td>

            <td style="text-align:right;font-weight:700;font-family:'DM Mono',monospace">
                € {{ number_format($order->total, 2, ',', '.') }}
            </td>

            <td style="text-align:center">
                <div style="display:flex;gap:6px;justify-content:center">

                    {{-- Visualizza --}}
                    <a href="/orders/{{ $order->id }}"
                       class="btn btn-secondary"
                       style="padding:4px 10px;font-size:12px"
                       title="Apri">👁</a>

                    {{-- Modifica (solo se non evaso) --}}
                    @if($order->status !== 'invoiced')
                        <a href="/orders/{{ $order->id }}/edit"
                           class="btn btn-secondary"
                           style="padding:4px 10px;font-size:12px"
                           title="Modifica">✏️</a>
                    @endif

                    {{-- Elimina (solo se bozza) --}}
                    @if($order->status === 'draft')
                        <form method="POST" action="/orders/{{ $order->id }}"
                              onsubmit="return confirm('Eliminare l\'ordine {{ $order->number }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-secondary"
                                    style="padding:4px 10px;font-size:12px;color:#c0392b"
                                    title="Elimina">🗑</button>
                        </form>
                    @endif

                </div>
            </td>

        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">
                Nessun ordine trovato
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>

</div>

<script>
function filterRows(){
    const q     = document.getElementById('searchInput').value.toLowerCase();
    const stato = document.getElementById('filterStato').value;
    let visible = 0;

    document.querySelectorAll('.order-row').forEach(row => {
        const match =
            (!q     || row.dataset.number.includes(q) || row.dataset.client.includes(q)) &&
            (!stato || row.dataset.stato === stato);
        row.style.display = match ? '' : 'none';
        if(match) visible++;
    });

    document.getElementById('countLabel').textContent = visible + ' ordini';
}

function resetFiltri(){
    document.getElementById('searchInput').value  = '';
    document.getElementById('filterStato').value  = '';
    filterRows();
}

document.getElementById('searchInput').addEventListener('input', filterRows);
document.getElementById('filterStato').addEventListener('change', filterRows);

filterRows();
</script>

@endsection