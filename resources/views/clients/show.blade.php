@extends('layouts.app')

@section('page-title', 'Cliente')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">👤 {{ $client->company_name }}</div>
        <div class="page-sub">{{ $client->city ?? '' }} {{ $client->province ? '(' . $client->province . ')' : '' }}</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ url('/clients') }}" class="btn btn-secondary">← Clienti</a>
        <a href="{{ url('/clients/' . $client->id . '/edit') }}" class="btn btn-secondary">✏️ Modifica</a>
    </div>
</div>

{{-- KPI --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">

    <div class="card" style="padding:16px 20px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Fatturato Totale</div>
        <div style="font-size:22px;font-weight:700;font-family:'DM Mono',monospace;color:var(--dark)">€ {{ number_format($revenue,2,',','.') }}</div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Costo Merce</div>
        <div style="font-size:22px;font-weight:700;font-family:'DM Mono',monospace;color:var(--dark)">€ {{ number_format($cost,2,',','.') }}</div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Margine</div>
        <div style="font-size:22px;font-weight:700;font-family:'DM Mono',monospace;color:var(--green)">€ {{ number_format($margin,2,',','.') }}</div>
        <div style="font-size:12px;color:var(--muted)">{{ number_format($margin_percent,1,',','.') }}%</div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Documenti</div>
        <div style="font-size:22px;font-weight:700;font-family:'DM Mono',monospace;color:var(--dark)">{{ $documents->count() }}</div>
    </div>

</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:20px">

    {{-- DATI ANAGRAFICI --}}
    <div class="card">
        <div style="font-weight:700;font-size:14px;color:var(--dark);margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border)">
            📋 Dati Anagrafici
        </div>
        <div style="display:flex;flex-direction:column;gap:10px">
            @foreach([
                ['P.IVA', $client->vat_number],
                ['Cod. Fiscale', $client->fiscal_code],
                ['Indirizzo', $client->address],
                ['Città', ($client->city ?? '') . ($client->zip ? ' ' . $client->zip : '') . ($client->province ? ' (' . $client->province . ')' : '')],
                ['Telefono', $client->phone],
                ['Email', $client->email],
                ['Pagamento', $client->payment_terms],
            ] as [$label, $value])
            <div style="display:flex;justify-content:space-between;font-size:13px;padding-bottom:8px;border-bottom:1px solid var(--border)">
                <span style="color:var(--muted);font-weight:600">{{ $label }}</span>
                <span style="color:var(--dark);font-weight:500">{{ $value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- DOCUMENTI --}}
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:14px 18px;border-bottom:1px solid var(--border);font-weight:700;font-size:14px;color:var(--dark)">
            📄 Documenti
        </div>
        <table>
            <thead>
                <tr>
                    <th>Numero</th>
                    <th>Data</th>
                    <th style="text-align:right">Totale</th>
                    <th style="text-align:center">Azioni</th>
                </tr>
            </thead>
            <tbody>
            @forelse($documents as $doc)
            <tr>
                <td style="font-family:'DM Mono',monospace;font-weight:600;color:var(--green)">{{ $doc->number }}</td>
                <td style="color:var(--muted)">{{ \Carbon\Carbon::parse($doc->date)->format('d/m/Y') }}</td>
                <td style="text-align:right;font-family:'DM Mono',monospace;font-weight:700">€ {{ number_format($doc->total,2,',','.') }}</td>
                <td style="text-align:center">
                    <a href="{{ url('/documents/' . $doc->id) }}" class="btn btn-secondary" style="padding:4px 10px;font-size:12px">Apri</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;padding:20px;color:var(--muted)">Nessun documento</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection