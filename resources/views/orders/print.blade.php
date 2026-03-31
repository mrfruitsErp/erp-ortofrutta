<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Stampa Ordini — Mr. Fruits ERP</title>

<style>
body {
    font-family: Arial;
    background: #e5e7eb;
}

/* CONTENITORE */
.print-preview {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 40px;
    padding: 30px 0;
}

/* FOGLIO */
.page {
    width: 210mm;
    min-height: 297mm;
    background: #fff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    padding: 15mm;
    transform: scale(0.9);
    transform-origin: top center;
}

/* HEADER CONTROLLI */
.controls {
    position: sticky;
    top: 0;
    background: #fff;
    padding: 15px;
    z-index: 10;
    border-bottom: 1px solid #ddd;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* TABELLA */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
}

th, td {
    border: 1px solid #ddd;
    padding: 5px;
}

th {
    background: #f0f0f0;
}

/* MODALITÀ */
.mode-compact .col-kg,
.mode-compact .col-price {
    display: none;
}

.mode-picking .col-price {
    display: none;
}

.mode-summary tbody {
    display: none;
}

/* STAMPA */
@media print {
    body {
        background: #fff;
    }

    .controls {
        display: none;
    }

    .page {
        box-shadow: none;
        transform: scale(1);
    }
}
</style>
</head>

<body>

<div class="controls">

    <button onclick="window.print()">🖨️ Stampa</button>

    <select onchange="setMode(this.value)">
        <option value="">Completa</option>
        <option value="compact">Compatta</option>
        <option value="picking">Picking</option>
        <option value="summary">Riepilogo</option>
    </select>

    Zoom:
    <input type="range" min="70" max="100" value="90" oninput="zoomPage(this.value)">

</div>

<div class="print-preview" id="preview">

@foreach($orders as $order)

<div class="page">

    <h2>Ordine {{ $order->number }}</h2>
    <p>Data: {{ \Carbon\Carbon::parse($order->date)->format('d/m/Y') }}</p>
    <p>Cliente: {{ $order->client->company_name ?? '-' }}</p>

    <table>
        <thead>
            <tr>
                <th>Prodotto</th>
                <th class="col-kg">Quantità</th>
                <th class="col-price">Prezzo</th>
                <th>Totale</th>
            </tr>
        </thead>

        <tbody>
        @foreach($order->items as $item)

        @php
            $isUnit = ($item->product->sale_type ?? 'kg') === 'unit';
        @endphp

        <tr>
            <td>{{ $item->product->name }}</td>

            <td class="col-kg">
                @if(!$isUnit)
                    {{ number_format($item->kg_net ?? 0,2,',','.') }} kg
                @else
                    {{ number_format($item->qty ?? 0,0,',','.') }} pz
                @endif
            </td>

            <td class="col-price">
                € {{ number_format($item->price_kg ?? $item->price ?? 0,2,',','.') }}
            </td>

            <td>
                € {{ number_format($item->total,2,',','.') }}
            </td>
        </tr>

        @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="3">Totale</td>
                <td>€ {{ number_format($order->total,2,',','.') }}</td>
            </tr>
        </tfoot>
    </table>

</div>

@endforeach

</div>

<script>
function setMode(mode) {
    const preview = document.getElementById('preview');
    preview.className = 'print-preview mode-' + mode;
}

function zoomPage(val) {
    document.querySelectorAll('.page').forEach(p => {
        p.style.transform = 'scale(' + (val/100) + ')';
    });
}
</script>

</body>
</html>