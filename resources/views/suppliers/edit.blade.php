@extends('layouts.app')
@section('page-title', 'Modifica Fornitore')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">🏭 Modifica Fornitore</div>
        <div class="page-sub">{{ $supplier->company_name }}</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ url('/suppliers/' . $supplier->id) }}" class="btn btn-secondary">← Torna al fornitore</a>
    </div>
</div>

<div class="form-card">
    <form method="POST" action="{{ route('suppliers.update', $supplier->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Ragione Sociale *</label>
            <input type="text" name="company_name" value="{{ old('company_name', $supplier->company_name) }}" required>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
                <label>P.IVA</label>
                <input type="text" name="vat_number" value="{{ old('vat_number', $supplier->vat_number) }}">
            </div>
            <div class="form-group">
                <label>Telefono</label>
                <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}">
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $supplier->email) }}">
            </div>
            <div class="form-group">
                <label>Città</label>
                <input type="text" name="city" value="{{ old('city', $supplier->city) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Indirizzo</label>
            <input type="text" name="address" value="{{ old('address', $supplier->address) }}">
        </div>

        <div class="form-group">
            <label>Note</label>
            <textarea name="note" rows="3" style="width:100%;resize:vertical">{{ old('note', $supplier->note) }}</textarea>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px">
            <button type="submit" class="btn btn-primary">💾 Salva Modifiche</button>
            <a href="{{ url('/suppliers/' . $supplier->id) }}" class="btn btn-secondary">Annulla</a>
        </div>

    </form>
</div>

@endsection