@extends('layouts.app')

@section('page-title','Dettaglio Ordine')

@section('content')

<div class="page-header">

<div>
<div class="page-title">Ordine {{ $order->number }}</div>
<div class="page-sub">Cliente: {{ $order->client->company_name }}</div>
</div>

<a href="/orders" class="btn btn-secondary">← Torna agli ordini</a>

</div>

<div class="card" style="margin-bottom:20px">

<div style="display:flex;gap:40px">

<div>
<strong>Numero ordine</strong><br>
{{ $order->number }}
</div>

<div>
<strong>Data</strong><br>
{{ $order->date }}
</div>

<div>
<strong>Stato</strong><br>
{{ $order->status }}
</div>

</div>

</div>

<div class="card">

<table>

<thead>

<tr>
<th>Prodotto</th>
<th style="width:120px">Quantità</th>
<th style="width:120px">Prezzo</th>
<th style="width:120px">Totale</th>
</tr>

</thead>

<tbody>

@foreach($order->items as $item)

<tr>

<td>{{ $item->product->name }}</td>

<td>{{ $item->qty }}</td>

<td>€ {{ number_format($item->price,2,',','.') }}</td>

<td>€ {{ number_format($item->total,2,',','.') }}</td>

</tr>

@endforeach

</tbody>

</table>

</div>

<div class="card" style="margin-top:20px;display:flex;justify-content:space-between;align-items:center">

<div style="font-size:20px;font-weight:700">
Totale ordine  
€ {{ number_format($order->total,2,',','.') }}
</div>

</div>

@endsection
