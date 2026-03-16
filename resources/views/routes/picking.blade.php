@extends('layouts.app')
@section('page-title', 'Picking — ' . ($route->name ?? ''))
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">📋 Picking — {{ $route->name }}</div>
        <div class="page-sub">Giorno: {{ $route->day }}</div>
    </div>
    <a href="{{ url('/routes') }}" class="btn btn-secondary">← Torna ai giri</a>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">
    <div>
        <div class="card" style="padding:0;margin-bottom:20px">
            <div style="padding:16px 20px;border-bottom:1px solid var(--border);font-size:14px;font-weight:600;color:var(--dark)">📦 Prodotti da caricare</div>
            <table>
            <thead>
                <tr><th>Prodotto</th><th>Casse Totali</th><th>Kg Stimati</th></tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
            <tr>
                <td><strong>{{ $row->product }}</strong></td>
                <td style="font-family:'DM Mono',monospace;font-weight:700;font-size:16px">{{ $row->boxes }}</td>
                <td style="font-family:'DM Mono',monospace">{{ number_format($row->kg,2,',','.') }} kg</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;padding:32px;color:var(--muted)">Nessun prodotto nel giro</td></tr>
            @endforelse
            </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom:20px">
            <div style="font-size:13px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px">👥 Clienti del giro</div>
            @forelse($clients as $i => $client)
            <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border)">
                <span style="width:24px;height:24px;background:var(--green-xl);color:var(--green);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700">{{ $i+1 }}</span>
                <span style="font-size:13px;font-weight:500">{{ $client->name }}</span>
            </div>
            @empty
            <div style="color:var(--muted);font-size:13px">Nessun cliente assegnato</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
