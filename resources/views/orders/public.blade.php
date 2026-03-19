<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Ordine — {{ $client->company_name }}</title>
<style>
* { box-sizing:border-box; margin:0; padding:0 }

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: #f0f4f1;
    color: #1b2d27;
    padding-bottom: 120px;
}

.header {
    background: #2d6a4f;
    color: white;
    padding: 16px 20px;
    position: sticky;
    top: 0;
    z-index: 100;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-title { font-size: 15px; font-weight: 700; }
.header-sub   { font-size: 12px; opacity: 0.8; margin-top: 2px; }

.total-badge {
    background: #40916c;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 20px;
    padding: 6px 14px;
    font-weight: 700;
    font-size: 15px;
    white-space: nowrap;
}

.container { max-width: 700px; margin: 0 auto; padding: 16px; }

.alert-success {
    background: #d4edda;
    color: #2d6a4f;
    border: 1px solid #b7e4c7;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 16px;
    font-weight: 600;
}

/* CONSEGNA */
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

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

label { font-size: 12px; color: #555; display: block; margin-bottom: 4px; }

input[type=date], input[type=number], select {
    width: 100%;
    padding: 10px 12px;
    border: 1.5px solid #e2ebe5;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    color: #1b2d27;
    background: #fafcfb;
    outline: none;
    transition: border-color 0.2s;
}

input:focus, select:focus { border-color: #2d6a4f; background: white; }

/* CATEGORIA */
.category-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #7a9e8e;
    letter-spacing: 0.8px;
    padding: 8px 0 6px;
    border-bottom: 1px solid #e2ebe5;
    margin-bottom: 4px;
}

/* RIGA PRODOTTO */
.product-row {
    display: grid;
    grid-template-columns: 1fr 80px 90px;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #f0f4f1;
}

.product-row:last-child { border-bottom: none; }

.product-name  { font-weight: 600; font-size: 14px; }
.product-price { font-size: 12px; color: #7a9e8e; margin-top: 2px; }

.qty-input {
    text-align: center;
    font-weight: 700;
    font-size: 15px;
    padding: 8px 6px !important;
}

.qty-input:not([value="0"]):not([value=""]) {
    border-color: #2d6a4f !important;
    background: #f0faf4 !important;
    color: #2d6a4f !important;
}

.row-total {
    text-align: right;
    font-weight: 700;
    font-size: 14px;
    color: #2d6a4f;
    font-family: monospace;
}

/* ORDINI PRECEDENTI */
.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f0f4f1;
    font-size: 13px;
}
.order-item:last-child { border-bottom: none; }
.order-num  { font-weight: 700; color: #2d6a4f; }
.order-date { color: #999; font-size: 12px; }
.order-total{ font-weight: 700; font-family: monospace; }

/* FOOTER FISSO */
.footer-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    border-top: 2px solid #e2ebe5;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 100;
}

.footer-total { font-size: 18px; font-weight: 700; }
.footer-total span { color: #2d6a4f; }

.btn-submit {
    background: #2d6a4f;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 12px 28px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
}
.btn-submit:active { background: #1b4332; }

@media(max-width:480px){
    .form-row { grid-template-columns: 1fr; }
    .product-row { grid-template-columns: 1fr 70px 80px; }
}
</style>
</head>

<body>

<div class="header">
    <div>
        <div class="header-title">{{ $client->company_name }}</div>
        <div class="header-sub">Nuovo ordine</div>
    </div>
    <div class="total-badge" id="headerTotal">€ 0,00</div>
</div>

<div class="container">

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    <form method="POST">
    @csrf

    {{-- CONSEGNA --}}
    <div class="card">
        <div class="card-title">📅 Consegna</div>
        <div class="form-row">
            <div>
                <label>Data consegna</label>
                <input type="date" name="delivery_date"
                       value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
            </div>
            <div>
                <label>Fascia oraria</label>
                <select name="delivery_slot">
                    @if($client->fascia_oraria_inizio && $client->fascia_oraria_fine)
                        <option value="{{ $client->fascia_oraria_inizio }}-{{ $client->fascia_oraria_fine }}" selected>
                            {{ $client->fascia_oraria_inizio }} - {{ $client->fascia_oraria_fine }}
                        </option>
                    @endif
                    @foreach($slots as $slot)
                        <option value="{{ $slot->start_time }}-{{ $slot->end_time }}">
                            {{ substr($slot->start_time,0,5) }} - {{ substr($slot->end_time,0,5) }}
                        </option>
                    @endforeach
                    @if(!$client->fascia_oraria_inizio && $slots->isEmpty())
                        <option value="">Da concordare</option>
                    @endif
                </select>
            </div>
        </div>
    </div>

    {{-- PRODOTTI --}}
    @php $categories = $products->groupBy('category'); @endphp

    @foreach($categories as $category => $items)
    <div class="card">
        <div class="category-label">{{ $category ?: 'Prodotti' }}</div>

        @foreach($items as $product)
        @php
            $isUnit = $product->sale_type === 'unit';
            $um     = $isUnit ? 'pz' : 'kg';
            $disp   = $product->disponibilita ?? 'disponibile';
            $step   = $product->ordine_step ?? 'colli';
            $min    = (float)($product->ordine_min ?? 1);
            $peso   = (float)($product->avg_box_weight ?? 0);

            // Step di input in base alla modalità
            $inputStep = match($step) {
                'colli'       => 1,
                'mezzo_collo' => 0.5,
                'kg'          => 0.5,
                'grammi'      => 0.1,
                default       => 1,
            };

            // Override con regola cliente se prodotto usa colli
            $clientStep = $client->modalita_ordine ?? 'colli';
            if(in_array($step, ['colli', 'mezzo_collo'])) {
                $inputStep = match($clientStep) {
                    'colli'       => 1,
                    'mezzo_collo' => 0.5,
                    default       => 1,
                };
            }
        @endphp
        <div class="product-row" style="{{ $disp == 'non_disponibile' ? 'opacity:0.7' : '' }}">
            <div>
                <div class="product-name">
                    {{ $product->name }}
                    @if($disp == 'su_richiesta')
                        <span style="background:#fff3e0;color:#e65100;font-size:10px;padding:1px 6px;border-radius:10px;font-weight:600;margin-left:4px">Su richiesta</span>
                    @elseif($disp == 'non_disponibile')
                        <span style="background:#fde8e8;color:#c0392b;font-size:10px;padding:1px 6px;border-radius:10px;font-weight:600;margin-left:4px">Non disponibile</span>
                    @endif
                </div>
                <div class="product-price">
                    € {{ number_format($product->price, 2, ',', '.') }}/{{ $um }}
                    @if($product->origin) · {{ $product->origin }} @endif
                    @if($peso > 0 && $step != 'grammi' && $step != 'kg')
                        · cassa ≈ {{ number_format($peso, 1, ',', '.') }} kg
                    @endif
                    @if($step == 'grammi')
                        · min {{ number_format($min * 1000, 0) }}g
                    @elseif($step == 'kg')
                        · min {{ number_format($min, 2, ',', '.') }} kg
                    @endif
                </div>
            </div>
            <input type="number"
                   class="qty-input"
                   step="{{ $inputStep }}"
                   min="0"
                   name="qty[{{ $product->id }}]"
                   value="0"
                   data-price="{{ $product->price }}"
                   data-id="{{ $product->id }}"
                   data-step="{{ $step }}"
                   data-peso="{{ $peso }}"
                   oninput="calcRow(this)">
            <div class="row-total" id="row-{{ $product->id }}">—</div>
        </div>
        @endforeach
    </div>
    @endforeach

    </form>

    {{-- ULTIMI ORDINI --}}
    @php
        $ultimi = \App\Models\Order::where('client_id', $client->id)
            ->orderBy('created_at','desc')
            ->limit(5)
            ->get();
    @endphp

    @if($ultimi->count() > 0)
    <div class="card">
        <div class="card-title">📋 Ultimi ordini</div>
        @foreach($ultimi as $ord)
        <a href="/order/{{ $client->order_token }}/ordine/{{ $ord->id }}"
           style="text-decoration:none;color:inherit;display:block">
        <div class="order-item" style="cursor:pointer">
            <div>
                <div class="order-num">{{ $ord->number }}</div>
                <div class="order-date">{{ \Carbon\Carbon::parse($ord->date)->format('d/m/Y') }}</div>
            </div>
            <div style="text-align:right">
                <div class="order-total">€ {{ number_format($ord->total, 2, ',', '.') }}</div>
                <div style="font-size:11px;color:#999;margin-top:2px">
                    @if($ord->status == 'draft') In attesa
                    @elseif($ord->status == 'confirmed') Confermato
                    @elseif($ord->status == 'invoiced') Evaso
                    @elseif($ord->status == 'web') Ricevuto
                    @else {{ $ord->status }}
                    @endif
                    &nbsp;›
                </div>
            </div>
        </div>
        </a>
        @endforeach
    </div>
    @endif

</div>

{{-- FOOTER FISSO --}}
<div class="footer-bar">
    <div class="footer-total">
        Totale: <span id="footerTotal">€ 0,00</span>
    </div>
    <button type="submit" class="btn-submit" onclick="submitOrder()">
        ✅ Invia ordine
    </button>
</div>

<script>
let orderTotal = 0;

function calcRow(input) {
    const price = parseFloat(input.dataset.price) || 0;
    const qty   = parseFloat(input.value) || 0;
    const id    = input.dataset.id;
    const step  = input.dataset.step || 'colli';
    const peso  = parseFloat(input.dataset.peso) || 0;

    let displayTotal = '—';
    if (qty > 0) {
        let importo = 0;
        if (step === 'colli' || step === 'mezzo_collo') {
            // colli × peso cassa × prezzo/kg
            const kg = qty * peso;
            importo  = kg * price;
            displayTotal = '≈ € ' + importo.toLocaleString('it-IT', {minimumFractionDigits:2, maximumFractionDigits:2});
        } else {
            // kg o grammi: qty direttamente
            importo     = qty * price;
            displayTotal = '€ ' + importo.toLocaleString('it-IT', {minimumFractionDigits:2, maximumFractionDigits:2});
        }
    }

    const cell = document.getElementById('row-' + id);
    if (cell) cell.textContent = displayTotal;

    // Ricalcola totale
    let t = 0;
    document.querySelectorAll('.qty-input').forEach(el => {
        const p  = parseFloat(el.dataset.price) || 0;
        const q  = parseFloat(el.value) || 0;
        const s  = el.dataset.step || 'colli';
        const pe = parseFloat(el.dataset.peso) || 0;
        if (s === 'colli' || s === 'mezzo_collo') {
            t += q * pe * p;
        } else {
            t += q * p;
        }
    });

    const fmt = '€ ' + t.toLocaleString('it-IT', {minimumFractionDigits:2, maximumFractionDigits:2});
    document.getElementById('footerTotal').textContent = fmt;
    document.getElementById('headerTotal').textContent = fmt;
}

function submitOrder() {
    const form = document.querySelector('form');
    let hasQty = false;

    document.querySelectorAll('.qty-input').forEach(el => {
        if (parseFloat(el.value) > 0) hasQty = true;
    });

    if (!hasQty) {
        alert('Inserisci almeno un prodotto prima di inviare l\'ordine.');
        return;
    }

    form.submit();
}
</script>

</body>
</html>