@extends('layouts.app')
@section('page-title', 'Giri Consegna')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">🗺️ Giri Consegna</div>
        <div class="page-sub">Gestione percorsi di consegna</div>
    </div>
    <a href="{{ url('/routes/create') }}" class="btn btn-primary">+ Nuovo Giro</a>
</div>
<div class="card" style="padding:0">
<table>
<thead>
<tr><th>ID</th><th>Nome Giro</th><th>Giorno</th><th>Picking</th></tr>
</thead>
<tbody>
@forelse($routes as $route)
<tr>
<td style="color:var(--muted);font-family:'DM Mono',monospace">{{ $route->id }}</td>
<td><strong>{{ $route->name }}</strong></td>
<td>{{ $route->day }}</td>
<td><a href="{{ url('/route-picking/'.$route->id) }}" class="btn btn-primary" style="padding:5px 14px;font-size:12px">📋 Apri Picking</a></td>
</tr>
@empty
<tr><td colspan="4" style="text-align:center;padding:32px;color:var(--muted)">Nessun giro creato ancora</td></tr>
@endforelse
</tbody>
</table>
</div>
@endsection
