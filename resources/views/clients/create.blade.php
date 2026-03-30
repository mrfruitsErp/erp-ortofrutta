@extends('layouts.app')
@section('page-title', 'Nuovo Cliente')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">👤 Nuovo Cliente</div>
        <div class="page-sub">Inserisci i dati del nuovo cliente</div>
    </div>
    <a href="{{ url('/clients') }}" class="btn btn-secondary">← Torna ai clienti</a>
</div>

<form method="POST" action="{{ route('clients.store') }}">
@csrf

{{-- ANAGRAFICA --}}
<div class="card" style="padding:20px;margin-bottom:16px">
    <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:14px">Anagrafica</div>

    <div class="form-group">
        <label>Ragione Sociale *</label>
        <input type="text" name="company_name" required placeholder="Es. Frutta Rossi Srl" value="{{ old('company_name') }}">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="form-group">
            <label>Partita IVA</label>
            <input type="text" name="vat_number" placeholder="Es. 01234567890" value="{{ old('vat_number') }}">
        </div>
        <div class="form-group">
            <label>Codice Fiscale</label>
            <input type="text" name="fiscal_code" value="{{ old('fiscal_code') }}">
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:16px">
        <div class="form-group">
            <label>Indirizzo</label>
            <input type="text" name="address" value="{{ old('address') }}">
        </div>
        <div class="form-group">
            <label>Città</label>
            <input type="text" name="city" value="{{ old('city') }}">
        </div>
        <div class="form-group">
            <label>CAP</label>
            <input type="text" name="zip" value="{{ old('zip') }}">
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
        <div class="form-group">
            <label>Provincia</label>
            <input type="text" name="province" maxlength="2" placeholder="TO" value="{{ old('province') }}">
        </div>
        <div class="form-group">
            <label>Telefono</label>
            <input type="text" name="phone" value="{{ old('phone') }}">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="form-group">
            <label>Referente</label>
            <input type="text" name="referente" value="{{ old('referente') }}">
        </div>
        <div class="form-group">
            <label>Cellulare Referente</label>
            <input type="text" name="cellulare_referente" value="{{ old('cellulare_referente') }}">
        </div>
    </div>
</div>

{{-- COMMERCIALE --}}
<div class="card" style="padding:20px;margin-bottom:16px">
    <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:14px">Commerciale</div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="form-group">
            <label>Listino Prezzi</label>
            <select name="price_list_id">
                <option value="">— Seleziona —</option>
                @foreach($priceLists as $pl)
                    <option value="{{ $pl->id }}" {{ old('price_list_id') == $pl->id ? 'selected' : '' }}>
                        {{ $pl->nome }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Metodo Pagamento</label>
            <select name="payment_method_id">
                <option value="">— Seleziona —</option>
                @foreach($paymentMethods as $pm)
                    <option value="{{ $pm->id }}" {{ old('payment_method_id') == $pm->id ? 'selected' : '' }}>
                        {{ $pm->nome }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
        <div class="form-group">
            <label>Fido €</label>
            <input type="number" step="0.01" name="fido" value="{{ old('fido', 0) }}">
        </div>
        <div class="form-group">
            <label>IBAN</label>
            <input type="text" name="iban" value="{{ old('iban') }}">
        </div>
        <div class="form-group">
            <label>Banca</label>
            <input type="text" name="banca" value="{{ old('banca') }}">
        </div>
    </div>
</div>

{{-- ORDINI --}}
<div class="card" style="padding:20px;margin-bottom:16px">
    <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:14px">Ordini</div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
        <div class="form-group">
            <label>Modalità Ordine</label>
            <select name="modalita_ordine">
                <option value="colli" {{ old('modalita_ordine') == 'colli' ? 'selected' : '' }}>A colli</option>
                <option value="kg" {{ old('modalita_ordine') == 'kg' ? 'selected' : '' }}>A kg</option>
                <option value="misto" {{ old('modalita_ordine') == 'misto' ? 'selected' : '' }}>Misto</option>
            </select>
        </div>
        <div class="form-group">
            <label>Orario Limite Ordine</label>
            <input type="time" name="orario_limite_ordine" value="{{ old('orario_limite_ordine', '18:00') }}">
        </div>
        <div class="form-group">
            <label>Può ordinare a kg</label>
            <select name="puo_ordinare_kg_select">
                <option value="0">No</option>
                <option value="1" {{ old('puo_ordinare_kg_select') == '1' ? 'selected' : '' }}>Sì</option>
            </select>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div class="form-group">
            <label>Zona Consegna</label>
            <input type="text" name="zona_consegna" value="{{ old('zona_consegna') }}">
        </div>
        <div class="form-group">
            <label>Stato</label>
            <select name="stato">
                <option value="attivo" selected>Attivo</option>
                <option value="inattivo">Inattivo</option>
                <option value="sospeso">Sospeso</option>
            </select>
        </div>
    </div>

    @if($deliverySlots->count())
    <div class="form-group">
        <label>Fasce Orarie Consegna</label>
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px">
            @foreach($deliverySlots as $slot)
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:400;cursor:pointer">
                <input type="checkbox" name="delivery_slots[]" value="{{ $slot->id }}">
                {{ $slot->label ?? $slot->nome ?? $slot->id }}
            </label>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- NOTE --}}
<div class="card" style="padding:20px;margin-bottom:16px">
    <div class="form-group" style="margin:0">
        <label>Note Interne</label>
        <textarea name="note_interne" rows="3" placeholder="Note visibili solo internamente...">{{ old('note_interne') }}</textarea>
    </div>
</div>

<div style="display:flex;gap:10px">
    <button type="submit" class="btn btn-primary">💾 Salva Cliente</button>
    <a href="{{ url('/clients') }}" class="btn btn-secondary">Annulla</a>
</div>

</form>
@endsection