<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="utf-8">
<title>{{ $document->number }}</title>
<style>

@page {
    margin: 10px 15px;
    size: A4;
}

* { box-sizing:border-box; margin:0; padding:0; }

body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 9px;
    color: #2d3748;
    background: #fff;
    line-height: 1.3;
}

/* ══════════════════════════════════════════
   HEADER
═══════════════════════════════════════════ */
.header {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    padding: 10px 18px;
    margin-bottom: 10px;
    border-radius: 5px;
}

.header-table {
    width: 100%;
}

.header-left {
    color: #fff;
}

.company-brand {
    font-size: 18px;
    font-weight: 700;
}

.company-tagline {
    font-size: 8px;
    opacity: 0.85;
}

.header-right {
    text-align: right;
}

.doc-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    border-radius: 5px;
    padding: 6px 12px;
    text-align: center;
}

.doc-type {
    font-size: 16px;
    font-weight: 700;
    color: #fff;
    letter-spacing: 1px;
}

.doc-number {
    font-size: 9px;
    color: rgba(255,255,255,0.9);
    margin-top: 2px;
    font-family: monospace;
}

/* ══════════════════════════════════════════
   INFO CARDS
═══════════════════════════════════════════ */
.info-grid {
    width: 100%;
    margin-bottom: 8px;
}

.info-card {
    background: #f7fafc;
    border-radius: 4px;
    padding: 8px 10px;
    border-left: 3px solid #48bb78;
    vertical-align: top;
}

.info-card.mittente {
    border-left-color: #2d6a4f;
}

.info-label {
    font-size: 7px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #48bb78;
    margin-bottom: 3px;
}

.info-card.mittente .info-label {
    color: #2d6a4f;
}

.info-value {
    font-size: 10px;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 2px;
}

.info-sub {
    font-size: 8px;
    color: #718096;
    line-height: 1.3;
}

/* ══════════════════════════════════════════
   META ROW
═══════════════════════════════════════════ */
.meta-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
    border: 1px solid #e2e8f0;
}

.meta-table td {
    padding: 5px 8px;
    border-right: 1px solid #e2e8f0;
    text-align: center;
}

.meta-table td:last-child {
    border-right: none;
}

.meta-label {
    font-size: 6px;
    font-weight: 600;
    text-transform: uppercase;
    color: #a0aec0;
    display: block;
    margin-bottom: 1px;
}

.meta-value {
    font-size: 8px;
    font-weight: 700;
    color: #2d3748;
}

/* ══════════════════════════════════════════
   TABELLA PRODOTTI
═══════════════════════════════════════════ */
.products-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
}

.products-table th {
    background: #2d3748;
    padding: 5px 4px;
    font-size: 7px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #fff;
    text-align: left;
}

.products-table th.r { text-align: right; }
.products-table th.c { text-align: center; }

.products-table td {
    padding: 4px;
    font-size: 8px;
    border-bottom: 1px solid #edf2f7;
    vertical-align: middle;
}

.products-table tr:nth-child(even) td {
    background: #f9fafb;
}

.r { text-align: right; }
.c { text-align: center; }
.b { font-weight: 700; }

.origin-badge {
    display: inline-block;
    background: #e2e8f0;
    color: #4a5568;
    font-size: 7px;
    font-weight: 700;
    padding: 1px 4px;
    border-radius: 3px;
}

/* ══════════════════════════════════════════
   FOOTER
═══════════════════════════════════════════ */
.footer-table {
    width: 100%;
    margin-top: 6px;
}

.footer-left {
    vertical-align: top;
    width: 55%;
}

.footer-right {
    vertical-align: top;
    width: 45%;
}

.transport-box {
    background: #f7fafc;
    border-radius: 4px;
    padding: 6px 8px;
    font-size: 8px;
}

.transport-row {
    margin-bottom: 3px;
}

.transport-label {
    font-size: 7px;
    font-weight: 700;
    text-transform: uppercase;
    color: #718096;
    display: inline-block;
    width: 95px;
}

.transport-value {
    color: #2d3748;
}

.colli-badge {
    display: inline-block;
    background: #2d3748;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 10px;
    margin-top: 4px;
}

.totals-box {
    border: 2px solid #48bb78;
    border-radius: 4px;
    overflow: hidden;
}

