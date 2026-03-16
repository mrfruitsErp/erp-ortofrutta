@extends('layouts.app')
@section('page-title', 'Report Vendite')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">📈 Report Vendite</div>
        <div class="page-sub">Margini per prodotto</div>
    </div>
</div>
<div class="card" style="padding:0">
<table>
<thead>
<tr><th>Prodotto</th><th>KG Venduti</th><th>Fatturato €</th><th>Costo €</th><th>Margine €</th><th>Margine %</th></tr>
</thead>
<tbody>
@forelse($rows as $row)
@php $margin = $row->fatturato - $row->costo; $pct = $row->fatturato > 0 ? ($margin/$row->fatturato)*100 : 0; @endphp
<tr>
<td><strong>{{ $row->name }}</strong></td>
<td style="font-family:'DM Mono',monospace">{{ number_format($row->kg_venduti,2,',','.') }}</td>
<td style="font-family:'DM Mono',monospace">€ {{ number_format($row->fatturato,2,',','.') }}</td>
<td style="font-family:'DM Mono',monospace">€ {{ number_format($row->costo,2,',','.') }}</td>
<td style="font-family:'DM Mono',monospace;font-weight:700;color:var(--green)">€ {{ number_format($margin,2,',','.') }}</td>
<td>
    <span style="background:var(--green-xl);color:var(--green);padding:3px 8px;border-radius:20px;font-size:12px;font-weight:600">
        {{ number_format($pct,1,',','.') }}%
    </span>
</td>
</tr>
@empty
<tr><td colspan="6" style="text-align:center;padding:32px;color:var(--muted)">Nessun dato disponibile</td></tr>
@endforelse
</tbody>
</table>
</div>
@endsection
