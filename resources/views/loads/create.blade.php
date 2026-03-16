@extends('layouts.app')
@section('page-title', 'Carico Magazzino')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">🚛 Carico Magazzino</div>
        <div class="page-sub">Registra entrata merce</div>
    </div>
    <a href="{{ url('/magazzino') }}" class="btn btn-secondary">← Magazzino</a>
</div>
<div class="form-card">
<form method="POST" action="/loads">
@csrf
<div class="form-group">
    <label>Prodotto</label>
    <select name="product_id" required>
        <option value="">-- Seleziona prodotto --</option>
        @foreach($products as $product)
        <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
    </select>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
<div class="form-group">
    <label>Quantità KG</label>
    <input type="number" step="0.01" name="qty" required placeholder="0.00">
</div>
<div class="form-group">
    <label>Prezzo Costo €/kg</label>
    <input type="number" step="0.01" name="cost_price" required placeholder="0.00">
</div>
</div>
<div style="margin-top:8px">
    <button type="submit" class="btn btn-primary">📦 Carica Magazzino</button>
</div>
</form>
</div>
@endsection
