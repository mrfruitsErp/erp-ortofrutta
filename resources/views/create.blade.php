@extends('layouts.app')

@section('page-title', 'Nuovo Prodotto')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">🧺 Nuovo Prodotto</div>
        <div class="page-sub">Inserisci i dati del prodotto</div>
    </div>

    <a href="{{ url('/products') }}" class="btn btn-secondary">
        ← Torna ai prodotti
    </a>
</div>

<div class="form-card">

<form method="POST" action="/products">
@csrf

<div class="form-group">
<label>Nome Prodotto</label>
<input type="text" name="name" required placeholder="Es. Mele Golden">
</div>


<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

<div class="form-group">
<label>Orig.</label>
<input type="text" name="origin" placeholder="IT, ES, ZA">
</div>

<div class="form-group">
<label>Unità di Misura</label>
<select name="unit">
<option value="kg">Kg</option>
<option value="cassa">Cassa</option>
<option value="pz">Pezzi</option>
</select>
</div>

</div>


<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

<div class="form-group">
<label>Tara Unitaria</label>
<input type="number" step="0.001" name="tara" value="0">
</div>

<div class="form-group">
<label>Peso Medio Cassa</label>
<input type="number" step="0.001" name="avg_box_weight" value="0">
</div>

</div>


<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

<div class="form-group">
<label>Prezzo di Vendita €</label>
<input type="number" name="price" step="0.01" value="0">
</div>

<div class="form-group">
<label>Prezzo di Costo €</label>
<input type="number" name="cost_price" step="0.01" value="0">
</div>

</div>


<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

<div class="form-group">
<label>Stock Iniziale</label>
<input type="number" name="stock" step="0.01" value="0">
</div>

</div>


<div style="margin-top:8px">
<button type="submit" class="btn btn-primary">
💾 Salva Prodotto
</button>
</div>

</form>

</div>

@endsection