@extends('layouts.app')

@section('page-title', 'Modifica Cliente')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">✏️ Modifica Cliente</div>
        <div class="page-sub">{{ $client->company_name }}</div>
    </div>
    <a href="{{ url('/clients') }}" class="btn btn-secondary">← Torna ai clienti</a>
</div>

<div class="form-card" style="max-width:700px">

<form method="POST" action="{{ url('/clients/' . $client->id) }}">
@csrf
@method('PUT')

<div class="form-group">
    <label>Ragione Sociale</label>
    <input type="text" name="ragione_sociale" value="{{ $client->company_name }}" required>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <div class="form-group">
        <label>Partita IVA</label>
        <input type="text" name="partita_iva" value="{{ $client->vat_number }}">
    </div>
    <div class="form-group">
        <label>Codice Fiscale</label>
        <input type="text" name="codice_fiscale" value="{{ $client->fiscal_code }}">
    </div>
</div>

<div class="form-group">
    <label>Indirizzo</label>
    <input type="text" name="indirizzo" value="{{ $client->address }}">
</div>

<div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:16px">
    <div class="form-group">
        <label>Città</label>
        <input type="text" name="citta" value="{{ $client->city }}">
    </div>
    <div class="form-group">
        <label>CAP</label>
        <input type="text" name="cap" value="{{ $client->zip }}">
    </div>
    <div class="form-group">
        <label>Provincia</label>
        <input type="text" name="provincia" value="{{ $client->province }}">
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <div class="form-group">
        <label>Telefono</label>
        <input type="text" name="telefono" value="{{ $client->phone }}">
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ $client->email }}">
    </div>
</div>

<div class="form-group">
    <label>Metodo Pagamento</label>
    <input type="text" name="metodo_pagamento" value="{{ $client->payment_terms }}">
</div>

<div style="display:flex;gap:10px;margin-top:8px">
    <button type="submit" class="btn btn-primary">💾 Salva modifiche</button>
    <a href="{{ url('/clients') }}" class="btn btn-secondary">Annulla</a>
</div>

</form>
</div>

@endsection