.totals-row {
    padding: 4px 8px;
    border-bottom: 1px solid #e2e8f0;
}

.totals-row:last-child {
    border-bottom: none;
}

.totals-row table {
    width: 100%;
}

.totals-label {
    font-size: 8px;
    color: #4a5568;
}

.totals-value {
    text-align: right;
    font-size: 9px;
    font-weight: 600;
    font-family: monospace;
}

.totals-row.final {
    background: #48bb78;
    padding: 5px 8px;
}

.totals-row.final .totals-label,
.totals-row.final .totals-value {
    color: #fff;
    font-size: 10px;
    font-weight: 700;
}

/* ══════════════════════════════════════════
   FIRME
═══════════════════════════════════════════ */
.signatures-table {
    width: 100%;
    margin-top: 12px;
}

.signature-cell {
    width: 50%;
    padding: 0 6px;
}

.signature-box {
    border: 1px dashed #cbd5e0;
    border-radius: 4px;
    padding: 6px;
    text-align: center;
    height: 45px;
}

.signature-label {
    font-size: 7px;
    font-weight: 600;
    text-transform: uppercase;
    color: #718096;
}

/* ══════════════════════════════════════════
   DISCLAIMER
═══════════════════════════════════════════ */
.disclaimer {
    margin-top: 8px;
    padding: 5px 8px;
    background: #fffaf0;
    border-left: 3px solid #ed8936;
    font-size: 7px;
    color: #744210;
}

