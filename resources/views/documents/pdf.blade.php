<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="utf-8">
<title>{{ $document->number }}</title>
<style>

* { box-sizing:border-box; margin:0; padding:0 }

body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 11px;
    color: #1b2d27;
    background: #fff;
}

.page {
    padding: 32px 36px;
    max-width: 900px;
    margin: 0 auto;
}

/* ── HEADER ── */
.header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #2d6a4f;
}

.company-name  { font-size: 16px; font-weight: 700; color: #2d6a4f; margin-bottom: 4px; }
.company-sub   { font-size: 11px; color: #555; line-height: 1.6; }
.doc-block     { text-align: right; }
.doc-type      { font-size: 20px; font-weight: 700; color: #2d6a4f; }
.doc-number    { font-size: 13px; font-family: monospace; margin-top: 4px; }
.doc-date      { font-size: 10px; color: #7a9e8e; margin-top: 2px; }

/* ── PARTI ── */
.parti { display: flex; gap: 20px; margin-bottom: 20px; }

.parte-box {
    flex: 1;
    border: 1px solid #e2ebe5;
    border-radius: 6px;
    padding: 12px 14px;
    background: #f8fbf9;
}

.parte-label {
    font-size: 8px; font-weight: 700; text-transform: uppercase;
    color: #7a9e8e; margin-bottom: 5px; letter-spacing: 0.5px;
}

.parte-name  { font-size: 13px; font-weight: 700; margin-bottom: 2px; }
.parte-info  { font-size: 10px; color: #555; line-height: 1.5; }

/* ── META DOCUMENTO ── */
.doc-meta {
    display: flex;
    border: 1px solid #e2ebe5;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 20px;
}

.doc-meta-item {
    flex: 1;
    padding: 8px 14px;
    border-right: 1px solid #e2ebe5;
    font-size: 10px;
}

.doc-meta-item:last-child { border-right: none; }
.doc-meta-label { color: #7a9e8e; font-size: 8px; text-transform: uppercase; margin-bottom: 3px; }
.doc-meta-value { font-weight: 600; }

/* ── TABELLA ── */
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }

thead tr { background: #2d6a4f; color: #fff; }

thead th {
    padding: 8px 8px;
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

tbody td {
    padding: 7px 8px;
    border-bottom: 1px solid #eef2ef;
    font-size: 10.5px;
    vertical-align: middle;
}

tbody tr:nth-child(even) { background: #f8fbf8; }

.c { text-align: center }
.r { text-align: right; font-family: monospace }
.l { text-align: left }
.b { font-weight: 700 }

/* ── TOTALI ── */
.totali-wrap { display: flex; justify-content: flex-end; margin-bottom: 32px; }

.totali-box {
    width: 300px;
    border: 1.5px solid #2d6a4f;
    border-radius: 6px;
    overflow: hidden;
}

.tot-row {
    display: flex;
    justify-content: space-between;
    padding: 7px 14px;
    font-size: 11px;
    border-bottom: 1px solid #e2ebe5;
}

.tot-row.final {
    background: #2d6a4f;
    color: #fff;
    font-weight: 700;
    font-size: 13px;
    border-bottom: none;
}

.tot-row span:last-child { font-family: monospace; }

/* ── FIRME ── */
.firme { display: flex; gap: 24px; margin-top: 40px; }

.firma-box {
    flex: 1;
    border: 1px solid #e2ebe5;
    border-radius: 6px;
    padding: 12px;
    text-align: center;
}

.firma-label { font-size: 9px; color: #7a9e8e; margin-bottom: 36px; }
.firma-line  { border-top: 1px solid #2d6a4f; }

</style>
</head>

<body>
<div class="page">

    {{-- ── HEADER ── --}}
    <div class="header">
        <div>
            <div class="company-name">Mr.Fruits di La Rosa Fabrizio</div>
            <div class="company-sub">
                Via Ettore Perrone, 16 – 10122 Torino (TO)<br>
                P.IVA: IT 11452750018 &nbsp;|&nbsp; C.F.: LRSFRZ84R04L219T
            </div>
        </div>
        <div class="doc-block">
            <div class="doc-type">{{ $document->type ?? 'DDT' }}</div>
            <div class="doc-number">{{ $document->number }}</div>
            <div class="doc-date">{{ \Carbon\Carbon::parse($document->date)->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- ── MITTENTE / DESTINATARIO ── --}}
    <div class="parti">

        <div class="parte-box">
            <div class="parte-label">Mittente</div>
            <div class="parte-name">Mr.Fruits di La Rosa Fabrizio</div>
            <div class="parte-info">
                Via Ettore Perrone, 16<br>
                10122 Torino (TO)<br>
                P.IVA: IT 11452750018
            </div>
        </div>

        <div class="parte-box">
            <div class="parte-label">Destinatario</div>
            <div class="parte-name">{{ $document->client->company_name ?? '—' }}</div>
            <div class="parte-info">
                {{ $document->client->address ?? '' }}<br>
                {{ $document->client->city ?? '' }}<br>
                @if($document->client->vat ?? null)
                    P.IVA: {{ $document->client->vat }}
                @endif
            </div>
        </div>

    </div>

    {{-- ── META DOCUMENTO ── --}}
    <div class="doc-meta">
        <div class="doc-meta-item">
            <div class="doc-meta-label">Tipo documento</div>
            <div class="doc-meta-value">Documento di Trasporto</div>
        </div>
        <div class="doc-meta-item">
            <div class="doc-meta-label">Numero</div>
            <div class="doc-meta-value">{{ $document->number }}</div>
        </div>
        <div class="doc-meta-item">
            <div class="doc-meta-label">Data</div>
            <div class="doc-meta-value">{{ \Carbon\Carbon::parse($document->date)->format('d/m/Y') }}</div>
        </div>
        <div class="doc-meta-item">
            <div class="doc-meta-label">Condizioni pagamento</div>
            <div class="doc-meta-value">{{ $document->client->payment_terms ?? 'Bonifico bancario' }}</div>
        </div>
    </div>

    {{-- ── TABELLA RIGHE ── --}}
    <table>
        <thead>
            <tr>
                <th class="c" style="width:45px">Colli</th>
                <th class="l">Descrizione articolo</th>
                <th class="c" style="width:55px">Origine</th>
                <th class="c" style="width:40px">UM</th>
                <th class="r" style="width:80px">Tara unit.</th>
                <th class="r" style="width:90px">Qtà netta</th>
                <th class="r" style="width:80px">Prezzo</th>
                <th class="r" style="width:95px">Importo</th>
            </tr>
        </thead>
        <tbody>

        @php
            $totImponibile = 0;
            $totIva        = 0;
        @endphp

        @foreach($rows as $row)

        @php
            $product  = $row->product;
            $saleType = $product->sale_type ?? 'kg';
            $isUnit   = ($saleType === 'unit');

            $um       = $isUnit ? 'PZ' : 'KG';
            $taraUnit = $product->tara ?? 0;

            if ($isUnit) {
                $pezziPerCassa = $product->pieces_per_box ?? 0;
                $qtaNetta      = $row->boxes * $pezziPerCassa;
                $taraUnit      = 0;
            } else {
                $kgLordi  = ($row->kg_real > 0) ? $row->kg_real : $row->kg_estimated;
                $taraTot  = $row->boxes * $taraUnit;
                $qtaNetta = $kgLordi - $taraTot;
            }

            $price   = $row->price_per_kg;
            $importo = $row->total;
            $vatRate = $row->vat_rate ?? $product->vat_rate ?? 4;

            $totImponibile += $importo;
            $totIva        += $importo * ($vatRate / 100);
        @endphp

        <tr>
            <td class="c b">{{ $row->boxes }}</td>
            <td class="l b">{{ $product->name ?? '—' }}</td>
            <td class="c">{{ $product->origin ?? '—' }}</td>

            <td class="c b" style="color:{{ $isUnit ? '#2d6a4f' : '#1a56a0' }}">
                {{ $um }}
            </td>

            <td class="r">
                @if(!$isUnit && $taraUnit > 0)
                    {{ number_format($taraUnit, 3, ',', '.') }}
                @else
                    —
                @endif
            </td>

            <td class="r b">
                @if($isUnit)
                    {{ number_format($qtaNetta, 0, ',', '.') }}
                @else
                    {{ number_format($qtaNetta, 2, ',', '.') }}
                @endif
            </td>

            <td class="r">{{ number_format($price, 2, ',', '.') }}</td>

            <td class="r b">€ {{ number_format($importo, 2, ',', '.') }}</td>
        </tr>

        @endforeach

        </tbody>
    </table>

    {{-- ── TOTALI (IVA solo qui, non in colonna) ── --}}
    <div class="totali-wrap">
        <div class="totali-box">

            <div class="tot-row">
                <span>Imponibile</span>
                <span>€ {{ number_format($totImponibile, 2, ',', '.') }}</span>
            </div>

            <div class="tot-row">
                <span>IVA 4%</span>
                <span>€ {{ number_format($totIva, 2, ',', '.') }}</span>
            </div>

            <div class="tot-row final">
                <span>TOTALE</span>
                <span>€ {{ number_format($totImponibile + $totIva, 2, ',', '.') }}</span>
            </div>

        </div>
    </div>

    {{-- ── FIRME ── --}}
    <div class="firme">
        <div class="firma-box">
            <div class="firma-label">Firma del destinatario</div>
            <div class="firma-line"></div>
        </div>
        <div class="firma-box">
            <div class="firma-label">Firma del mittente</div>
            <div class="firma-line"></div>
        </div>
    </div>

</div>
</body>
</html>