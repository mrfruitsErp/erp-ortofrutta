@extends('layouts.app')

@section('page-title','Modifica Ordine')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">✏️ Modifica {{ $order->number }}</div>
        <div class="page-sub">Cliente: {{ $order->client->company_name }}</div>
    </div>
    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary">← Torna all'ordine</a>
</div>

<form method="POST" action="{{ route('orders.update', $order->id) }}">
@csrf
@method('PUT')

{{-- TESTATA --}}
<div class="card" style="margin-bottom:20px">
    <div class="form-group">
        <label>Cliente</label>
        <select name="client_id" required>
            @foreach($clients as $client)
                <option value="{{ $client->id }}"
                    {{ $order->client_id == $client->id ? 'selected' : '' }}>
                    {{ $client->company_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>Data ordine</label>
        <input type="date" name="date" value="{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}" required>
    </div>
</div>

{{-- RIGHE PRODOTTI --}}
<div class="card">

    <div style="font-weight:700;margin-bottom:15px">Prodotti ordine</div>

    <div style="overflow-x:auto">
    <table id="productsTable" style="width:100%;min-width:800px">
        <thead>
            <tr>
                <th style="min-width:160px">Prodotto</th>
                <th style="width:65px">Origine</th>
                <th style="width:75px">Colli</th>
                <th style="width:90px" class="col-kg">Kg stimati</th>
                <th style="width:90px" class="col-kg">Kg reali</th>
                <th style="width:75px" class="col-kg">Tara/cassa</th>
                <th style="width:90px" class="col-kg">Kg netti</th>
                <th style="width:90px">Prezzo</th>
                <th style="width:95px">Totale</th>
                <th style="width:36px"></th>
            </tr>
        </thead>
        <tbody id="orderRows">

            @foreach($order->items as $item)
            @php
                $p       = $item->product;
                $isUnit  = ($p->sale_type ?? 'kg') === 'unit';
            @endphp
            <tr data-sale="{{ $p->sale_type ?? 'kg' }}">
                <td>
                    <select name="product_id[]" class="productSelect" required>
                        <option value="">— Prodotto —</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}"
                                data-origin="{{ $prod->origin }}"
                                data-sale="{{ $prod->sale_type }}"
                                data-weight="{{ $prod->avg_box_weight ?? 0 }}"
                                data-tara="{{ $prod->tara ?? 0 }}"
                                data-price="{{ $prod->price ?? 0 }}"
                                data-pieces="{{ $prod->pieces_per_box ?? 0 }}"
                                {{ $item->product_id == $prod->id ? 'selected' : '' }}>
                                {{ $prod->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" name="origin[]" value="{{ $item->origin ?? $p->origin }}" readonly style="width:55px"></td>
                <td><input type="number" name="colli[]" step="1" value="{{ $item->colli ?? 1 }}" min="1" style="width:65px"></td>
                <td class="col-kg" style="{{ $isUnit ? 'display:none' : '' }}">
                    <input type="number" name="kg_estimated[]" step="0.001" value="{{ $item->kg_estimated }}" readonly style="width:80px;color:#999">
                </td>
                <td class="col-kg" style="{{ $isUnit ? 'display:none' : '' }}">
                    <input type="number" name="kg_real[]" step="0.001" value="{{ $item->kg_real }}" style="width:80px">
                </td>
                <td class="col-kg" style="{{ $isUnit ? 'display:none' : '' }}">
                    <input type="number" name="tara_unit[]" step="0.001" value="{{ $item->tara }}" readonly style="width:65px;color:#999">
                </td>
                <td class="col-kg" style="{{ $isUnit ? 'display:none' : '' }}">
                    <input type="number" name="kg_net[]" step="0.001" value="{{ $item->kg_net }}" readonly style="width:80px;font-weight:600">
                </td>
                <td>
                    <input type="number" name="price[]" step="0.01" value="{{ $item->price_kg ?? $item->price }}" style="width:78px">
                    <div class="price-lbl" style="font-size:10px;color:#999">{{ $isUnit ? '€/pz' : '€/kg' }}</div>
                </td>
                <td><input type="number" name="total[]" step="0.01" value="{{ $item->total }}" readonly style="width:82px;font-weight:700"></td>
                <td><button type="button" onclick="removeRow(this)" class="btn btn-secondary" style="padding:3px 7px">✕</button></td>
            </tr>
            @endforeach

        </tbody>
    </table>
    </div>

    <button type="button" onclick="addRow()" class="btn btn-secondary" style="margin-top:12px">
        + Aggiungi prodotto
    </button>

</div>

<div class="card" style="margin-top:20px;display:flex;justify-content:space-between;align-items:center">
    <div style="font-size:18px;font-weight:700">
        Totale ordine: € <span id="orderTotal">{{ number_format($order->total,2,',','.') }}</span>
    </div>
    <button type="submit" class="btn btn-primary">Aggiorna Ordine</button>
</div>

</form>

{{-- Template riga vuota --}}
<template id="rowTemplate">
    <tr data-sale="kg">
        <td>
            <select name="product_id[]" class="productSelect" required>
                <option value="">— Prodotto —</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}"
                        data-origin="{{ $p->origin }}"
                        data-sale="{{ $p->sale_type }}"
                        data-weight="{{ $p->avg_box_weight ?? 0 }}"
                        data-tara="{{ $p->tara ?? 0 }}"
                        data-price="{{ $p->price ?? 0 }}"
                        data-pieces="{{ $p->pieces_per_box ?? 0 }}">
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td><input type="text" name="origin[]" readonly style="width:55px"></td>
        <td><input type="number" name="colli[]" step="1" value="1" min="1" style="width:65px"></td>
        <td class="col-kg"><input type="number" name="kg_estimated[]" step="0.001" readonly style="width:80px;color:#999"></td>
        <td class="col-kg"><input type="number" name="kg_real[]" step="0.001" style="width:80px"></td>
        <td class="col-kg"><input type="number" name="tara_unit[]" step="0.001" readonly style="width:65px;color:#999"></td>
        <td class="col-kg"><input type="number" name="kg_net[]" step="0.001" readonly style="width:80px;font-weight:600"></td>
        <td>
            <input type="number" name="price[]" step="0.01" style="width:78px">
            <div class="price-lbl" style="font-size:10px;color:#999">€/kg</div>
        </td>
        <td><input type="number" name="total[]" step="0.01" readonly style="width:82px;font-weight:700"></td>
        <td><button type="button" onclick="removeRow(this)" class="btn btn-secondary" style="padding:3px 7px">✕</button></td>
    </tr>
</template>

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
    const template = document.getElementById('rowTemplate');
    document.getElementById('orderRows').appendChild(template.content.cloneNode(true));
}

function removeRow(btn){
    const tbody = document.getElementById('orderRows');
    if(tbody.rows.length > 1){ btn.closest('tr').remove(); calcTotal(); }
}

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
    const lbl = row.querySelector('.price-lbl');
    if(lbl) lbl.textContent = p.sale === 'unit' ? '€/pz' : '€/kg';
    setKgCols(row, p.sale);
    calcRow(row);
});

