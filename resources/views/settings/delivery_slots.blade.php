@extends('layouts.app')

@section('page-title','Slot Consegna')

@section('content')

<div class="page-header">
<div>
<div class="page-title">⏱ Slot Consegna</div>
<div class="page-sub">Gestione fasce orarie di consegna</div>
</div>
</div>

<div class="card" style="margin-bottom:20px">

<form method="POST" action="/settings/delivery-slots">

@csrf

<div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;align-items:end">

<div class="form-group">
<label>Nome slot</label>
<input type="text" name="name" required placeholder="Es. Mattina">
</div>

<div class="form-group">
<label>Ora inizio</label>
<input type="time" name="start_time" required>
</div>

<div class="form-group">
<label>Ora fine</label>
<input type="time" name="end_time" required>
</div>

<div class="form-group">
<label>&nbsp;</label>
<button class="btn btn-primary">+ Aggiungi</button>
</div>

</div>

</form>

</div>

<div class="card" style="padding:0;overflow:hidden">

<table>

<thead>
<tr>
<th>Nome</th>
<th>Ora inizio</th>
<th>Ora fine</th>
<th style="text-align:center">Attivo</th>
<th style="text-align:center">Azioni</th>
</tr>
</thead>

<tbody>

@forelse($slots as $slot)

<tr>

<form method="POST" action="/settings/delivery-slots/{{ $slot->id }}">

@csrf

<td>
<input type="text" name="name" value="{{ $slot->name }}">
</td>

<td>
<input type="time" name="start_time" value="{{ $slot->start_time }}">
</td>

<td>
<input type="time" name="end_time" value="{{ $slot->end_time }}">
</td>

<td style="text-align:center">
<input type="checkbox" name="active" value="1" {{ $slot->active ? 'checked' : '' }}>
</td>

<td style="text-align:center">

<button class="btn btn-primary" style="padding:5px 10px;font-size:12px">
Salva
</button>

</form>

<form method="POST" action="/settings/delivery-slots/{{ $slot->id }}" style="display:inline">

@csrf
@method('DELETE')

<button class="btn btn-secondary" style="padding:5px 10px;font-size:12px">
Elimina
</button>

</form>

</td>

</tr>

@empty

<tr>
<td colspan="5" style="text-align:center;padding:30px;color:var(--muted)">
Nessuno slot creato
</td>
</tr>

@endforelse

</tbody>

</table>

</div>

@endsection
