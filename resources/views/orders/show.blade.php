@extends('layouts.app')

@section('page-title','Dettaglio Ordine')

@section('content')

<div class="page-header">

    <div>
        <div class="page-title">Ordine {{ $order->number }}</div>
        <div class="page-sub">Cliente: <strong>{{ $order->client->company_name ?? '' }}</strong></div>
    </div>

    <div style="display:flex;gap:10px;align-items:center">

        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            ← Torna agli ordini
        </a>

        @if($order->status == 'draft')

            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning">
                ✏️ Modifica
            </a>

            <a href="{{ route('orders.confirm', $order->id) }}"
               class="btn btn-primary"
               onclick="return confirm('Confermare l\'ordine {{ $order->number }}?')">
                ✅ Conferma ordine
            </a>

        @endif

        @if($order->status == 'confirmed')

            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning">
                ✏️ Modifica
            </a>

            <a href="{{ route('orders.generateDocument', $order->id) }}"
               class="btn btn-success"
               onclick="return confirm('Generare DDT dall\'ordine {{ $order->number }}?')">
                📄 Genera DDT
            </a>

        @endif

        @if($order->status == 'invoiced')

            <span class="btn btn-secondary" style="opacity:0.6;cursor:default">
                ✔ DDT generato
            </span>

        @endif

    </div>

</div>


{{-- ALERT SUCCESS --}}
@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px">
        {{ session('success') }}
    </div>
@endif


{{-- INFO ORDINE --}}
<div class="card" style="margin-bottom:20px">
    <div style="display:flex;gap:40px;flex-wrap:wrap">

        <div>
            <strong>Numero ordine</strong><br>
            {{ $order->number }}
        </div>

        <div>
            <strong>Data</strong><br>
            {{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}
        </div>

        @if($order->delivery_date)
        <div>
            <strong>Data consegna</strong><br>
            {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
        </div>
        @endif

        @if($order->delivery_slot)
        <div>
            <strong>Fascia oraria</strong><br>
            {{ $order->delivery_slot }}
        </div>
        @endif

        <div>
            <strong>Stato</strong><br>
            @if($order->status == 'draft')
                <span style="color:#f59e0b;font-weight:600">● Bozza</span>
            @elseif($order->status == 'confirmed')
                <span style="color:#3b82f6;font-weight:600">● Confermato</span>
            @elseif($order->status == 'invoiced')
                <span style="color:#10b981;font-weight:600">● Evaso</span>
            @else
                {{ $order->status }}
            @endif
        </div>

    </div>
</div>


{{-- RIGHE ORDINE --}}
<div class="card">

    <table>
        <thead>
            <tr>
                <th>Prodotto</th>
                <th>Origine</th>
                <th style="width:80px;text-align:center">Colli</th>
                <th style="width:100px;text-align:right">Kg stimati</th>
                <th style="width:100px;text-align:right">Kg reali</th>
                <th style="width:100px;text-align:right">Kg netti</th>
                <th style="width:100px;text-align:right">€/kg</th>
                <th style="width:110px;text-align:right">Totale</th>
            </tr>
        </thead>
        <tbody>
        @forelse($order->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '—' }}</td>
                <td>{{ $item->origin ?? '—' }}</td>
                <td style="text-align:center">{{ $item->qty }}</td>
                <td style="text-align:right">{{ number_format($item->kg_estimated,2,',','.') }}</td>
                <td style="text-align:right">{{ $item->kg_real ? number_format($item->kg_real,2,',','.') : '—' }}</td>
                <td style="text-align:right">{{ number_format($item->kg_net,2,',','.') }}</td>
                <td style="text-align:right">€ {{ number_format($item->price,2,',','.') }}</td>
                <td style="text-align:right">€ {{ number_format($item->total,2,',','.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;color:#999;padding:20px">
                    Nessun prodotto in questo ordine
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

</div>


{{-- TOTALE --}}
<div class="card" style="margin-top:20px;display:flex;justify-content:flex-end;align-items:center">
    <div style="font-size:20px;font-weight:700">
        Totale ordine &nbsp; € {{ number_format($order->total,2,',','.') }}
    </div>
</div>

@endsection