document.addEventListener('input', function(e){
    const row = e.target.closest('tr');
    if(row && row.closest('#orderRows')) calcRow(row);
});

function setKgCols(row, sale){
    row.querySelectorAll('.col-kg').forEach(td => td.style.display = (sale === 'unit') ? 'none' : '');
}

function calcRow(row){
    const select = row.querySelector('.productSelect');
    const pid    = select ? select.value : null;
    if(!pid) return;
    const p = PRODUCTS[pid];
    if(!p) return;

    const colli = parseFloat(row.querySelector('[name="colli[]"]').value) || 0;
    const price = parseFloat(row.querySelector('[name="price[]"]').value) || 0;
    let total   = 0;

    if(p.sale === 'unit'){
        total = colli * p.pieces * price;
        row.querySelector('[name="kg_net[]"]').value = (colli * (p.weight - p.tara)).toFixed(3);
    } else {
        const kgEst   = colli * p.weight;
        const taraTot = colli * p.tara;
        const kgRealInput = row.querySelector('[name="kg_real[]"]');
        let kgReal = parseFloat(kgRealInput.value) || 0;
        if(!kgReal){ kgReal = kgEst; kgRealInput.value = kgEst.toFixed(3); }
        const kgNet = kgReal - taraTot;
        row.querySelector('[name="kg_estimated[]"]').value = kgEst.toFixed(3);
        row.querySelector('[name="tara_unit[]"]').value    = p.tara.toFixed(3);
        row.querySelector('[name="kg_net[]"]').value       = kgNet.toFixed(3);
        total = kgNet * price;
    }

    row.querySelector('[name="total[]"]').value = total.toFixed(2);
    calcTotal();
}

function calcTotal(){
    let t = 0;
    document.querySelectorAll('#orderRows [name="total[]"]').forEach(el => t += parseFloat(el.value)||0);
    document.getElementById('orderTotal').textContent =
        t.toLocaleString('it-IT',{minimumFractionDigits:2,maximumFractionDigits:2});
}

// Ricalcola tutte le righe al caricamento
window.addEventListener('load', function(){
    document.querySelectorAll('#orderRows tr').forEach(row => {
        const select = row.querySelector('.productSelect');
        if(select && select.value) calcRow(row);
    });
});

</script>

@endsection