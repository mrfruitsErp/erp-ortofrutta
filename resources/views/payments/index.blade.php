@extends('layouts.app')

@section('content')

<div class="card" style="padding:20px">

<h2 style="margin-bottom:20px">Pagamenti</h2>

<table>

<thead>

<tr>
<th>ID</th>
<th>Documento</th>
<th>Importo</th>
<th>Metodo</th>
<th>Data</th>
</tr>

</thead>

<tbody>

@foreach($payments as $payment)

<tr>

<td>{{ $payment->id }}</td>

<td>{{ $payment->document_id }}</td>

<td>€ {{ number_format($payment->amount,2,',','.') }}</td>

<td>{{ $payment->method }}</td>

<td>{{ $payment->payment_date }}</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@endsection