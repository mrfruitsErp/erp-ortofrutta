@extends('layouts.app')

@section('page-title','Nuovo Prodotto')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">➕ Nuovo Prodotto</div>
        <div class="page-sub">Aggiungi un prodotto al catalogo</div>
    </div>
    <a href="{{ url('/products') }}" class="btn btn-secondary">← Torna ai prodotti</a>
</div>

<form method="POST" action="{{ url('/products') }}">
@csrf

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

{{-- ================= SINISTRA ================= --}}
<div class="card">

<div style="font-weight:700;margin-bottom:16px">Dati Prodotto</div>

<div class="form-group">
    <label>Nome Prodotto *</label>
    <input type="text" name="name" required>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
    <div class="form-group">
        <label>Categoria *</label>
        <select name="category" required>
            <option value="">Seleziona</option>
            @foreach(['Frutta','Verdura','Erbe Aromatiche','Funghi','Frutta Secca','Legumi Secchi','Insalata 4a Gamma'] as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Codice SKU</label>
        <input type="text" value="Auto generato" disabled>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
    <div class="form-group">
        <label>Origine</label>
        <input type="text" name="origin">
    </div>
    <div class="form-group">
        <label>Aliquota IVA</label>
        <select name="vat_rate">
            <option value="4">4%</option>
            <option value="10">10%</option>
            <option value="22">22%</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label>Tipo Vendita</label>
    <select name="sale_type" id="sale_type" onchange="toggleSale()">
        <option value="kg">A Kg</option>
        <option value="unit">A Pezzi</option>
    </select>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
    <div class="form-group">
        <label>Prezzo Vendita</label>
        <input type="number" step="0.01" name="price">
    </div>
    <div class="form-group">
        <label>Prezzo Costo</label>
        <input type="number" step="0.01" name="cost_price">
    </div>
</div>

<div id="kg-box">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="form-group">
            <label>Peso Medio Cassa (kg)</label>
            <input type="number" step="0.001" name="avg_box_weight">
        </div>
        <div class="form-group">
            <label>Tara (kg)</label>
            <input type="number" step="0.001" name="tara" value="0">
        </div>
    </div>
</div>

<div id="unit-box" style="display:none">
    <div class="form-group">
        <label>Pezzi per Cassa</label>
        <input type="number" name="pieces_per_box">
    </div>
</div>

</div>

{{-- ================= DESTRA ================= --}}
<div style="display:flex;flex-direction:column;gap:20px">

<div class="card">
<div style="font-weight:700;margin-bottom:16px">Stock</div>

<div class="form-group">
    <label>Quantità iniziale</label>
    <input type="number" step="0.001" name="new_stock_qty">
</div>

<div class="form-group">
    <label>Scorta minima</label>
    <input type="number" step="0.001" name="min_stock" value="0">
</div>

<input type="hidden" name="unit" id="unit_hidden" value="kg">

</div>

<div class="card">
<div style="font-weight:700;margin-bottom:16px">🛒 Vendita Cliente</div>

<div class="form-group">
    <label>Disponibilità</label>
    <select name="disponibilita">
        <option value="disponibile">Disponibile</option>
        <option value="su_richiesta">Su richiesta</option>
        <option value="non_disponibile">Non disponibile</option>
    </select>
</div>

<div class="form-group">
    <label>Modalità ordine</label>
    <select name="ordine_step">
        <option value="colli">Colli</option>
        <option value="kg">Kg</option>
        <option value="grammi">Grammi</option>
        <option value="pezzi_interi">Pezzi</option>
    </select>
</div>

<div class="form-group">
    <label>Minimo ordinabile</label>
    <input type="number" step="0.001" name="ordine_min" value="1">
</div>

</div>

</div>

</div>

<div style="margin-top:20px">
    <button type="submit" class="btn btn-primary">💾 Salva</button>
</div>

</form>

<script>
function toggleSale(){
    const type = document.getElementById('sale_type').value;

    document.getElementById('kg-box').style.display   = type === 'kg' ? '' : 'none';
    document.getElementById('unit-box').style.display = type === 'unit' ? '' : 'none';

    document.getElementById('unit_hidden').value = type === 'unit' ? 'pz' : 'kg';
}
</script>

@endsection