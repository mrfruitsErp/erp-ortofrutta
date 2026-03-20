@extends('layouts.app')

@section('page-title', 'Modifica Cliente')

@section('content')

@php
    $giorni = ['Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato','Domenica'];
    $giorniConsegna = $client->giorni_consegna ?? [];
    $giorniChiusura = $client->giorni_chiusura ?? [];
@endphp

<div class="page-header">
    <div>
        <div class="page-title">✏️ Modifica Cliente</div>
        <div class="page-sub">{{ $client->company_name }}</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('clients.show', $client->id) }}" class="btn btn-secondary">← Torna al cliente</a>
    </div>
</div>

<form method="POST" action="{{ route('clients.update', $client->id) }}">
@csrf
@method('PUT')

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    {{-- ============================================ --}}
    {{-- COLONNA SINISTRA --}}
    {{-- ============================================ --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        {{-- DATI ANAGRAFICI --}}
        <div class="card">
            <div style="font-weight:700;margin-bottom:16px">📋 Dati Anagrafici</div>

            <div class="form-group">
                <label>Ragione Sociale *</label>
                <input type="text" name="company_name" value="{{ $client->company_name }}" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>P.IVA</label>
                    <input type="text" name="vat_number" value="{{ $client->vat_number }}">
                </div>
                <div class="form-group">
                    <label>Codice Fiscale</label>
                    <input type="text" name="fiscal_code" value="{{ $client->fiscal_code }}">
                </div>
            </div>

            <div class="form-group">
                <label>Indirizzo</label>
                <input type="text" name="address" value="{{ $client->address }}">
            </div>

            <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Città</label>
                    <input type="text" name="city" value="{{ $client->city }}">
                </div>
                <div class="form-group">
                    <label>CAP</label>
                    <input type="text" name="zip" value="{{ $client->zip }}">
                </div>
                <div class="form-group">
                    <label>Prov.</label>
                    <input type="text" name="province" value="{{ $client->province }}" maxlength="2">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ $client->email }}">
                </div>
                <div class="form-group">
                    <label>Telefono</label>
                    <input type="text" name="phone" value="{{ $client->phone }}">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Referente</label>
                    <input type="text" name="referente" value="{{ $client->referente }}" placeholder="Nome cognome">
                </div>
                <div class="form-group">
                    <label>Cellulare referente</label>
                    <input type="text" name="cellulare_referente" value="{{ $client->cellulare_referente }}">
                </div>
            </div>
        </div>

        {{-- DATI COMMERCIALI --}}
        <div class="card">
            <div style="font-weight:700;margin-bottom:16px">💶 Dati Commerciali</div>

            {{-- LISTINO + PAGAMENTO --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Listino Prezzi</label>
                    <select name="price_list_id" id="price_list_id">
                        <option value="">— Nessun listino —</option>
                        @foreach($priceLists as $pl)
                            <option value="{{ $pl->id }}"
                                data-puo-kg="{{ $pl->puo_ordinare_kg ? '1' : '0' }}"
                                data-payment="{{ $pl->payment_method_id }}"
                                {{ ($client->price_list_id ?? '') == $pl->id ? 'selected' : '' }}>
                                {{ $pl->nome }}
                            </option>
                        @endforeach
                    </select>
                    <div style="font-size:10px;color:#999;margin-top:3px" id="listino_desc">
                        @if($client->priceList)
                            {{ $client->priceList->descrizione }}
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label>Metodo di Pagamento</label>
                    <select name="payment_method_id">
                        <option value="">— Usa default listino —</option>
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm->id }}" {{ ($client->payment_method_id ?? '') == $pm->id ? 'selected' : '' }}>
                                {{ $pm->nome }}
                            </option>
                        @endforeach
                    </select>
                    <div style="font-size:10px;color:#999;margin-top:3px">
                        Se vuoto, usa il pagamento di default del listino
                    </div>
                </div>
            </div>

            {{-- FIDO + STATO --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Fido (€)</label>
                    <input type="number" step="0.01" name="fido" value="{{ $client->fido ?? 0 }}"
                           placeholder="0 = nessun limite">
                    <div style="font-size:10px;color:#999;margin-top:3px">0 = nessun limite</div>
                </div>
                <div class="form-group">
                    <label>Stato Cliente</label>
                    <select name="stato">
                        <option value="attivo"     {{ ($client->stato ?? 'attivo') == 'attivo'     ? 'selected' : '' }}>✓ Attivo</option>
                        <option value="sospeso"    {{ ($client->stato ?? 'attivo') == 'sospeso'    ? 'selected' : '' }}>⏸ Sospeso</option>
                        <option value="potenziale" {{ ($client->stato ?? 'attivo') == 'potenziale' ? 'selected' : '' }}>◎ Potenziale</option>
                    </select>
                </div>
            </div>

            {{-- IBAN + BANCA (per Ri.Ba. / SDD) --}}
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px">
                <div class="form-group">
                    <label>IBAN</label>
                    <input type="text" name="iban" value="{{ $client->iban }}"
                           placeholder="IT60X0542811101000000123456" maxlength="34"
                           style="font-family:monospace;letter-spacing:1px">
                    <div style="font-size:10px;color:#999;margin-top:3px">Per Ri.Ba. e addebiti diretti SDD</div>
                </div>
                <div class="form-group">
                    <label>Banca</label>
                    <input type="text" name="banca" value="{{ $client->banca }}"
                           placeholder="Es. Intesa Sanpaolo">
                </div>
            </div>

            {{-- LEGACY: payment_terms nascosto per compatibilità --}}
            <input type="hidden" name="payment_terms" value="{{ $client->payment_terms }}">

            <div class="form-group">
                <label>Note Interne</label>
                <textarea name="note_interne" rows="3"
                          placeholder="Istruzioni consegna, accesso, preferenze...">{{ $client->note_interne }}</textarea>
            </div>
        </div>

    </div>

    {{-- ============================================ --}}
    {{-- COLONNA DESTRA --}}
    {{-- ============================================ --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        {{-- REGOLE ORDINE --}}
        <div class="card">
            <div style="font-weight:700;margin-bottom:16px">🛒 Regole Ordine</div>

            <div class="form-group">
                <label>Può ordinare a kg? (prodotti venduti a cassa)</label>
                <select name="puo_ordinare_kg_select" id="puo_ordinare_kg_select">
                    <option value="null" {{ $client->puo_ordinare_kg === null ? 'selected' : '' }}>
                        📋 Usa regola del listino
                        @if($client->priceList)
                            ({{ $client->priceList->puo_ordinare_kg ? 'Sì' : 'No' }})
                        @endif
                    </option>
                    <option value="1" {{ $client->puo_ordinare_kg === true ? 'selected' : '' }}>✅ Sì — può ordinare a kg</option>
                    <option value="0" {{ $client->puo_ordinare_kg === false ? 'selected' : '' }}>❌ No — solo casse intere</option>
                </select>
                <div style="font-size:10px;color:#999;margin-top:3px">
                    Override sulla regola del listino. "Usa regola del listino" = segue l'impostazione del listino assegnato.
                </div>
            </div>

            <div class="form-group">
                <label>Orario limite invio ordini</label>
                <input type="time" name="orario_limite_ordine"
                       value="{{ $client->orario_limite_ordine }}"
                       placeholder="21:00">
                <div style="font-size:10px;color:#999;margin-top:3px">
                    Lascia vuoto = usa il default globale ({{ \DB::table('settings')->where('key', 'orario_limite_ordine_default')->value('value') ?? '21:00' }}).
                    Il cliente deve inviare l'ordine entro quest'ora per la consegna del giorno dopo.
                </div>
            </div>

            <div class="form-group">
                <label>Zona Consegna</label>
                <input type="text" name="zona_consegna" value="{{ $client->zona_consegna }}"
                       placeholder="Es: Centro, Mirafiori, Nord...">
            </div>

            {{-- LEGACY: modalita_ordine nascosto --}}
            <input type="hidden" name="modalita_ordine" value="{{ $client->modalita_ordine ?? 'colli' }}">
        </div>

        {{-- FASCE ORARIE CONSEGNA --}}
        <div class="card">
            <div style="font-weight:700;margin-bottom:16px">🚚 Fasce Consegna</div>

            <div class="form-group">
                <label style="margin-bottom:8px;display:block">Fasce orarie accettate dal cliente</label>
                <div style="display:flex;flex-direction:column;gap:8px">
                    @foreach($deliverySlots as $slot)
                        @php
                            $isSelected = in_array($slot->id, $clientSlotIds ?? []);
                            $isPref = ($clientPrefSlotId ?? null) == $slot->id;
                        @endphp
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;
                                      background:{{ $isSelected ? 'var(--green-xl, #f0faf4)' : 'var(--bg, #f8f9fa)' }};
                                      border:1px solid {{ $isSelected ? 'var(--green, #7a9e8e)' : 'var(--border, #ddd)' }};
                                      padding:10px 14px;border-radius:8px;font-size:13px;transition:all .2s"
                               id="slot-label-{{ $slot->id }}">
                            <input type="checkbox" name="delivery_slots[]" value="{{ $slot->id }}"
                                   {{ $isSelected ? 'checked' : '' }}
                                   onchange="updateSlotStyle({{ $slot->id }})"
                                   style="accent-color:var(--green, #7a9e8e)">
                            <div style="flex:1">
                                <strong>{{ $slot->nome }}</strong>
                                <span style="color:#999;margin-left:6px">{{ $slot->orario_inizio }} – {{ $slot->orario_fine }}</span>
                            </div>
                            <label style="display:flex;align-items:center;gap:4px;font-size:11px;color:#999;cursor:pointer">
                                <input type="radio" name="delivery_slot_preferito" value="{{ $slot->id }}"
                                       {{ $isPref ? 'checked' : '' }}
                                       style="accent-color:#f59e0b">
                                ⭐ Preferita
                            </label>
                        </label>
                    @endforeach
                </div>
                <div style="font-size:10px;color:#999;margin-top:6px">
                    Seleziona le fasce in cui il cliente accetta consegne. La stella indica la fascia preferita.
                </div>
            </div>

            <div style="border-top:1px solid var(--border, #ddd);margin:16px 0"></div>

            {{-- GIORNI CONSEGNA --}}
            <div class="form-group">
                <label style="margin-bottom:8px;display:block">Giorni di Consegna</label>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    @foreach($giorni as $i => $giorno)
                    <label style="display:flex;align-items:center;gap:5px;cursor:pointer;
                                  background:{{ in_array($i+1, $giorniConsegna) ? 'var(--green-xl, #f0faf4)' : 'var(--bg, #f8f9fa)' }};
                                  border:1px solid {{ in_array($i+1, $giorniConsegna) ? 'var(--green, #7a9e8e)' : 'var(--border, #ddd)' }};
                                  padding:5px 12px;border-radius:20px;font-size:13px"
                           id="label-consegna-{{ $i+1 }}">
                        <input type="checkbox" name="giorni_consegna[]" value="{{ $i+1 }}"
                               {{ in_array($i+1, $giorniConsegna) ? 'checked' : '' }}
                               onchange="updateCheckStyle(this, 'label-consegna-{{ $i+1 }}')"
                               style="display:none">
                        {{ substr($giorno, 0, 3) }}
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- GIORNI CHIUSURA --}}
            <div class="form-group">
                <label style="margin-bottom:8px;display:block">Giorni di Chiusura</label>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    @foreach($giorni as $i => $giorno)
                    <label style="display:flex;align-items:center;gap:5px;cursor:pointer;
                                  background:{{ in_array($i+1, $giorniChiusura) ? '#fde8e8' : 'var(--bg, #f8f9fa)' }};
                                  border:1px solid {{ in_array($i+1, $giorniChiusura) ? '#c0392b' : 'var(--border, #ddd)' }};
                                  padding:5px 12px;border-radius:20px;font-size:13px"
                           id="label-chiusura-{{ $i+1 }}">
                        <input type="checkbox" name="giorni_chiusura[]" value="{{ $i+1 }}"
                               {{ in_array($i+1, $giorniChiusura) ? 'checked' : '' }}
                               onchange="updateCheckStyle(this, 'label-chiusura-{{ $i+1 }}')"
                               style="display:none">
                        {{ substr($giorno, 0, 3) }}
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- ORARI CONSEGNA LEGACY (mantenuti per compatibilità) --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Orario inizio consegna</label>
                    <input type="time" name="fascia_oraria_inizio"
                           value="{{ $client->fascia_oraria_inizio }}">
                </div>
                <div class="form-group">
                    <label>Orario fine consegna</label>
                    <input type="time" name="fascia_oraria_fine"
                           value="{{ $client->fascia_oraria_fine }}">
                </div>
            </div>
        </div>

        {{-- LINK ORDINI CLIENTE --}}
        <div class="card">
            <div style="font-weight:700;margin-bottom:16px">🔗 Link Ordini Cliente</div>

            @if($client->order_token)
                <div style="background:var(--bg, #f8f9fa);border:1px solid var(--border, #ddd);border-radius:8px;padding:12px;margin-bottom:12px">
                    <div style="font-size:11px;color:var(--muted, #999);margin-bottom:6px">Link da inviare al cliente:</div>
                    <div style="font-family:monospace;font-size:12px;word-break:break-all;color:var(--green, #7a9e8e)">
                        {{ url('/order/' . $client->order_token) }}
                    </div>
                </div>
                <button type="button" onclick="copyLink('{{ url('/order/' . $client->order_token) }}')"
                        class="btn btn-secondary" style="width:100%;justify-content:center">
                    📋 Copia Link
                </button>
            @else
                <div style="color:var(--muted, #999);font-size:13px;margin-bottom:12px">
                    Nessun link generato. Salva il cliente per generarlo automaticamente.
                </div>
            @endif
        </div>

    </div>

</div>

<div style="margin-top:20px;display:flex;gap:10px">
    <button type="submit" class="btn btn-primary">💾 Salva</button>
    <a href="{{ route('clients.show', $client->id) }}" class="btn btn-secondary">Annulla</a>
</div>

</form>

<script>
function updateCheckStyle(checkbox, labelId) {
    const label      = document.getElementById(labelId);
    const isChiusura = labelId.includes('chiusura');

    if (checkbox.checked) {
        label.style.background  = isChiusura ? '#fde8e8' : 'var(--green-xl, #f0faf4)';
        label.style.borderColor = isChiusura ? '#c0392b' : 'var(--green, #7a9e8e)';
    } else {
        label.style.background  = 'var(--bg, #f8f9fa)';
        label.style.borderColor = 'var(--border, #ddd)';
    }
}

function updateSlotStyle(slotId) {
    const label    = document.getElementById('slot-label-' + slotId);
    const checkbox = label.querySelector('input[type="checkbox"]');

    if (checkbox.checked) {
        label.style.background  = 'var(--green-xl, #f0faf4)';
        label.style.borderColor = 'var(--green, #7a9e8e)';
    } else {
        label.style.background  = 'var(--bg, #f8f9fa)';
        label.style.borderColor = 'var(--border, #ddd)';
    }
}

function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        alert('Link copiato!');
    });
}

// Aggiorna descrizione listino quando cambia
document.getElementById('price_list_id')?.addEventListener('change', function() {
    const opt  = this.options[this.selectedIndex];
    const desc = document.getElementById('listino_desc');

    // Aggiorna hint "può ordinare kg" nel dropdown regole
    const kgSelect = document.getElementById('puo_ordinare_kg_select');
    if (kgSelect) {
        const puoKg  = opt.dataset.puoKg === '1';
        const firstOpt = kgSelect.options[0];
        if (this.value) {
            firstOpt.textContent = '📋 Usa regola del listino (' + (puoKg ? 'Sì' : 'No') + ')';
        } else {
            firstOpt.textContent = '📋 Usa regola del listino';
        }
    }
});
</script>

@endsection