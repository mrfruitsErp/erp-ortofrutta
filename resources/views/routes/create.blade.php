@extends('layouts.app')
@section('page-title', 'Nuovo Giro Consegna')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">🗺️ Nuovo Giro Consegna</div>
    </div>
    <a href="{{ url('/routes') }}" class="btn btn-secondary">← Torna ai giri</a>
</div>
<div class="form-card">
<form method="POST" action="/routes">
@csrf
<div class="form-group">
    <label>Nome Giro</label>
    <input type="text" name="name" required placeholder="Es. Giro Nord">
</div>
<div class="form-group">
    <label>Giorno</label>
    <select name="day">
        <option>Lunedì</option>
        <option>Martedì</option>
        <option>Mercoledì</option>
        <option>Giovedì</option>
        <option>Venerdì</option>
        <option>Sabato</option>
    </select>
</div>
<div style="margin-top:8px">
    <button type="submit" class="btn btn-primary">💾 Salva Giro</button>
</div>
</form>
</div>
@endsection
