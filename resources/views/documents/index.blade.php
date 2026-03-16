@extends('layouts.app')

@section('page-title', 'Documenti')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Documenti</div>
        <div class="page-sub">DDT, fatture e ordini emessi</div>
    </div>
    <a href="{{ url('/documents/create') }}" class="btn btn-primary">＋ Nuovo Documento</a>
</div>

{{-- STATS BAR --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">

    <div class="card" style="padding:16px 20px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Totale Documenti</div>
        <div style="font-size:26px;font-weight:700;color:var(--dark)">{{ $documents->count() }}</div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Fatturato Totale</div>
        <div style="font-size:26px;font-weight:700;color:var(--dark)">€ {{ number_format($documents->sum('total'),2,',','.') }}</div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Questo Mese</div>
        <div style="font-size:26px;font-weight:700;color:var(--dark)">
            {{ $documents->where('date','>=',now()->startOfMonth()->toDateString())->count() }}
        </div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">DDT Emessi</div>
        <div style="font-size:26px;font-weight:700;color:var(--dark)">
            {{ $documents->where('type','DDT')->count() }}
        </div>
    </div>

</div>

{{-- TABELLA --}}
<div class="card" style="padding:0;overflow:hidden">

    {{-- FILTRO RAPIDO --}}
    <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;gap:10px;align-items:center">
        <input type="text" id="searchInput" placeholder="🔍 Cerca per numero, cliente, tipo..." style="max-width:320px;margin:0">
        <select id="filterType" style="max-width:160px;margin:0">
            <option value="">Tutti i tipi</option>
            <option value="DDT">DDT</option>
            <option value="Fattura">Fattura</option>
            <option value="Ordine">Ordine</option>
        </select>
    </div>

    <table id="docTable">
        <thead>
            <tr>
                <th style="width:60px">#</th>
                <th>Numero</th>
                <th>Tipo</th>
                <th>Cliente</th>
                <th>Data</th>
                <th style="text-align:right">Totale</th>
                <th style="width:130px;text-align:center">Azioni</th>
            </tr>
        </thead>

        <tbody>

        @forelse($documents as $document)

            <tr class="doc-row"
                data-numero="{{ strtolower($document->number) }}"
                data-cliente="{{ strtolower($document->client->company_name ?? '') }}"
                data-tipo="{{ $document->type }}">

                <td style="color:var(--muted);font-size:12px">
                    {{ $document->id }}
                </td>

                <td>
                    <a href="{{ url('/documents/'.$document->id) }}"
                       style="font-weight:600;color:var(--green);text-decoration:none;font-family:'DM Mono',monospace;font-size:13px">
                        {{ $document->number }}
                    </a>
                </td>

                <td>
                    <span style="
                        display:inline-block;
                        padding:3px 10px;
                        border-radius:20px;
                        font-size:11px;
                        font-weight:600;
                        background:{{ $document->type == 'DDT' ? 'var(--green-xl)' : '#fff3e0' }};
                        color:{{ $document->type == 'DDT' ? 'var(--green)' : '#e65100' }}
                    ">
                        {{ $document->type }}
                    </span>
                </td>

                <td style="font-weight:500">
                    {{ $document->client->company_name ?? '—' }}
                </td>

                <td style="color:var(--muted);font-size:13px">
                    {{ \Carbon\Carbon::parse($document->date)->format('d/m/Y') }}
                </td>

                <td style="text-align:right;font-weight:700;font-family:'DM Mono',monospace">
                    € {{ number_format($document->total,2,',','.') }}
                </td>

                <td style="text-align:center">
                    <div style="display:flex;gap:6px;justify-content:center">
                        <a href="{{ url('/documents/'.$document->id) }}"
                           class="btn btn-secondary"
                           style="padding:5px 12px;font-size:12px">
                           👁 Apri
                        </a>

                        <a href="{{ url('/documents/'.$document->id.'/pdf') }}"
                           class="btn btn-secondary"
                           style="padding:5px 12px;font-size:12px"
                           target="_blank">
                           🖨 PDF
                        </a>
                    </div>
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">
                    Nessun documento trovato.
                </td>
            </tr>

        @endforelse

        </tbody>
    </table>

</div>

<script>

const search = document.getElementById('searchInput')
const filterType = document.getElementById('filterType')

function filterRows(){

    const q = search.value.toLowerCase()
    const t = filterType.value

    document.querySelectorAll('.doc-row').forEach(row => {

        const matchQ =
            !q ||
            row.dataset.numero.includes(q) ||
            row.dataset.cliente.includes(q)

        const matchT =
            !t ||
            row.dataset.tipo === t

        row.style.display =
            (matchQ && matchT) ? '' : 'none'

    })

}

search.addEventListener('input', filterRows)
filterType.addEventListener('change', filterRows)

</script>

@endsection