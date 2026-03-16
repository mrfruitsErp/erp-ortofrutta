@extends('layouts.app')

@section('page-title','Ordini')

@section('content')

<div class="page-header">

<div>

<div class="page-title">Ordini</div>

<div class="page-sub">Ordini clienti</div>

</div>

<a href="/orders/create" class="btn btn-primary">
＋ Nuovo Ordine
</a>

</div>

<div class="card">

<table>

<thead>

<tr>

<th>#</th>

<th>Numero</th>

<th>Cliente</th>

<th>Data</th>

<th style="text-align:right">Totale</th>

</tr>

</thead>

<tbody>

@foreach($orders as $order)

<tr>

<td>{{ $order->id }}</td>

<td>
<a href="/orders/{{ $order->id }}" style="font-weight:600;color:var(--green);text-decoration:none">
{{ $order->number }}
</a>
</td>

<td>{{ $order->client->company_name ?? '' }}</td>

<td>{{ $order->date }}</td>

<td style="text-align:right">
€ {{ number_format($order->total,2,',','.') }}
</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@endsection
