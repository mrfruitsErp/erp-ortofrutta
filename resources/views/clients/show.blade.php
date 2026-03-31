@extends('layouts.app')

@section('page-title', 'Cliente — ' . $client->company_name)

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">👤 {{ $client->company_name }}</div>
        <div class="page-sub">
            {{ $client->city ?? '' }}{{ $client->province ? ' (' . $client->province . ')' : '' }}
            @if($client->stato === 'inattivo')
                &nbsp;<span style="font-size:11px;background:#fef3cd;color:#856404;padding:2px 8px;border-radius:4px;font-weight:600">INATTIVO</span>
            @endif
        </div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ url('/clients') }}" class="btn btn-secondary">← Clienti</a>
        <a href="{{ url('/clients/' . $client->id . '/edit') }}" class="btn btn-secondary">✏️ Modifica</a>
    </div>
</div>

{{-- KPI row --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:24px">

    <div class="card" style="padding:16px 20px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Fatturato Totale</div>
        <div style="font-size:22px;font-weight:700;font-family:'DM Mono',monospace;color:var(--dark)">€ {{ number_format($revenue,2,',','.') }}</div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Costo Merce</div>
        <div style="font-size:22px;font-weight:700;font-family:'DM Mono',monospace;color:var(--dark)">€ {{ number_format($cost,2,',','.') }}</div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Margine</div>
        <div style="font-size:22px;font-weight:700;font-family:'DM Mono',monospace;color:var(--green)">€ {{ number_format($margin,2,',','.') }}</div>
        <div style="font-size:12px;color:var(--muted)">{{ number_format($margin_percent,1,',','.') }}% sul fatturato</div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Pagato</div>
        <div style="font-size:22px;font-weight:700;font-family:'DM Mono',monospace;color:var(--dark)">€ {{ number_format($pagato,2,',','.') }}</div>
    </div>

    <div class="card" style="padding:16px 20px;{{ $da_incassare > 0 ? 'border-left:3px solid #e74c3c' : '' }}">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Da Incassare</div>
        <div style="font-size:22px;font-weight:700;font-family:'DM Mono',monospace;color:{{ $da_incassare > 0 ? '#e74c3c' : 'var(--muted)' }}">
            € {{ number_format($da_incassare,2,',','.') }}
        </div>
        <div style="font-size:12px;color:var(--muted)">{{ $documents->count() }} document{{ $documents->count() === 1 ? 'o' : 'i' }}</div>
    </div>

</div>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start">

    {{-- COLONNA SX: Anagrafica + Commerciale --}}
    <div style="display:flex;flex-direction:column;gap:16px">

        {{-- Dati Anagrafici --}}
        <div class="card">
            <div style="font-weight:700;font-size:14px;color:var(--dark);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border)">
                📋 Dati Anagrafici
            </div>
            <div style="display:flex;flex-direction:column;gap:0">
                @foreach([
                    ['P.IVA',        $client->vat_number],
                    ['Cod. Fiscale',  $client->fiscal_code],
                    ['Indirizzo',     $client->address],
                    ['Città',         trim(($client->city ?? '') . ($client->zip ? ' ' . $client->zip : '') . ($client->province ? ' (' . $client->province . ')' : ''))],
                    ['Telefono',      $client->phone],
                    ['Cellulare',     $client->cellulare_referente],
                    ['Email',         $client->email],
                    ['Referente',     $client->referente],
                ] as [$label, $value])
                @if($value)
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:7px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--muted);font-weight:600;flex-shrink:0;margin-right:8px">{{ $label }}</span>
                    <span style="color:var(--dark);font-weight:500;text-align:right;word-break:break-word">{{ $value }}</span>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Dati Commerciali --}}
        <div class="card">
            <div style="font-weight:700;font-size:14px;color:var(--dark);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border)">
                💼 Dati Commerciali
            </div>
            <div style="display:flex;flex-direction:column;gap:0">
                @foreach([
                    ['Listino',         $client->priceList->name ?? null],
                    ['Metodo Pagam.',   $client->paymentMethod->name ?? null],
                    ['Termini Pagam.',  $client->payment_terms],
                    ['Fido €',          $client->fido ? '€ ' . number_format($client->fido, 2, ',', '.') : null],
                    ['IBAN',            $client->iban],
                    ['Banca',           $client->banca],
                    ['Zona Consegna',   $client->zona_consegna],
                    ['Modalità Ordine', $client->modalita_ordine],
                ] as [$label, $value])
                @if($value)
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:7px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--muted);font-weight:600;flex-shrink:0;margin-right:8px">{{ $label }}</span>
                    <span style="color:var(--dark);font-weight:500;text-align:right">{{ $value }}</span>
                </div>
                @endif
                @endforeach
                @if($client->note_interne)
                <div style="font-size:13px;padding:8px 0">
                    <div style="color:var(--muted);font-weight:600;margin-bottom:4px">Note interne</div>
                    <div style="color:var(--dark);font-style:italic;font-size:12px;line-height:1.5">{{ $client->note_interne }}</div>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- COLONNA DX: Documenti --}}
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <span style="font-weight:700;font-size:14px;color:var(--dark)">📄 Documenti</span>
            <a href="{{ url('/documents/create?client_id=' . $client->id) }}" class="btn btn-primary" style="padding:5px 14px;font-size:12px">+ Nuovo</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Numero</th>
                    <th>Data</th>
                    <th style="text-align:right">Totale</th>
                    <th style="text-align:center">Pagato</th>
                    <th style="text-align:center">Azioni</th>
                </tr>
            </thead>
            <tbody>
            @forelse($documents as $doc)
            @php
                $docPagato    = $doc->payments->sum('amount') ?? 0;
                $docResiduo   = $doc->total - $docPagato;
                $isPagato     = $docResiduo <= 0.01;
            @endphp
            <tr style="cursor:pointer" onclick="window.location='{{ url('/documents/' . $doc->id) }}'">
                <td style="font-family:'DM Mono',monospace;font-weight:600;color:var(--green)">
                    {{ $doc->number ?? '<span style="color:#e74c3c;font-size:11px">Bozza</span>' }}
                </td>
                <td style="color:var(--muted)">{{ \Carbon\Carbon::parse($doc->date)->format('d/m/Y') }}</td>
                <td style="text-align:right;font-family:'DM Mono',monospace;font-weight:700">
                    € {{ number_format($doc->total,2,',','.') }}
                </td>
                <td style="text-align:center">
                    @if($isPagato)
                        <span style="font-size:11px;background:#d4edda;color:#155724;padding:2px 8px;border-radius:20px;font-weight:600">✓ Pagato</span>
                    @else
                        <span style="font-size:11px;background:#fde8e8;color:#8b0000;padding:2px 8px;border-radius:20px;font-weight:600">€ {{ number_format($docResiduo,2,',','.') }}</span>
                    @endif
                </td>
                <td style="text-align:center" onclick="event.stopPropagation()">
                    <div style="display:flex;gap:4px;justify-content:center">
                        <a href="{{ url('/documents/' . $doc->id) }}" class="btn btn-secondary" style="padding:4px 8px;font-size:11px">Apri</a>
                        <a href="{{ route('documents.pdf', $doc->id) }}" class="btn btn-secondary" style="padding:4px 8px;font-size:11px" target="_blank">PDF</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:32px;color:var(--muted)">
                    Nessun documento per questo cliente.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection