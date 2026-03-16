<!DOCTYPE html>

<html>
<head>

<title>Ordine prodotti</title>

<meta name="viewport" content="width=device-width,initial-scale=1">

<style>

body{
font-family:Arial;
background:#f5f5f5;
padding:20px;
}

table{
width:100%;
background:white;
border-collapse:collapse;
margin-bottom:30px;
}

th,td{
padding:10px;
border-bottom:1px solid #ddd;
text-align:left;
}

input,select{
width:120px;
padding:6px;
}

button{
padding:12px 20px;
background:#2d6a4f;
color:white;
border:none;
font-size:16px;
}

.category{
margin-top:30px;
font-size:20px;
font-weight:bold;
}

#total{
font-size:22px;
margin-top:20px;
}

.delivery-box{
background:white;
padding:15px;
margin-bottom:20px;
border:1px solid #ddd;
}

</style>

<script>

function calculateTotal(){

let total=0;

document.querySelectorAll(".qty").forEach(function(input){

let price=parseFloat(input.dataset.price);
let qty=parseFloat(input.value);

if(!isNaN(qty)){
total+=price*qty;
}

});

document.getElementById("total").innerText="Totale ordine € "+total.toFixed(2);

}

</script>

</head>

<body>

<h2>Ordine {{ $client->company_name }}</h2>

@if(session('success'))

<p style="color:green">{{ session('success') }}</p>
@endif

<form method="POST">

@csrf

<div class="delivery-box">

<h3>Consegna</h3>

<label>Data consegna</label>

<input type="date"
name="delivery_date"
value="{{ date('Y-m-d',strtotime('+1 day')) }}">

<br><br>

<label>Fascia oraria</label>

<select name="delivery_slot">

<option value="">Seleziona orario</option>

@foreach($slots as $slot)

<option value="{{ $slot->start_time }}-{{ $slot->end_time }}">

{{ substr($slot->start_time,0,5) }} - {{ substr($slot->end_time,0,5) }}

</option>

@endforeach

</select>

</div>

@php
$categories=$products->groupBy('category');
@endphp

@foreach($categories as $category=>$items)

<div class="category">
{{ strtoupper($category) }}
</div>

<table>

<tr>
<th>Prodotto</th>
<th>Prezzo</th>
<th>Quantità</th>
</tr>

@foreach($items as $product)

<tr>

<td>{{ $product->name }}</td>

<td>€ {{ number_format($product->price,2) }}/{{ $product->unit }}</td>

<td>

<input
type="number"
step="0.001"
class="qty"
data-price="{{ $product->price }}"
name="qty[{{ $product->id }}]"
value="0"
onkeyup="calculateTotal()"
onchange="calculateTotal()"

>

</td>

</tr>

@endforeach

</table>

@endforeach

<div id="total">
Totale ordine € 0.00
</div>

<br>

<button type="submit">
Invia ordine
</button>

</form>

</body>
</html>
