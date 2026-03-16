@extends('layouts.app')

@section('page-title','Impostazioni Ordini')

@section('content')

<div class="page-header">

<div>

<div class="page-title">Impostazioni Ordini</div>

<div class="page-sub">Configurazione portale clienti</div>

</div>

</div>

<div class="card">

<form method="POST">

@csrf

<!-- ATTIVA ORDINI -->

<div class="form-group">

<label>
<input type="checkbox" name="orders_enabled" value="1"
{{ $orders_enabled ? 'checked' : '' }}>
Attiva ordini cliente
</label>

</div>

<!-- ORA CHIUSURA -->

<div class="form-group">

<label>Ora chiusura ordini</label>

<input
type="time"
name="cutoff"
value="{{ $cutoff }}"
required

>

</div>

<!-- ORDINE MINIMO -->

<div class="form-group">

<label>Ordine minimo €</label>

<input
type="number"
step="0.01"
name="minimum_order"
value="{{ $minimum_order }}"

>

</div>

<!-- RIORDINO ORDINE -->

<div class="form-group">

<label>
<input type="checkbox"
name="reorder_enabled"
value="1"
{{ $reorder_enabled ? 'checked' : '' }}>
Attiva riordino ordine precedente
</label>

</div>

<button class="btn btn-primary">
Salva impostazioni
</button>

</form>

</div>

@endsection
