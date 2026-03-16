@extends('layouts.app')

@section('content')

<div class="container">

<h2>Dashboard Giornaliera</h2>

<hr>

<div style="display:flex;gap:40px">

<div style="background:#eef;padding:20px;width:200px">
<h4>Fatturato Oggi</h4>
<h2>{{ number_format($revenue,2) }} €</h2>
</div>

<div style="background:#efe;padding:20px;width:200px">
<h4>Costo Merce</h4>
<h2>{{ number_format($cost,2) }} €</h2>
</div>

<div style="background:#fee;padding:20px;width:200px">
<h4>Margine</h4>
<h2>{{ number_format($margin,2) }} €</h2>
</div>

<div style="background:#ffe;padding:20px;width:200px">
<h4>Margine %</h4>
<h2>{{ number_format($margin_percent,2) }} %</h2>
</div>

<div style="background:#eef;padding:20px;width:200px">
<h4>Documenti Oggi</h4>
<h2>{{ $docs_count }}</h2>
</div>

</div>

<hr>

<h3>Top Clienti (per margine)</h3>

<table border="1" cellpadding="5">

<tr>
<th>Cliente</th>
<th>Margine €</th>
</tr>

@foreach($top_clients as $c)

<tr>
<td>{{ $c->client }}</td>
<td>{{ number_format($c->margin,2) }}</td>
</tr>

@endforeach

</table>

<hr>

<h3>Top Prodotti (per margine)</h3>

<table border="1" cellpadding="5">

<tr>
<th>Prodotto</th>
<th>Margine €</th>
</tr>

@foreach($top_products as $p)

<tr>
<td>{{ $p->product }}</td>
<td>{{ number_format($p->margin,2) }}</td>
</tr>

@endforeach

</table>

</div>

@endsection