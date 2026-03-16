@extends('layouts.app')

@section('page-title','Nuovo Ordine')

@section('content')

<div class="page-header">
<div>
<div class="page-title">🧾 Nuovo Ordine</div>
<div class="page-sub">Crea un ordine cliente</div>
</div>
<a href="/orders" class="btn btn-secondary">← Torna agli ordini</a>
</div>

<form method="POST" action="/orders">
@csrf

<div class="card" style="margin-bottom:20px">

<div class="form-group">
<label>Cliente</label>
<select name="client_id" required>
<option value="">Seleziona cliente</option>

@foreach($clients as $client)

<option value="{{ $client->id }}">
{{ $client->company_name }}
</option>
@endforeach

</select>
</div>

<div class="form-group">
<label>Data ordine</label>
<input type="date" name="date" value="{{ date('Y-m-d') }}" required>
</div>

</div>

<div class="card">

<div style="font-weight:700;margin-bottom:15px">Prodotti ordine</div>

<table id="productsTable">

<thead>
<tr>
<th>Prodotto</th>
<th style="width:120px">Quantità</th>
<th style="width:120px">Prezzo</th>
<th style="width:120px">Totale</th>
<th style="width:80px"></th>
</tr>
</thead>

<tbody id="orderRows">

<tr>

<td>

<select name="product_id[]" required>

<option value="">Prodotto</option>

@foreach($products as $product)

<option value="{{ $product->id }}" data-price="{{ $product->price }}">
{{ $product->name }}
</option>
@endforeach

</select>

</td>

<td>
<input type="number" name="qty[]" step="0.01" value="1">
</td>

<td>
<input type="number" name="price[]" step="0.01">
</td>

<td>
<input type="number" name="total[]" step="0.01" readonly>
</td>

<td>
<button type="button" onclick="removeRow(this)" class="btn btn-secondary">✕</button>
</td>

</tr>

</tbody>

</table>

<button type="button" onclick="addRow()" class="btn btn-secondary" style="margin-top:10px">
+ Aggiungi prodotto
</button>

</div>

<div class="card" style="margin-top:20px;display:flex;justify-content:space-between;align-items:center">

<div style="font-size:18px;font-weight:700">
Totale ordine: € <span id="orderTotal">0.00</span>
</div>

<button type="submit" class="btn btn-primary">
Salva Ordine
</button>

</div>

</form>

<script>

function addRow(){

const table = document.getElementById('orderRows')

const row = table.rows[0].cloneNode(true)

row.querySelectorAll('input').forEach(i=>i.value='')

table.appendChild(row)

}

function removeRow(btn){

const row = btn.closest('tr')

const table = document.getElementById('orderRows')

if(table.rows.length>1){
row.remove()
calculateTotal()
}

}

document.addEventListener('input',function(e){

if(e.target.name=="qty[]" || e.target.name=="price[]"){

const row = e.target.closest('tr')

const qty = row.querySelector('[name="qty[]"]').value || 0
const price = row.querySelector('[name="price[]"]').value || 0

const total = qty * price

row.querySelector('[name="total[]"]').value = total.toFixed(2)

calculateTotal()

}

})

function calculateTotal(){

let total = 0

document.querySelectorAll('[name="total[]"]').forEach(el=>{
total += parseFloat(el.value) || 0
})

document.getElementById('orderTotal').innerText = total.toFixed(2)

}

</script>

@endsection
