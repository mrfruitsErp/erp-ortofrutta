@extends('layouts.app')
@section('page-title', 'Report Clienti')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">👤 Report Clienti</div>
        <div class="page-sub">Analisi margini per cliente</div>
    </div>
</div>
<div class="card" style="padding:0">
<table>
<thead>
<tr><th>Cliente</th><th>Fatturato</th><th>Costo</th><th>Margine</th><th>Margine %</th></tr>
</thead>
<tbody>
@forelse($clients as $c)
<tr>
<td><strong>{{ $c['name'] }}</strong></td>
<td style="font-family:'DM Mono',monospace">€ {{ number_format($c['revenue'],2,',','.') }}</td>
<td style="font-family:'DM Mono',monospace">€ {{ number_format($c['cost'],2,',','.') }}</td>
<td style="font-family:'DM Mono',monospace;font-weight:700;color:var(--green)">€ {{ number_format($c['margin'],2,',','.') }}</td>
<td>
    <span style="background:var(--green-xl);color:var(--green);padding:3px 8px;border-radius:20px;font-size:12px;font-weight:600">
        {{ number_format($c['margin_percent'],1,',','.') }}%
    </span>
</td>
</tr>
@empty
<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--muted)">Nessun dato disponibile</td></tr>
@endforelse
</tbody>
</table>
</div>
@endsection
