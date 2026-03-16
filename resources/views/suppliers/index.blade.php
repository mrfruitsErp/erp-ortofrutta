@extends('layouts.app')
@section('page-title', 'Fornitori')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">🏭 Fornitori</div>
        <div class="page-sub">Gestione anagrafica fornitori</div>
    </div>
    <a href="{{ url('/suppliers/create') }}" class="btn btn-primary">+ Nuovo Fornitore</a>
</div>
<div class="card" style="padding:0">
<table>
<thead>
<tr><th>ID</th><th>Ragione Sociale</th><th>P.IVA</th><th>Città</th><th>Telefono</th></tr>
</thead>
<tbody>
@forelse($suppliers as $supplier)
<tr>
<td style="color:var(--muted);font-family:'DM Mono',monospace">{{ $supplier->id }}</td>
<td><strong>{{ $supplier->company_name }}</strong></td>
<td style="font-family:'DM Mono',monospace">{{ $supplier->vat_number }}</td>
<td>{{ $supplier->city }}</td>
<td>{{ $supplier->phone }}</td>
</tr>
@empty
<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--muted)">Nessun fornitore ancora</td></tr>
@endforelse
</tbody>
</table>
</div>
@endsection
