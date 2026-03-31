@extends('layouts.app')

@section('page-title', 'Fornitore — ' . $supplier->company_name)

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">🏭 {{ $supplier->company_name }}</div>
        <div class="page-sub">{{ $supplier->city ?? 'Fornitore' }}</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ url('/suppliers') }}" class="btn btn-secondary">← Fornitori</a>
        <a href="{{ url('/suppliers/' . $supplier->id . '/edit') }}" class="btn btn-secondary">✏️ Modifica</a>
    </div>
</div>

{{-- KPI --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">

    <div class="card" style="padding:16px 20px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Acquistato Totale</div>
        <div style="font-size:22px;font-weight:700;font-family:'DM Mono',monospace;color:var(--dark)">
            € {{ number_format($totale_acquistato, 2, ',', '.') }}
        </div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">N. Acquisti</div>
        <div style="font-size:22px;font-weight:700;font-family:'DM Mono',monospace;color:var(--dark)">
            {{ $purchases->count() }}
        </div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Prodotti Acquistati</div>
        <div style="font-size:22px;font-weight:700;font-family:'DM Mono',monospace;color:var(--dark)">
            {{ $purchases->pluck('product_id')->unique()->count() }}
        </div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Ultimo Acquisto</div>
        <div style="font-size:18px;font-weight:700;font-family:'DM Mono',monospace;color:var(--dark)">
            @if($purchases->first())
                {{ \Carbon\Carbon::parse($purchases->first()->date)->format('d/m/Y') }}
            @else
                —
            @endif
        </div>
    </div>

</div>

<div style="display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start">

    {{-- Dati Anagrafici --}}
    <div class="card">
        <div style="font-weight:700;font-size:14px;color:var(--dark);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border)">
            📋 Dati Anagrafici
        </div>
        <div style="display:flex;flex-direction:column;gap:0">
            @foreach([
                ['P.IVA',     $supplier->vat_number],
                ['Telefono',  $supplier->phone],
                ['Email',     $supplier->email],
                ['Città',     $supplier->city],
                ['Indirizzo', $supplier->address ?? null],
            ] as [$label, $value])
            @if($value)
            <div style="display:flex;justify-content:space-between;font-size:13px;padding:7px 0;border-bottom:1px solid var(--border)">
                <span style="color:var(--muted);font-weight:600;flex-shrink:0;margin-right:8px">{{ $label }}</span>
                <span style="color:var(--dark);font-weight:500;text-align:right;word-break:break-word">{{ $value }}</span>
            </div>
            @endif
            @endforeach
        </div>

        @if($supplier->note ?? null)
        <div style="margin-top:12px;font-size:13px">
            <div style="color:var(--muted);font-weight:600;margin-bottom:4px">Note</div>
            <div style="color:var(--dark);font-style:italic;line-height:1.5">{{ $supplier->note }}</div>
        </div>
        @endif
    </div>

    {{-- Storico Acquisti --}}
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <span style="font-weight:700;font-size:14px;color:var(--dark)">🛒 Storico Acquisti</span>
            <a href="{{ url('/purchases/create?supplier_id=' . $supplier->id) }}" class="btn btn-primary" style="padding:5px 14px;font-size:12px">+ Nuovo</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Prodotto</th>
                    <th style="text-align:right">Kg</th>
                    <th style="text-align:right">€/Kg</th>
                    <th style="text-align:right">Totale</th>
                </tr>
            </thead>
            <tbody>
            @forelse($purchases as $purchase)
            <tr>
                <td style="color:var(--muted)">
                    {{ \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') }}
                </td>
                <td style="font-weight:600;color:var(--dark)">
                    {{ $purchase->product->name ?? '—' }}
                </td>
                <td style="text-align:right;font-family:'DM Mono',monospace">
                    {{ $purchase->kg ? number_format($purchase->kg, 3, ',', '.') . ' kg' : '—' }}
                </td>
                <td style="text-align:right;font-family:'DM Mono',monospace;color:var(--muted)">
                    {{ $purchase->price_per_kg ? '€ ' . number_format($purchase->price_per_kg, 2, ',', '.') : '—' }}
                </td>
                <td style="text-align:right;font-family:'DM Mono',monospace;font-weight:700">
                    € {{ number_format($purchase->total, 2, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:32px;color:var(--muted)">
                    Nessun acquisto registrato per questo fornitore.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection