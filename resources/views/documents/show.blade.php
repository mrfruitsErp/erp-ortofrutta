@extends('layouts.app')

@section('page-title', 'Documento ' . $document->number)

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="page-header">
<div>
<div class="page-title" style="font-family:'DM Mono',monospace">
{{ $document->number }}
</div>

<div class="page-sub">
{{ \Carbon\Carbon::parse($document->date)->format('d/m/Y') }}
 · 
{{ $document->client->company_name ?? '—' }}
</div>
</div>

<div style="display:flex;gap:10px">
<a href="{{ url('/documents') }}" class="btn btn-secondary">← Torna ai documenti</a>
<a href="{{ url('/documents/'.$document->id.'/edit') }}" class="btn btn-secondary">✏️ Modifica</a>
<a href="{{ url('/documents/'.$document->id.'/pdf') }}" class="btn btn-secondary" target="_blank">🖨 Stampa PDF</a>
</div>
</div>



<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">

<div class="card" style="padding:16px 20px">
<div style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted)">Cliente</div>
<div style="font-size:15px;font-weight:700;color:var(--dark)">
{{ $document->client->company_name ?? '—' }}
</div>
</div>

<div class="card" style="padding:16px 20px">
<div style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted)">Tipo Documento</div>
<div style="font-size:15px;font-weight:700;color:var(--dark)">
{{ $document->type }}
</div>
</div>

<div class="card" style="padding:16px 20px">
<div style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted)">Data</div>
<div style="font-size:15px;font-weight:700;color:var(--dark)">
{{ \Carbon\Carbon::parse($document->date)->format('d/m/Y') }}
</div>
</div>

<div class="card" style="padding:16px 20px;background:var(--dark)">
<div style="font-size:11px;color:var(--muted)">Totale Documento</div>
<div id="docTotalDisplay" style="font-size:22px;font-weight:700;color:#fff;font-family:'DM Mono',monospace">
€ {{ number_format($document->total,2,',','.') }}
</div>
</div>

</div>



{{-- PAGAMENTI DOCUMENTO --}}

@php
$pagato = \App\Models\Payment::where('document_id',$document->id)->sum('amount');
$residuo = $document->total - $pagato;
@endphp

<div class="card" style="padding:20px;margin-bottom:20px">

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">

<div>
<div style="font-size:11px;color:var(--muted)">Pagato</div>
<div style="font-weight:700;font-size:18px">
€ {{ number_format($pagato,2,',','.') }}
</div>
</div>

<div>
<div style="font-size:11px;color:var(--muted)">Residuo</div>
<div style="font-weight:700;font-size:18px;color:#c0392b">
€ {{ number_format($residuo,2,',','.') }}
</div>
</div>

<div>

<form method="POST" action="{{ url('/payments') }}">

@csrf

<input type="hidden" name="document_id" value="{{ $document->id }}">
<input type="hidden" name="client_id" value="{{ $document->client_id }}">

<div style="display:flex;gap:10px">

<input type="number"
step="0.01"
name="amount"
placeholder="Importo"
required
style="padding:6px 10px;width:120px">

<input type="date"
name="payment_date"
value="{{ date('Y-m-d') }}"
style="padding:6px 10px">

<button class="btn btn-primary">
Registra Pagamento
</button>

</div>

</form>

</div>

</div>

</div>



{{-- RIGHE DOCUMENTO --}}

<div class="card" style="padding:0;overflow:hidden">

<div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between">
<div style="font-weight:700">Righe Documento</div>
</div>

<table>

<thead>
<tr>
<th>Prodotto</th>
<th style="text-align:right">Casse</th>
<th style="text-align:right">KG Stimati</th>
<th style="text-align:right">KG Reali</th>
<th style="text-align:right">€/kg</th>
<th style="text-align:right">Totale</th>
<th style="text-align:right">Margine</th>
<th style="width:90px"></th>
</tr>
</thead>

<tbody>

@foreach($rows as $row)

@php
$costo = $row->product->cost_price ?? 0;
$kg = $row->kg_real ? $row->kg_real : $row->kg_estimated;
$margine = ($row->price_per_kg - $costo) * $kg;
@endphp

<tr id="row-{{ $row->id }}"
@if($margine < 0)
style="background:#ffecec"
@endif
>

<td style="font-weight:600">
{{ $row->product->name ?? '—' }}
</td>

<td style="text-align:right;font-family:'DM Mono',monospace">
{{ $row->boxes }}
</td>

<td style="text-align:right;font-family:'DM Mono',monospace;color:var(--muted)">
{{ number_format($row->kg_estimated,2,',','.') }}
</td>

<td style="text-align:right">

<input
type="number"
step="0.01"
min="0"
class="kg-real-input"
data-row-id="{{ $row->id }}"
value="{{ $row->kg_real ?? '' }}"
style="width:100px;text-align:right;padding:6px 10px;font-family:'DM Mono',monospace"
>

</td>

<td style="text-align:right;font-family:'DM Mono',monospace">
€ {{ number_format($row->price_per_kg,2,',','.') }}
</td>

<td style="text-align:right;font-weight:700;font-family:'DM Mono',monospace" id="total-{{ $row->id }}">
€ {{ number_format($row->total,2,',','.') }}
</td>

<td style="text-align:right;font-family:'DM Mono',monospace">

@if($margine < 0)

<span style="color:#c0392b;font-weight:700">
⚠ € {{ number_format($margine,2,',','.') }}
</span>

@else

<span style="color:#27ae60;font-weight:700">
€ {{ number_format($margine,2,',','.') }}
</span>

@endif

</td>

<td style="text-align:center">

<button
onclick="saveWeight({{ $row->id }})"
id="btn-{{ $row->id }}"
class="btn btn-primary"
style="padding:5px 12px;font-size:12px">

💾 Salva

</button>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>



<script>

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function saveWeight(rowId){

const input = document.querySelector(`.kg-real-input[data-row-id="${rowId}"]`);

const val = input.value;

fetch('/save-real-weight',{

method:'POST',

headers:{
'X-CSRF-TOKEN':csrfToken
},

body:new URLSearchParams({
document_row_id:rowId,
real_weight:val
})

})

.then(r=>r.json())

.then(data=>{

if(data.success){

document.getElementById(`total-${rowId}`).textContent='€ '+parseFloat(data.new_total_row).toLocaleString('it-IT',{minimumFractionDigits:2});

document.getElementById('docTotalDisplay').textContent='€ '+parseFloat(data.new_total_document).toLocaleString('it-IT',{minimumFractionDigits:2});

}

})

}

</script>

@endsection