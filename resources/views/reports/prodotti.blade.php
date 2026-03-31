@extends('layouts.app')

@section('page-title', 'Report Prodotti')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">📉 Report Prodotti</div>
        <div class="page-sub">Analisi margini per prodotto</div>
    </div>
</div>

<div class="card" style="padding:0">

<table>
<thead>
<tr>
    <th>Prodotto</th>
    <th>Kg Venduti</th>
    <th>Fatturato</th>
    <th>Costo</th>
    <th>Margine</th>
    <th>Margine %</th>
</tr>
</thead>

<tbody>

@forelse($products as $p)

@php
    $margin = $p->fatturato - $p->costo;
    $percent = $p->fatturato > 0 ? ($margin / $p->fatturato) * 100 : 0;
@endphp

<tr>
<td><strong>{{ $p->name }}</strong></td>

<td style="font-family:'DM Mono',monospace">
    {{ number_format($p->kg_venduti,2,',','.') }}
</td>

<td style="font-family:'DM Mono',monospace">
    € {{ number_format($p->fatturato,2,',','.') }}
</td>

<td style="font-family:'DM Mono',monospace">
    € {{ number_format($p->costo,2,',','.') }}
</td>

<td style="font-family:'DM Mono',monospace;font-weight:700;color:{{ $margin >= 0 ? 'var(--green)' : 'red' }}">
    € {{ number_format($margin,2,',','.') }}
</td>

<td>
    <span style="background:var(--green-xl);color:{{ $margin >= 0 ? 'var(--green)' : 'red' }};padding:3px 8px;border-radius:20px;font-size:12px;font-weight:600">
        {{ number_format($percent,1,',','.') }}%
    </span>
</td>

</tr>

@empty

<tr>
<td colspan="6" style="text-align:center;padding:32px;color:var(--muted)">
    Nessun dato disponibile
</td>
</tr>

@endforelse

</tbody>

</table>

</div>

@endsection