</style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <table class="header-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header-left">
                    <div class="company-brand">🍎 Mr.Fruits</div>
                    <div class="company-tagline">Frutta & Verdura</div>
                </td>
                <td class="header-right">
                    <div class="doc-badge">
                        <div class="doc-type">{{ $document->type ?? 'DDT' }}</div>
                        <div class="doc-number">{{ $document->number }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFO GRID: MITTENTE + DESTINATARIO --}}
    <table class="info-grid" cellpadding="0" cellspacing="0">
        <tr>
            <td class="info-card mittente" style="width:48%">
                <div class="info-label">Mittente / Cedente</div>
                <div class="info-value">Mr.Fruits di La Rosa Fabrizio</div>
                <div class="info-sub">
                    Via Ettore Perrone, 16 – 10122 Torino (TO)<br>
                    P.IVA: IT 11452750018 | C.F.: LRSFRZ84R04L219T
                </div>
            </td>
            <td style="width:4%"></td>
            <td class="info-card" style="width:48%">
                <div class="info-label">Destinatario</div>
                <div class="info-value">{{ $document->client->company_name ?? '—' }}</div>
                <div class="info-sub">
                    {{ $document->client->address ?? '' }}, {{ $document->client->cap ?? '' }} {{ $document->client->city ?? '' }} {{ $document->client->province ? '('.$document->client->province.')' : '' }}
                    @if($document->client->vat_number ?? $document->client->vat ?? null)
                        <br>P.IVA: {{ $document->client->vat_number ?? $document->client->vat }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- META ROW --}}
    <table class="meta-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <span class="meta-label">Tipo Documento</span>
                <span class="meta-value">D.d.T.</span>
            </td>
            <td>
                <span class="meta-label">Nr. Documento</span>
                <span class="meta-value">{{ preg_replace('/[^0-9]/', '', $document->number) }}</span>
            </td>
            <td>
                <span class="meta-label">Data</span>
                <span class="meta-value">{{ \Carbon\Carbon::parse($document->date)->format('d/m/Y') }}</span>
            </td>
            <td>
                <span class="meta-label">Pagamento</span>
                <span class="meta-value">{{ $document->client->payment_terms ?? 'data fattura' }}</span>
            </td>
            <td>
                <span class="meta-label">Pagina</span>
                <span class="meta-value">1/1</span>
            </td>
        </tr>
    </table>

    {{-- TABELLA PRODOTTI (senza colonna IVA) --}}
    @php
        $totImponibile = 0;
        $totColli = 0;
        $ivaPerAliquota = [];
    @endphp

    <table class="products-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th class="c" style="width:40px">Colli</th>
                <th>Descrizione Articolo</th>
                <th class="c" style="width:40px">Orig.</th>
                <th class="c" style="width:35px">UM</th>
                <th class="r" style="width:50px">Tara</th>
                <th class="r" style="width:70px">Qtà Netta</th>
                <th class="r" style="width:55px">Prezzo</th>
                <th class="r" style="width:70px">Importo</th>
            </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            @php
                $product = $row->product;
                $origin = $product->origin ?? 'IT';
                $taraUnit = $product->tara ?? 0;
                
                $kgLordi = ($row->kg_real > 0) ? $row->kg_real : $row->kg_estimated;
                $taraTot = $row->boxes * $taraUnit;
                $qtaNetta = $kgLordi - $taraTot;
                if ($qtaNetta <= 0) $qtaNetta = $kgLordi;

                $price = $row->price_per_kg;
                $importo = $row->total;
                $vatRate = (int)($row->vat_rate ?? $product->vat_rate ?? 4);

                $totImponibile += $importo;
                $totColli += $row->boxes;

                if (!isset($ivaPerAliquota[$vatRate])) $ivaPerAliquota[$vatRate] = 0;
                $ivaPerAliquota[$vatRate] += $importo * ($vatRate / 100);
            @endphp
            <tr>
                <td class="c b">{{ $row->boxes }}</td>
                <td class="b">{{ $product->name ?? '—' }}</td>
                <td class="c"><span class="origin-badge">{{ strtoupper($origin) }}</span></td>
                <td class="c">KG</td>
                <td class="r">{{ number_format($taraUnit, 2, ',', '.') }}</td>
                <td class="r b">{{ number_format($qtaNetta, 3, ',', '.') }}</td>
                <td class="r">{{ number_format($price, 2, ',', '.') }}</td>
                <td class="r b">€ {{ number_format($importo, 2, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- FOOTER --}}
    @php
        $totIva = array_sum($ivaPerAliquota);
        $totFattura = $totImponibile + $totIva;
    @endphp

    <table class="footer-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="footer-left">
                <div class="transport-box">
                    <div class="transport-row">
                        <span class="transport-label">Causale Trasporto</span>
                        <span class="transport-value">Vendita</span>
                    </div>
                    <div class="transport-row">
                        <span class="transport-label">Trasporto a mezzo</span>
                        <span class="transport-value">Cedente</span>
                    </div>
                    <div class="transport-row">
                        <span class="transport-label">Aspetto dei beni</span>
                        <span class="transport-value">Casse</span>
                    </div>
                    <div class="transport-row">
                        <span class="transport-label">Destinazione</span>
                        <span class="transport-value">Idem c/o sede legale</span>
                    </div>
                    <div>
                        <span class="colli-badge">{{ $totColli }} COLLI</span>
                    </div>
                </div>
            </td>
            <td style="width:10px"></td>
            <td class="footer-right">
                <div class="totals-box">
                    <div class="totals-row">
                        <table cellpadding="0" cellspacing="0"><tr>
                            <td class="totals-label">Imponibile</td>
                            <td class="totals-value">€ {{ number_format($totImponibile, 2, ',', '.') }}</td>
                        </tr></table>
                    </div>
                    @foreach($ivaPerAliquota as $aliquota => $importoIva)
                    <div class="totals-row">
                        <table cellpadding="0" cellspacing="0"><tr>
                            <td class="totals-label">IVA {{ $aliquota }}%</td>
                            <td class="totals-value">€ {{ number_format($importoIva, 2, ',', '.') }}</td>
                        </tr></table>
                    </div>
                    @endforeach
                    <div class="totals-row final">
                        <table cellpadding="0" cellspacing="0"><tr>
                            <td class="totals-label">TOTALE</td>
                            <td class="totals-value">€ {{ number_format($totFattura, 2, ',', '.') }}</td>
                        </tr></table>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- FIRME --}}
    <table class="signatures-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="signature-cell">
                <div class="signature-box">
                    <div class="signature-label">Firma Conducente</div>
                </div>
            </td>
            <td class="signature-cell">
                <div class="signature-box">
                    <div class="signature-label">Firma Destinatario</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- DISCLAIMER --}}
    <div class="disclaimer">
        Eventuali reclami riguardanti qualità o quantità della merce dovranno essere inoltrati entro le ore 24 del giorno stesso della consegna.
    </div>

</body>
</html>