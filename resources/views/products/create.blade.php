@extends('layouts.app')

@section('page-title', 'Nuovo Prodotto')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">➕ Nuovo Prodotto</div>
        <div class="page-sub">Inserisci un nuovo prodotto nel catalogo</div>
    </div>
</div>

<div class="card" style="max-width:600px">

<form method="POST" action="/products">

@csrf

<div class="form-group">

<label>Nome prodotto</label>

<input type="text" name="name" required>

</div>


<div class="form-group">

<label>Origine</label>

<input type="text" name="origin">

</div>


<div class="form-group">

<label>Unità di misura</label>

<select name="unit">

<option value="kg">kg</option>
<option value="pz">pz</option>
<option value="cassa">cassa</option>

</select>

</div>


<div class="form-group">

<label>Tara cassa (kg)</label>

<input type="number" step="0.001" name="tara">

</div>


<div class="form-group">

<label>Peso medio cassa (kg)</label>

<input type="number" step="0.001" name="avg_box_weight">

</div>


<div class="form-group">

<label>Costo €/kg</label>

<input type="number" step="0.01" name="cost_price">

</div>


<div class="form-group">

<label>Prezzo €/kg</label>

<input type="number" step="0.01" name="price">

</div>


<div class="form-group">

<label>Aliquota IVA</label>

<select name="vat_rate">

<option value="4">IVA 4%</option>
<option value="5">IVA 5%</option>
<option value="10">IVA 10%</option>
<option value="22">IVA 22%</option>

</select>

</div>


<br>

<button class="btn btn-primary">

Salva Prodotto

</button>

<a href="/products" class="btn btn-secondary">

Annulla

</a>

</form>

</div>

@endsection