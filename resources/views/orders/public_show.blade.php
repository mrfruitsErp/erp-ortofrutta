<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Ordine {{ $order->number }}</title>
<style>
* { box-sizing:border-box; margin:0; padding:0 }

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: #f0f4f1;
    color: #1b2d27;
    padding-bottom: 40px;
}

.header {
    background: #2d6a4f;
    color: white;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-title { font-size: 15px; font-weight: 700; }
.header-sub   { font-size: 12px; opacity: 0.8; margin-top: 2px; }

.back-btn {
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 13px;
    text-decoration: none;
    font-family: inherit;
}

.container { max-width: 700px; margin: 0 auto; padding: 16px; }

.card {
    background: white;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
}

.card-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    color: #7a9e8e;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
}

.meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.meta-item label {
    font-size: 11px;
    color: #999;
    display: block;
    margin-bottom: 3px;
    text-transform: uppercase;
}

.meta-item span {
    font-size: 14px;
    font-weight: 600;
}

.status-badge {
    display: inline-block;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
}

.product-row {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 10px;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f0f4f1;
}

.product-row:last-child { border-bottom: none; }

.product-name  { font-weight: 600; font-size: 14px; }
.product-detail { font-size: 12px; color: #7a9e8e; margin-top: 2px; }

.product-qty   { text-align: right; font-size: 13px; color: #555; white-space: nowrap; }
.product-total { text-align: right; font-weight: 700; font-size: 14px; font-family: monospace; white-space: nowrap; }

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    font-size: 16px;
    font-weight: 700;
    border-top: 2px solid #2d6a4f;
    margin-top: 8px;
    color: #2d6a4f;
}

@media(max-width:480px){
    .meta-grid { grid-template-columns: 1fr; }
}
</style>
</head>

<body>

<div class="header">
    <div>
        <div class="header-title">{{ $order->number }}</div>
        <div class="header-sub">{{ $client->company_name }}</div>
    </div>
    <a href="/order/{{ $client->order_token }}" class="back-btn">← Torna agli ordini</a>
</div>

<div class="container">

    {{-- INFO ORDINE --}}
    <div class="card">
        <div class="card-title">📋 Dettaglio ordine</div>
        <div class="meta-grid">
            <div class="meta-item">
                <label>Numero</label>
                <span>{{ $order->number }}</span>
            </div>
            <div class="meta-item">
                <label>Data</label>
                <span>{{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}</span>
            </div>
            @if($order->delivery_date)
            <div class="meta-item">
                <label>Data consegna</label>
                <span>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</span>
            </div>
            @endif
            @if($order->delivery_slot)
            <div class="meta-item">
                <label>Fascia oraria</label>
                <span>{{ $order->delivery_slot }}</span>
            </div>
            @endif
            <div class="meta-item">
                <label>Stato</label>
                @php
                    $stati = [
                        'draft'     => ['label' => 'In attesa',    'bg' => '#fff3e0', 'color' => '#e65100'],
                        'web'       => ['label' => 'Ricevuto',     'bg' => '#ede9fe', 'color' => '#7c3aed'],
                        'confirmed' => ['label' => 'Confermato',   'bg' => '#e3f0ff', 'color' => '#1a56a0'],
                        'invoiced'  => ['label' => 'Evaso',        'bg' => '#d4edda', 'color' => '#2d6a4f'],
                    ];
                    $s = $stati[$order->status] ?? ['label' => $order->status, 'bg' => '#f3f4f6', 'color' => '#555'];
                @endphp
                <span class="status-badge" style="background:{{ $s['bg'] }};color:{{ $s['color'] }}">
                    {{ $s['label'] }}
                </span>
            </div>
        </div>
    </div>

    {{-- PRODOTTI --}}
    <div class="card">
        <div class="card-title">🛒 Prodotti ordinati</div>

        @foreach($order->items as $item)
        @php
            $isUnit = ($item->product->sale_type ?? 'kg') === 'unit';
        @endphp
        <div class="product-row">
            <div>
                <div class="product-name">{{ $item->product->name ?? '—' }}</div>
                <div class="product-detail">
                    {{ $item->origin ?? $item->product->origin ?? '' }}
                    @if(!$isUnit)
                        · {{ number_format($item->kg_net ?? 0, 2, ',', '.') }} kg netti
                    @endif
                </div>
            </div>
            <div class="product-qty">
                @if($isUnit)
                    {{ number_format($item->qty ?? 0, 0, ',', '.') }} pz
                @else
                    {{ $item->colli }} colli
                @endif
            </div>
            <div class="product-total">
                € {{ number_format($item->total, 2, ',', '.') }}
            </div>
        </div>
        @endforeach

        <div class="total-row">
            <span>Totale ordine</span>
            <span>€ {{ number_format($order->total, 2, ',', '.') }}</span>
        </div>
    </div>

</div>

</body>
</html>