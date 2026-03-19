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

    {{-- COLONNA SINISTRA --}}
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

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Condizioni Pagamento</label>
                    <select name="payment_terms">
                        <option value="bonifico" {{ $client->payment_terms == 'bonifico' ? 'selected' : '' }}>Bonifico bancario</option>
                        <option value="contanti" {{ $client->payment_terms == 'contanti' ? 'selected' : '' }}>Contanti</option>
                        <option value="30gg" {{ $client->payment_terms == '30gg' ? 'selected' : '' }}>Bonifico 30gg</option>
                        <option value="60gg" {{ $client->payment_terms == '60gg' ? 'selected' : '' }}>Bonifico 60gg</option>
                        <option value="riba" {{ $client->payment_terms == 'riba' ? 'selected' : '' }}>RiBa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fido (€)</label>
                    <input type="number" step="0.01" name="fido" value="{{ $client->fido ?? 0 }}"
                           placeholder="0 = nessun limite">
                    <div style="font-size:10px;color:#999;margin-top:3px">0 = nessun limite</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label>Zona Consegna</label>
                    <input type="text" name="zona_consegna" value="{{ $client->zona_consegna }}"
                           placeholder="Es: Centro, Mirafiori, Nord...">
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

            <div class="form-group">
                <label>Note Interne</label>
                <textarea name="note_interne" rows="3"
                          placeholder="Istruzioni consegna, accesso, preferenze...">{{ $client->note_interne }}</textarea>
            </div>
        </div>

    </div>

    {{-- COLONNA DESTRA --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        {{-- LOGISTICA CONSEGNE --}}
        <div class="card">
            <div style="font-weight:700;margin-bottom:16px">🚚 Logistica Consegne</div>

            <div class="form-group">
                <label style="margin-bottom:8px;display:block">Giorni di Consegna</label>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    @foreach($giorni as $i => $giorno)
                    <label style="display:flex;align-items:center;gap:5px;cursor:pointer;
                                  background:{{ in_array($i+1, $giorniConsegna) ? 'var(--green-xl)' : 'var(--bg)' }};
                                  border:1px solid {{ in_array($i+1, $giorniConsegna) ? 'var(--green)' : 'var(--border)' }};
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

            <div class="form-group">
                <label style="margin-bottom:8px;display:block">Giorni di Chiusura</label>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    @foreach($giorni as $i => $giorno)
                    <label style="display:flex;align-items:center;gap:5px;cursor:pointer;
                                  background:{{ in_array($i+1, $giorniChiusura) ? '#fde8e8' : 'var(--bg)' }};
                                  border:1px solid {{ in_array($i+1, $giorniChiusura) ? '#c0392b' : 'var(--border)' }};
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
                <div style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:12px;margin-bottom:12px">
                    <div style="font-size:11px;color:var(--muted);margin-bottom:6px">Link da inviare al cliente:</div>
                    <div style="font-family:monospace;font-size:12px;word-break:break-all;color:var(--green)">
                        {{ url('/order/' . $client->order_token) }}
                    </div>
                </div>
                <button type="button" onclick="copyLink('{{ url('/order/' . $client->order_token) }}')"
                        class="btn btn-secondary" style="width:100%;justify-content:center">
                    📋 Copia Link
                </button>
            @else
                <div style="color:var(--muted);font-size:13px;margin-bottom:12px">
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
    const label    = document.getElementById(labelId);
    const isChiusura = labelId.includes('chiusura');

    if (checkbox.checked) {
        label.style.background = isChiusura ? '#fde8e8' : 'var(--green-xl)';
        label.style.borderColor = isChiusura ? '#c0392b' : 'var(--green)';
    } else {
        label.style.background  = 'var(--bg)';
        label.style.borderColor = 'var(--border)';
    }
}

function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        alert('Link copiato!');
    });
}
</script>

@endsection