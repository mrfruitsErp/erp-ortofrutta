@extends('layouts.app')

@section('content')

<h2>Nuovo DDT</h2>

<form method="POST" action="/documents">
@csrf

<div style="margin-bottom:20px">

<label>Cliente</label>

<select name="client_id" required>

<option value="">-- seleziona cliente --</option>

@foreach($clients as $client)

<option value="{{ $client->id }}">
{{ $client->company_name }}
</option>

@endforeach

</select>

<label style="margin-left:20px">Data</label>

<input type="date" name="date" value="{{ date('Y-m-d') }}">

</div>

<table border="1" width="100%" cellpadding="6" id="docTable">

<tr>
<th>COLLI</th>
<th>DESCRIZIONE</th>
<th>ORIG</th>
<th>UM</th>
<th>TARA</th>
<th>KG</th>
<th>€/KG</th>
<th>IMPORTO</th>
<th>MARGINE</th>
</tr>

<tbody id="rows">

<tr>

<td>
<input type="number" name="qty[]" class="boxes" value="0">
</td>

<td>

<select name="products[]" class="product">

<option value="">-- seleziona --</option>

@foreach($products as $product)

<option
value="{{ $product->id }}"
data-weight="{{ $product->avg_box_weight }}"
data-tara="{{ $product->tara }}"
data-origin="{{ $product->origin }}"
data-unit="{{ $product->unit }}"
data-cost="{{ $product->cost_price }}"
>

{{ $product->name }}

</option>

@endforeach

</select>

</td>

<td class="origin"></td>

<td class="unit"></td>

<td class="tara"></td>

<td>

<input type="text" class="kg" readonly value="0">

</td>

<td>

<input type="number" step="0.01" name="price[]" class="price" value="0">

</td>

<td class="total">0</td>

<td class="margin"></td>

</tr>

</tbody>

</table>

<br>

<button type="button" onclick="addRow()">

+ Aggiungi Riga

</button>

<br><br>

<h3>

Totale Documento: € <span id="docTotal">0.00</span>

</h3>

<br>

<button type="submit">

Salva DDT

</button>

</form>

<script>

function updateRow(row){

let product = row.querySelector(".product")

let boxes = parseFloat(row.querySelector(".boxes").value) || 0
let price = parseFloat(row.querySelector(".price").value) || 0

let weight = parseFloat(product.selectedOptions[0]?.dataset.weight || 0)
let tara = parseFloat(product.selectedOptions[0]?.dataset.tara || 0)
let cost = parseFloat(product.selectedOptions[0]?.dataset.cost || 0)

let origin = product.selectedOptions[0]?.dataset.origin || ""
let unit = product.selectedOptions[0]?.dataset.unit || ""

row.querySelector(".origin").innerText = origin
row.querySelector(".unit").innerText = unit
row.querySelector(".tara").innerText = tara

/* CALCOLO KG */

let gross = boxes * weight
let taraTotal = boxes * tara
let kg = gross - taraTotal

row.querySelector(".kg").value = kg.toFixed(3)

/* IMPORTO */

let total = kg * price

row.querySelector(".total").innerText = total.toFixed(2)

/* MARGINE LIVE */

let marginCell = row.querySelector(".margin")

if(price > 0){

let margin = ((price - cost) / price) * 100

if(margin < 0){

marginCell.innerHTML = "⚠ SOTTO COSTO"

marginCell.style.color = "red"

}else{

marginCell.innerHTML = margin.toFixed(1) + "%"

marginCell.style.color = "green"

}

}else{

marginCell.innerHTML = ""

}

updateTotal()

}



function updateTotal(){

let total = 0

document.querySelectorAll("#rows tr").forEach(row => {

let kg = parseFloat(row.querySelector(".kg").value) || 0
let price = parseFloat(row.querySelector(".price").value) || 0

total += kg * price

})

document.getElementById("docTotal").innerText = total.toFixed(2)

}



function attachEvents(row){

row.querySelector(".boxes").addEventListener("input", () => updateRow(row))
row.querySelector(".product").addEventListener("change", () => updateRow(row))
row.querySelector(".price").addEventListener("input", () => updateRow(row))

}



document.querySelectorAll("#rows tr").forEach(row => {

attachEvents(row)

})



function addRow(){

let table = document.getElementById("rows")

let newRow = table.rows[0].cloneNode(true)

newRow.querySelectorAll("input").forEach(i => i.value = 0)
newRow.querySelector(".kg").value = 0
newRow.querySelector(".origin").innerText = ""
newRow.querySelector(".unit").innerText = ""
newRow.querySelector(".tara").innerText = ""
newRow.querySelector(".total").innerText = 0
newRow.querySelector(".margin").innerText = ""

table.appendChild(newRow)

attachEvents(newRow)

}

</script>

@endsection