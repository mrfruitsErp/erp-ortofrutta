@extends('layouts.app')

@section('page-title','Nuovo Ordine')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">🧾 Nuovo Ordine</div>
        <div class="page-sub">Crea un ordine cliente</div>
    </div>
    <a href="/orders" class="btn btn-secondary">← Torna agli ordini</a>
</div>

<form method="POST" action="/orders">
@csrf

{{-- TESTATA --}}
<div class="card" style="margin-bottom:20px">
    <div class="form-group">
        <label>Cliente</label>
        <select name="client_id" required>
            <option value="">Seleziona cliente</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}">{{ $client->company_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>Data ordine</label>
        <input type="date" name="date" value="{{ date('Y-m-d') }}" required>
    </div>
</div>

{{-- RIGHE PRODOTTI --}}
<div class="card">

    <div style="font-weight:700;margin-bottom:15px">Prodotti ordine</div>

    <div style="overflow-x:auto">
    <table id="productsTable" style="width:100%;min-width:900px">
        <thead>
            <tr>
                <th style="min-width:160px">Prodotto</th>
                <th style="width:55px">Orig.</th>
                <th style="width:55px;text-align:center">UM</th>
                <th style="width:70px">Colli</th>
                <th style="width:85px">Kg stimati</th>
                <th style="width:85px">Kg reali</th>
                <th style="width:70px">Tara/cassa</th>
                <th style="width:85px">Kg netti</th>
                <th style="width:85px">Prezzo</th>
                <th style="width:90px">Totale</th>
                <th style="width:32px"></th>
            </tr>
        </thead>
        <tbody id="orderRows">
            <tr data-sale="kg">
                <td>
                    <select name="product_id[]" class="productSelect" required>
                        <option value="">— Prodotto —</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}"
                                data-origin="{{ $p->origin }}"
                                data-sale="{{ $p->sale_type }}"
                                data-weight="{{ (float)($p->avg_box_weight ?? 0) }}"
                                data-tara="{{ (float)($p->tara ?? 0) }}"
                                data-price="{{ (float)($p->price ?? 0) }}"
                                data-pieces="{{ (int)($p->pieces_per_box ?? 0) }}">
                                {{ $p->name }}{{ $p->origin ? ' (' . $p->origin . ')' : '' }}{{ $p->avg_box_weight ? ' · ' . number_format($p->avg_box_weight, 1, ',', '.') . 'kg/cs' : '' }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" name="origin[]" readonly style="width:48px"></td>
                <td style="text-align:center">
                    <span class="um-badge" style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:4px;background:#e3f0ff;color:#1a56a0">KG</span>
                </td>
                <td><input type="number" name="colli[]" step="1" value="1" min="1" style="width:60px"></td>
                <td><input type="number" name="kg_estimated[]" step="0.001" readonly style="width:75px;color:#999" placeholder="auto"></td>
                <td><input type="number" name="kg_real[]" step="0.001" style="width:75px" placeholder="auto"></td>

                {{-- TARA: editabile, precompilata dal prodotto ma modificabile --}}
                <td><input type="number" name="tara_unit[]" step="0.001" style="width:62px" placeholder="0"></td>

                {{-- KG NETTI: editabile manualmente, ricalcolato automaticamente ma sovrascrivibile --}}
                <td><input type="number" name="kg_net[]" step="0.001" style="width:75px;font-weight:600" placeholder="auto"></td>

                <td>
                    <input type="number" name="price[]" step="0.01" style="width:72px">
                    <div class="price-lbl" style="font-size:10px;color:#999">€/kg</div>
                </td>
                <td><input type="number" name="total[]" step="0.01" readonly style="width:78px;font-weight:700"></td>
                <td><button type="button" onclick="removeRow(this)" class="btn btn-secondary" style="padding:3px 7px">✕</button></td>
            </tr>
        </tbody>
    </table>
    </div>

    <button type="button" onclick="addRow()" class="btn btn-secondary" style="margin-top:12px">
        + Aggiungi prodotto
    </button>

</div>

<div class="card" style="margin-top:20px;display:flex;justify-content:space-between;align-items:center">
    <div style="font-size:18px;font-weight:700">
        Totale ordine: € <span id="orderTotal">0,00</span>
    </div>
    <button type="submit" class="btn btn-primary">Salva Ordine</button>
</div>

</form>

<script>

const PRODUCTS = {
    @foreach($products as $p)
    {{ $p->id }}: {
        origin:  "{{ $p->origin }}",
        sale:    "{{ $p->sale_type }}",
        weight:  {{ (float)($p->avg_box_weight ?? 0) }},
        tara:    {{ (float)($p->tara ?? 0) }},
        price:   {{ (float)($p->price ?? 0) }},
        pieces:  {{ (int)($p->pieces_per_box ?? 0) }},
    },
    @endforeach
};

function addRow(){
    const tbody  = document.getElementById('orderRows');
    const first  = tbody.rows[0];
    const newRow = first.cloneNode(true);
    newRow.querySelectorAll('input').forEach(i => { if(i.type !== 'button') i.value = ''; });
    newRow.querySelector('[name="colli[]"]').value = 1;
    newRow.querySelector('.productSelect').value = '';
    newRow.setAttribute('data-sale', 'kg');
    const badge = newRow.querySelector('.um-badge');
    if(badge){ badge.textContent = 'KG'; badge.style.background='#e3f0ff'; badge.style.color='#1a56a0'; }
    const lbl = newRow.querySelector('.price-lbl');
    if(lbl) lbl.textContent = '€/kg';
    tbody.appendChild(newRow);
}

function removeRow(btn){
    const tbody = document.getElementById('orderRows');
    if(tbody.rows.length > 1){ btn.closest('tr').remove(); calcTotal(); }
}

// ── CAMBIO PRODOTTO ──────────────────────────────────────────────────────
document.addEventListener('change', function(e){
    if(!e.target.classList.contains('productSelect')) return;
    const row = e.target.closest('tr');
    const pid = e.target.value;
    if(!pid) return;
    const p = PRODUCTS[pid];
    if(!p) return;

    row.querySelector('[name="origin[]"]').value    = p.origin;
    row.querySelector('[name="price[]"]').value     = p.price;
    row.querySelector('[name="tara_unit[]"]').value = p.tara;
    row.setAttribute('data-sale', p.sale);

    const badge = row.querySelector('.um-badge');
    if(badge){
        if(p.sale === 'unit'){
            badge.textContent = 'PZ'; badge.style.background='#e8f5e9'; badge.style.color='#2d6a4f';
        } else {
            badge.textContent = 'KG'; badge.style.background='#e3f0ff'; badge.style.color='#1a56a0';
        }
    }

    const lbl = row.querySelector('.price-lbl');
    if(lbl) lbl.textContent = p.sale === 'unit' ? '€/pz' : '€/kg';

    row.querySelector('[name="kg_real[]"]').value = '';
    row.querySelector('[name="kg_net[]"]').value  = '';

    calcRow(row);
});

// ── INPUT ────────────────────────────────────────────────────────────────
document.addEventListener('input', function(e){
    const row = e.target.closest('tr');
    if(!row || !row.closest('#orderRows')) return;

    // Se l'utente modifica kg_net manualmente, ricalcola solo il totale
    if(e.target.name === 'kg_net[]'){
        calcTotaleFromKgNet(row);
        return;
    }

    // Se cambiano i colli → azzera kg_real e kg_net per ricalcolare
    if(e.target.name === 'colli[]'){
        row.querySelector('[name="kg_real[]"]').value = '';
        const kgNetInput = row.querySelector('[name="kg_net[]"]');
        kgNetInput.value = '';
        delete kgNetInput.dataset.manual;
    }

    // Se cambia la tara → azzera kg_net e ricalcola
    if(e.target.name === 'tara_unit[]'){
        const kgNetInput = row.querySelector('[name="kg_net[]"]');
        kgNetInput.value = '';
        delete kgNetInput.dataset.manual;
    }

    calcRow(row);
});

// ── CALCOLA TOTALE DA KG_NET MANUALE ─────────────────────────────────────
function calcTotaleFromKgNet(row){
    const select = row.querySelector('.productSelect');
    const pid    = select ? select.value : null;
    if(!pid) return;
    const p = PRODUCTS[pid];
    if(!p) return;

    const kgNet = parseFloat(row.querySelector('[name="kg_net[]"]').value) || 0;
    const price = parseFloat(row.querySelector('[name="price[]"]').value)  || 0;

    let total = 0;
    if(p.sale === 'unit'){
        const colli = parseFloat(row.querySelector('[name="colli[]"]').value) || 0;
        total = (colli * p.pieces) * price;
    } else {
        total = kgNet * price;
    }

    row.querySelector('[name="total[]"]').value = total > 0 ? total.toFixed(2) : '';
    calcTotal();
}

// ── CALCOLA RIGA ─────────────────────────────────────────────────────────
function calcRow(row){
    const select = row.querySelector('.productSelect');
    const pid    = select ? select.value : null;
    if(!pid) return;
    const p = PRODUCTS[pid];
    if(!p) return;

    const colli = parseFloat(row.querySelector('[name="colli[]"]').value)    || 0;
    const price = parseFloat(row.querySelector('[name="price[]"]').value)    || 0;

    const taraInput = row.querySelector('[name="tara_unit[]"]');
    const tara      = parseFloat(taraInput.value) || 0;

    const kgEst   = colli * p.weight;
    const taraTot = colli * tara;

    const kgRealInput = row.querySelector('[name="kg_real[]"]');
    let   kgReal = parseFloat(kgRealInput.value) || 0;
    if(!kgReal){
        kgReal = kgEst;
        kgRealInput.placeholder = kgEst > 0 ? kgEst.toFixed(3) : 'auto';
    }

    const kgNet = kgReal - taraTot;

    row.querySelector('[name="kg_estimated[]"]').value = kgEst > 0 ? kgEst.toFixed(3) : '';

    // Imposta kg_net solo se l'utente non lo ha modificato manualmente
    const kgNetInput = row.querySelector('[name="kg_net[]"]');
    if(!kgNetInput.dataset.manual){
        kgNetInput.value = kgNet !== 0 ? kgNet.toFixed(3) : '';
    }

    let total = 0;
    if(p.sale === 'unit'){
        total = (colli * p.pieces) * price;
    } else {
        const kgNetUsato = parseFloat(kgNetInput.value) || kgNet;
        total = kgNetUsato * price;
    }

    row.querySelector('[name="total[]"]').value = total > 0 ? total.toFixed(2) : '';
    calcTotal();
}

// Marca kg_net come modificato manualmente
document.addEventListener('focus', function(e){
    if(e.target.name === 'kg_net[]') e.target.dataset.manual = '';
}, true);

document.addEventListener('blur', function(e){
    if(e.target.name === 'kg_net[]' && e.target.value === ''){
        delete e.target.dataset.manual;
    }
}, true);

function calcTotal(){
    let t = 0;
    document.querySelectorAll('#orderRows [name="total[]"]').forEach(el => {
        t += parseFloat(el.value) || 0;
    });
    document.getElementById('orderTotal').textContent =
        t.toLocaleString('it-IT', {minimumFractionDigits:2, maximumFractionDigits:2});
}

</script>

@endsection