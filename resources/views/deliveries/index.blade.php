@extends('layouts.app')

@section('page-title', 'Consegne Giorno')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">🚛 Consegne</div>
        <div class="page-sub">Gestione operativa giornaliera</div>
    </div>
</div>

<div class="card">

<form method="GET" style="margin-bottom:20px">
    <input type="date" name="date" value="{{ $date }}">
    <button class="btn">Filtra</button>
</form>

@foreach($slots as $slot)

    @php
        $orders = $grouped[$slot->id] ?? collect();

        $totale = $orders->sum('total');
        $numero_ordini = $orders->count();
    @endphp

    <div style="margin-bottom:40px">

        <h3 style="margin-bottom:5px">
            🕒 {{ $slot->nome }} ({{ $slot->orario_inizio }} - {{ $slot->orario_fine }})
        </h3>

        <div style="margin-bottom:10px; font-size:13px; color:gray">
            Ordini: {{ $numero_ordini }} / {{ $slot->max_orders }} |
            Totale: € {{ number_format($totale,2,',','.') }}
        </div>

        @if($orders->count())

        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Zona</th>
                    <th>Indirizzo</th>
                    <th>Totale €</th>
                    <th>Note</th>
                </tr>
            </thead>

            <tbody>
            @foreach($orders as $order)

                <tr>
                    <td><strong>{{ $order->client->company_name }}</strong></td>
                    <td>{{ $order->client->zona_consegna }}</td>
                    <td>{{ $order->client->address }}</td>
                    <td>€ {{ number_format($order->total,2,',','.') }}</td>
                    <td>{{ $order->notes }}</td>
                </tr>

            @endforeach
            </tbody>
        </table>

        @else
            <div style="color:gray">Nessuna consegna</div>
        @endif

    </div>

@endforeach

</div>

@endsection