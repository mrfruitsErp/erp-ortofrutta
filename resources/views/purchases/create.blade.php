@extends('layouts.app')
@section('page-title', 'Nuovo Acquisto')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">🛒 Nuovo Acquisto</div>
    </div>
    <a href="{{ url('/purchases') }}" class="btn btn-secondary">← Torna agli acquisti</a>
</div>
<div class="form-card">
<form method="POST" action="/purchases">
@csrf
<div class="form-group">
    <label>Fornitore</label>
    <select name="supplier_id" required>
        <option value="">-- Seleziona fornitore --</option>
        @foreach($suppliers as $supplier)
        <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label>Prodotto</label>
    <select name="product_id" required>
        <option value="">-- Seleziona prodotto --</option>
        @foreach($products as $product)
        <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
    </select>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
<div class="form-group">
    <label>KG Acquistati</label>
    <input type="number" step="0.01" name="kg" required placeholder="0.00">
</div>
<div class="form-group">
    <label>Prezzo €/kg</label>
    <input type="number" step="0.01" name="price" required placeholder="0.00">
</div>
<div class="form-group">
    <label>Data</label>
    <input type="date" name="date" required value="{{ date('Y-m-d') }}">
</div>
</div>
<div style="margin-top:8px">
    <button type="submit" class="btn btn-primary">💾 Salva Acquisto</button>
</div>
</form>
</div>
@endsection
