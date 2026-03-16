@extends('layouts.app')

@section('page-title','Crediti Clienti')

@section('content')

<div class="card" style="padding:20px">

<h2 style="margin-bottom:20px">Crediti Clienti</h2>

<table>

<thead>

<tr>
<th>Cliente</th>
<th>Documento</th>
<th>Data</th>
<th>Importo</th>
<th>Giorni</th>
</tr>

</thead>

<tbody>

@foreach($rows as $r)

<tr>

<td>{{ $r['cliente'] }}</td>

<td>{{ $r['documento'] }}</td>

<td>{{ \Carbon\Carbon::parse($r['data'])->format('d/m/Y') }}</td>

<td>
€ {{ number_format($r['importo'],2,',','.') }}
</td>

<td>

@if($r['giorni'] > 7)

<span style="color:#c0392b;font-weight:700">

{{ $r['giorni'] }}

</span>

@else

{{ $r['giorni'] }}

@endif

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@endsection