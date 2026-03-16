@extends('layouts.app')
@section('page-title', 'Nuovo Fornitore')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">🏭 Nuovo Fornitore</div>
    </div>
    <a href="{{ url('/suppliers') }}" class="btn btn-secondary">← Torna ai fornitori</a>
</div>
<div class="form-card">
<form method="POST" action="/suppliers">
@csrf
<div class="form-group">
    <label>Ragione Sociale</label>
    <input type="text" name="company_name" required>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
<div class="form-group">
    <label>P.IVA</label>
    <input type="text" name="vat_number">
</div>
<div class="form-group">
    <label>Telefono</label>
    <input type="text" name="phone">
</div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
<div class="form-group">
    <label>Email</label>
    <input type="email" name="email">
</div>
<div class="form-group">
    <label>Città</label>
    <input type="text" name="city">
</div>
</div>
<div style="margin-top:8px">
    <button type="submit" class="btn btn-primary">💾 Salva Fornitore</button>
</div>
</form>
</div>
@endsection
