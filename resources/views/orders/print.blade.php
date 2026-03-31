<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Stampa Ordini — Mr. Fruits ERP</title>
<style>
/* (stile invariato, non toccato) */
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 11px; color: #111; background: #fff; }
/* resto CSS identico... */
</style>
</head>
<body>

<div class="screen-header">
    <div>
        <h1>🖨️ Anteprima Stampa Ordini</h1>
        <div class="meta">{{ $orders->count() }} ordini · Generato il {{ date('d/m/Y H:i') }}</div>
    </div>
</div>

<div class="screen-controls">
    <button onclick="window.print()" class="btn-print">🖨️ Stampa adesso</button>
    <a href="{{ route('orders.index') }}" class="btn-back">← Torna agli ordini</a>
</div>

<div class="summary-bar">
    <strong>{{ $orders->count() }}</strong> ordini ·
    Totale complessivo: <strong>€ {{ number_format($orders->sum('total'), 2, ',', '.') }}</strong>
</div>

@forelse($orders as $order)

<div class="order-block">

    <div class="order-head">
        <div>
            <div class="company-name">Mr. Fruits</div>
            <div class="company-sub">Gestionale ERP</div>
        </div>
        <div class="order-ref">
            <div class="number">{{ $order->number ?? 'N° mancante' }}</div>
            <div class="date">{{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}</div>
        </div>
    </div>

    <hr class="divider">

    <div class="info-grid">
        <div>
            <div class="info-label">Cliente</div>
            <div class="info-value">{{ $order->client->company_name ?? '—' }}</div>
        </div>
    </div>

    {{-- TABELLA PRODOTTI --}}
    <table>
        <thead>
            <tr>
                <th>Prodotto</th>
                <th class="center">UM</th>
                <th class="center">Colli</th>
                <th class="right">Kg / Pz</th>
                <th class="right">Prezzo</th>
                <th class="right">Totale</th>
            </tr>
        </thead>

        <tbody>
        @foreach($order->items as $item)

        @php
            $isUnit = ($item->product->sale_type ?? 'kg') === 'unit';
        @endphp

        <tr>
            <td><strong>{{ $item->product->name ?? '—' }}</strong></td>

            <td class="center">
                {{ $isUnit ? 'PZ' : 'KG' }}
            </td>

            <td class="center">
                {{ $item->colli ?? '—' }}
            </td>

            <td class="right">
                @if(!$isUnit)
                    {{ number_format($item->kg_net ?? 0,2,',','.') }}
                @else
                    {{ number_format($item->qty ?? 0,0,',','.') }} pz
                @endif
            </td>

            <td class="right">
                € {{ number_format($item->price_kg ?? $item->price ?? 0,2,',','.') }}
            </td>

            <td class="right">
                <strong>€ {{ number_format($item->total,2,',','.') }}</strong>
            </td>
        </tr>

        @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="5" class="right">Totale ordine</td>
                <td class="right">
                    € {{ number_format($order->total,2,',','.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    @if($order->notes)
        <div class="order-notes">
            📝 Note: {{ $order->notes }}
        </div>
    @endif

</div>

@empty

<div style="padding:40px;text-align:center">
    Nessun ordine trovato
</div>

@endforelse

</body>
</html>