@extends('layouts.app')

@section('page-title', 'Listini Prezzi')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">📋 Listini Prezzi</div>
        <div class="page-sub">Gestisci i listini per tipo cliente</div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:20px">

    @foreach($priceLists as $pl)
    <div class="card" style="position:relative">

        {{-- Badge attivo/disattivato --}}
        <div style="position:absolute;top:16px;right:16px">
            @if($pl->attivo)
                <span style="background:var(--green-xl, #f0faf4);color:var(--green, #7a9e8e);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">Attivo</span>
            @else
                <span style="background:#f3f4f6;color:#6b7280;padding:3px 10px;border-radius:20px;font-size:11px">Disattivato</span>
            @endif
        </div>

        <div style="font-size:20px;font-weight:700;margin-bottom:4px">{{ $pl->nome }}</div>
        <div style="font-size:12px;color:#999;margin-bottom:16px">{{ $pl->descrizione }}</div>

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:16px">
            <div style="text-align:center;padding:10px;background:var(--bg, #f8f9fa);border-radius:8px">
                <div style="font-size:20px;font-weight:700">{{ $pl->clients_count }}</div>
                <div style="font-size:10px;color:#999;text-transform:uppercase">Clienti</div>
            </div>
            <div style="text-align:center;padding:10px;background:var(--bg, #f8f9fa);border-radius:8px">
                <div style="font-size:20px;font-weight:700">{{ $pl->items_count }}</div>
                <div style="font-size:10px;color:#999;text-transform:uppercase">Prezzi custom</div>
            </div>
            <div style="text-align:center;padding:10px;background:var(--bg, #f8f9fa);border-radius:8px">
                <div style="font-size:20px;font-weight:700">{{ $pl->sconto_default_pct }}%</div>
                <div style="font-size:10px;color:#999;text-transform:uppercase">Sconto base</div>
            </div>
        </div>

        {{-- Regole --}}
        <div style="font-size:12px;color:#666;margin-bottom:16px;line-height:1.8">
            @if($pl->puo_ordinare_kg)
                <span style="color:var(--green, #7a9e8e)">✅ Può ordinare a kg</span><br>
            @else
                <span style="color:#999">❌ Solo casse intere</span><br>
            @endif

            @if($pl->ordine_min_importo > 0)
                <span>💶 Ordine minimo: € {{ number_format($pl->ordine_min_importo, 2, ',', '.') }}</span><br>
            @endif

            @if($pl->defaultPaymentMethod)
                <span>💳 Pagamento: {{ $pl->defaultPaymentMethod->nome }}</span>
            @endif
        </div>

        <a href="{{ route('price-lists.edit', $pl->id) }}" class="btn btn-primary" style="width:100%;justify-content:center">
            ✏️ Gestisci listino
        </a>
    </div>
    @endforeach

</div>

@endsection
