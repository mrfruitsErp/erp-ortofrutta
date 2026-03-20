@extends('layouts.app')

@section('page-title', 'Listino: ' . $priceList->nome)

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">📋 {{ $priceList->nome }}</div>
        <div class="page-sub">{{ $priceList->descrizione }}</div>
    </div>
    <a href="{{ route('price-lists.index') }}" class="btn btn-secondary">← Torna ai listini</a>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('price-lists.update', $priceList->id) }}">
@csrf
@method('PUT')

{{-- IMPOSTAZIONI LISTINO --}}
<div class="card" style="margin-bottom:20px">
    <div style="font-weight:700;margin-bottom:16px">⚙️ Impostazioni Listino</div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
        <div class="form-group">
            <label>Nome listino</label>
            <input type="text" name="nome" value="{{ $priceList->nome }}" required>
        </div>
        <div class="form-group">
            <label>Sconto globale %</label>
            <input type="number" step="0.01" name="sconto_default_pct"
                   value="{{ $priceList->sconto_default_pct }}" placeholder="0">
            <div style="font-size:10px;color:#999;margin-top:3px">Applicato a tutti i prodotti senza prezzo specifico</div>
        </div>
        <div class="form-group">
            <label>Ordine minimo (€)</label>
            <input type="number" step="0.01" name="ordine_min_importo"
                   value="{{ $priceList->ordine_min_importo }}" placeholder="0">
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="form-group">
            <label>Pagamento di default</label>
            <select name="payment_method_id">
                <option value="">— Nessun default —</option>
                @foreach($paymentMethods as $pm)
                    <option value="{{ $pm->id }}" {{ ($priceList->payment_method_id ?? '') == $pm->id ? 'selected' : '' }}>
                        {{ $pm->nome }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Descrizione</label>
            <input type="text" name="descrizione" value="{{ $priceList->descrizione }}">
        </div>
    </div>

    <div style="margin-top:8px">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="puo_ordinare_kg" value="1"
                   {{ $priceList->puo_ordinare_kg ? 'checked' : '' }}
                   style="accent-color:var(--green, #7a9e8e)">
            <span>I clienti di questo listino possono ordinare anche a kg (prodotti venduti a cassa)</span>
        </label>
    </div>
</div>

{{-- TABELLA PRODOTTI --}}
<div class="card" style="padding:0;overflow:hidden">

    <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
        <div style="font-weight:700">📦 Prezzi per prodotto</div>
        <div style="display:flex;gap:8px;align-items:center">
            <input type="text" id="searchProd" placeholder="🔍 Cerca prodotto..."
                   style="max-width:200px;margin:0;padding:6px 10px;font-size:12px"
                   oninput="filterProducts()">
            <select id="filterCat" style="max-width:160px;margin:0;padding:6px 10px;font-size:12px" onchange="filterProducts()">
                <option value="">Tutte le categorie</option>
                @foreach($products->pluck('category')->unique()->sort() as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div style="overflow-x:auto">
    <table style="width:100%;min-width:1000px">
        <thead>
            <tr>
                <th style="min-width:180px">Prodotto</th>
                <th style="width:60px">Cat.</th>
                <th style="width:70px">Mod.</th>
                <th style="width:80px;text-align:right">Prezzo base</th>
                <th style="width:90px">Prezzo override</th>
                <th style="width:70px">Sconto %</th>
                <th style="width:80px;text-align:right;color:var(--green, #7a9e8e);font-weight:700">Prezzo finale</th>
                <th style="width:70px">Min qty</th>
                <th style="width:70px">Max qty</th>
                <th style="width:70px">Min kg</th>
                <th style="width:50px;text-align:center">🚫</th>
            </tr>
        </thead>
        <tbody id="prodBody">
        @foreach($products as $i => $product)
            @php
                $item = $items[$product->id] ?? null;
            @endphp
            <tr class="prod-row" data-name="{{ strtolower($product->name) }}" data-cat="{{ $product->category }}">
                <input type="hidden" name="prod_id[{{ $i }}]" value="{{ $product->id }}">

                <td>
                    <div style="font-weight:600">{{ $product->name }}</div>
                    <div style="font-size:10px;color:#999">{{ $product->origin }} · {{ $product->sku }}</div>
                </td>

                <td style="font-size:11px;color:#999">{{ Str::limit($product->category, 10) }}</td>

                <td>
                    <span style="font-size:10px;padding:2px 6px;border-radius:4px;
                        @if($product->modalita_vendita === 'cassa_kg') background:#e3f0ff;color:#1a56a0
                        @elseif($product->modalita_vendita === 'pezzo') background:#e8f5e9;color:#2d6a4f
                        @elseif($product->modalita_vendita === 'peso_step') background:#fff3e0;color:#e65100
                        @else background:#f3f4f6;color:#666
                        @endif
                    ">{{ $product->modalita_vendita }}</span>
                </td>

                <td style="text-align:right;font-family:'DM Mono',monospace;font-size:13px">
                    € {{ number_format($product->price, 2, ',', '.') }}
                </td>

                <td>
                    <input type="number" step="0.01" name="prezzo_override[{{ $i }}]"
                           value="{{ $item?->prezzo_override }}"
                           placeholder="—" style="width:80px;font-size:12px"
                           oninput="calcFinale(this)">
                </td>

                <td>
                    <input type="number" step="0.1" name="sconto_pct[{{ $i }}]"
                           value="{{ $item?->sconto_pct }}"
                           placeholder="—" style="width:60px;font-size:12px"
                           oninput="calcFinale(this)">
                </td>

                <td style="text-align:right;font-weight:700;font-family:'DM Mono',monospace;font-size:13px"
                    data-base="{{ $product->price }}"
                    data-sconto-globale="{{ $priceList->sconto_default_pct }}"
                    class="finale-cell">
                    —
                </td>

                <td>
                    <input type="number" step="0.001" name="min_qty[{{ $i }}]"
                           value="{{ $item?->min_qty }}"
                           placeholder="—" style="width:60px;font-size:12px">
                </td>

                <td>
                    <input type="number" step="0.001" name="max_qty[{{ $i }}]"
                           value="{{ $item?->max_qty }}"
                           placeholder="—" style="width:60px;font-size:12px">
                </td>

                <td>
                    <input type="number" step="0.1" name="min_qty_kg[{{ $i }}]"
                           value="{{ $item?->min_qty_kg }}"
                           placeholder="—" style="width:60px;font-size:12px">
                </td>

                <td style="text-align:center">
                    <input type="checkbox" name="bloccato[{{ $i }}]" value="1"
                           {{ ($item?->bloccato) ? 'checked' : '' }}
                           style="accent-color:#c0392b"
                           onchange="this.closest('tr').style.opacity = this.checked ? '0.4' : '1'">
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>

<div style="margin-top:20px;display:flex;gap:10px">
    <button type="submit" class="btn btn-primary">💾 Salva Listino</button>
    <a href="{{ route('price-lists.index') }}" class="btn btn-secondary">Annulla</a>
</div>

</form>

<script>
// Calcola prezzo finale per ogni riga
function calcFinale(input) {
    const row   = input.closest('tr');
    const cell  = row.querySelector('.finale-cell');
    const base  = parseFloat(cell.dataset.base) || 0;
    const sGlob = parseFloat(cell.dataset.scontoGlobale) || 0;

    const prezzoOvr = row.querySelector('[name^="prezzo_override"]').value;
    const scontoPct = row.querySelector('[name^="sconto_pct"]').value;

    let finale = base;

    if (prezzoOvr) {
        finale = parseFloat(prezzoOvr);
    } else if (scontoPct) {
        finale = base * (1 - parseFloat(scontoPct) / 100);
    } else if (sGlob > 0) {
        finale = base * (1 - sGlob / 100);
    }

    cell.textContent = '€ ' + finale.toFixed(2).replace('.', ',');

    if (finale < base) {
        cell.style.color = 'var(--green, #7a9e8e)';
    } else {
        cell.style.color = '';
    }
}

// Filtra prodotti
function filterProducts() {
    const q   = document.getElementById('searchProd').value.toLowerCase();
    const cat = document.getElementById('filterCat').value;

    document.querySelectorAll('.prod-row').forEach(row => {
        const match =
            (!q   || row.dataset.name.includes(q)) &&
            (!cat || row.dataset.cat === cat);
        row.style.display = match ? '' : 'none';
    });
}

// Init: calcola tutti i prezzi finali
window.addEventListener('load', function() {
    document.querySelectorAll('.prod-row').forEach(row => {
        const input = row.querySelector('[name^="prezzo_override"]') || row.querySelector('[name^="sconto_pct"]');
        if (input) calcFinale(input);
    });

    // Opacità righe bloccate
    document.querySelectorAll('[name^="bloccato"]').forEach(cb => {
        if (cb.checked) cb.closest('tr').style.opacity = '0.4';
    });
});
</script>

@endsection
