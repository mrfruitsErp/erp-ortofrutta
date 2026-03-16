@extends('layouts.app')
@section('page-title', 'Nuovo Cliente')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">👤 Nuovo Cliente</div>
        <div class="page-sub">Inserisci i dati del nuovo cliente</div>
    </div>
    <a href="{{ url('/clients') }}" class="btn btn-secondary">← Torna ai clienti</a>
</div>
<div class="form-card">
<form method="POST" action="{{ route('clients.store') }}">
@csrf
<div class="form-group">
    <label>Ragione Sociale</label>
    <input type="text" name="ragione_sociale" required placeholder="Es. Frutta Rossi Srl">
</div>
<div class="form-group">
    <label>Partita IVA</label>
    <input type="text" name="partita_iva" placeholder="Es. 01234567890">
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
<div class="form-group">
    <label>Indirizzo</label>
    <input type="text" name="indirizzo">
</div>
<div class="form-group">
    <label>Città</label>
    <input type="text" name="citta">
</div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
<div class="form-group">
    <label>Telefono</label>
    <input type="text" name="telefono">
</div>
<div class="form-group">
    <label>Email</label>
    <input type="email" name="email">
</div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
<div class="form-group">
    <label>Fido €</label>
    <input type="number" step="0.01" name="fido" value="0">
</div>
<div class="form-group">
    <label>Giorni Pagamento</label>
    <input type="number" name="giorni_pagamento" value="30">
</div>
<div class="form-group">
    <label>Metodo Pagamento</label>
    <input type="text" name="metodo_pagamento" value="bonifico">
</div>
</div>
<div style="margin-top:8px">
    <button type="submit" class="btn btn-primary">💾 Salva Cliente</button>
</div>
</form>
</div>
@endsection
