@extends('layouts.app')
@section('page-title', 'Acquisti')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">🛒 Acquisti</div>
        <div class="page-sub">Storico acquisti da fornitori</div>
    </div>
    <a href="{{ url('/purchases/create') }}" class="btn btn-primary">+ Nuovo Acquisto</a>
</div>
<div class="card" style="padding:0">
<table>
<thead>
<tr><th>ID</th><th>Data</th><th>Fornitore</th><th>Prodotto</th><th>Kg</th><th>€/kg</th><th>Totale</th></tr>
</thead>
<tbody>
@forelse($purchases as $p)
<tr>
<td style="color:var(--muted);font-family:'DM Mono',monospace">{{ $p->id }}</td>
<td>{{ $p->date }}</td>
<td>{{ $p->supplier->name ?? '' }}</td>
<td><strong>{{ $p->product->name ?? '' }}</strong></td>
<td style="font-family:'DM Mono',monospace">{{ $p->qty }}</td>
<td style="font-family:'DM Mono',monospace">€ {{ number_format($p->cost_per_kg,2,',','.') }}</td>
<td style="font-family:'DM Mono',monospace;font-weight:600">€ {{ number_format($p->total,2,',','.') }}</td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--muted)">Nessun acquisto registrato</td></tr>
@endforelse
</tbody>
</table>
</div>
@endsection
