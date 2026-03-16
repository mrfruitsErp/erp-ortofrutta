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

    <div style="padding:14px 18px;border-bottom:1px solid var(--border)">
        <input type="text" id="searchInput" placeholder="🔍 Cerca cliente..." style="max-width:300px;margin:0">
    </div>

    <table id="clientsTable">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>P.IVA</th>
                <th>Città</th>
                <th>Telefono</th>
                <th style="text-align:right">Venduto</th>
                <th style="text-align:right">Pagato</th>
                <th style="text-align:right">Da Incassare</th>
                <th style="text-align:center;width:130px">Azioni</th>
            </tr>
        </thead>
        <tbody>
        @forelse($clients as $client)
        <tr class="client-row" data-name="{{ strtolower($client->company_name) }}">

            <td style="font-weight:700;color:var(--dark)">{{ $client->company_name }}</td>

            <td style="color:var(--muted);font-size:13px">{{ $client->vat_number ?? '—' }}</td>

            <td style="color:var(--muted);font-size:13px">{{ $client->city ?? '—' }}</td>

            <td style="font-size:13px">{{ $client->phone ?? '—' }}</td>

            <td style="text-align:right;font-family:'DM Mono',monospace">
                € {{ number_format($client->totale_venduto ?? 0, 2, ',', '.') }}
            </td>

            <td style="text-align:right;font-family:'DM Mono',monospace">
                € {{ number_format($client->pagato ?? 0, 2, ',', '.') }}
            </td>

            <td style="text-align:right">
                @if(($client->da_incassare ?? 0) > 0)
                    <span style="color:#e74c3c;font-weight:700;font-family:'DM Mono',monospace">
                        € {{ number_format($client->da_incassare, 2, ',', '.') }}
                    </span>
                @else
                    <span style="color:var(--muted);font-family:'DM Mono',monospace">€ 0,00</span>
                @endif
            </td>

            <td style="text-align:center">
                <div style="display:flex;gap:6px;justify-content:center">
                    <a href="{{ url('/clients/' . $client->id) }}" class="btn btn-secondary" style="padding:5px 10px;font-size:12px">👁</a>
                    <a href="{{ url('/clients/' . $client->id . '/edit') }}" class="btn btn-secondary" style="padding:5px 10px;font-size:12px">✏️</a>
                </div>
            </td>

        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center;padding:40px;color:var(--muted)">
                Nessun cliente. <a href="{{ url('/clients/create') }}">Aggiungi il primo →</a>
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>

</div>

<script>
document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.client-row').forEach(row => {
        row.style.display = row.dataset.name.includes(q) ? '' : 'none';
    });
});
</script>

@endsection