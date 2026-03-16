@extends('layouts.app')

@section('page-title', 'Dashboard — Oggi ' . date('d/m/Y'))

@section('content')

<style>
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .kpi-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: transform 0.15s, box-shadow 0.15s;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.07);
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
    }

    .kpi-card.green::before  { background: var(--green-l); }
    .kpi-card.orange::before { background: var(--accent); }
    .kpi-card.blue::before   { background: #4c6ef5; }
    .kpi-card.red::before    { background: #e74c3c; }

    .kpi-label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 8px;
    }

    .kpi-value {
        font-size: 26px;
        font-weight: 700;
        color: var(--dark);
        letter-spacing: -0.5px;
        font-family: 'DM Mono', monospace;
    }

    .kpi-sub {
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
    }

    .kpi-icon {
        position: absolute;
        top: 16px; right: 16px;
        font-size: 24px;
        opacity: 0.25;
    }

    /* GRID 2 colonne */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .section-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
    }

    .section-head {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-head h3 {
        font-size: 14px;
        font-weight: 600;
        color: var(--dark);
    }

    .section-body {
        padding: 16px 20px;
    }

    .chart-wrap {
        position: relative;
        height: 220px;
    }

    /* TOP TABLE */
    .top-table { width: 100%; border-collapse: collapse; }
    .top-table th {
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: var(--muted);
        padding: 8px 12px;
        border-bottom: 1px solid var(--border);
        text-align: left;
    }
    .top-table td {
        padding: 10px 12px;
        font-size: 13px;
        border-bottom: 1px solid var(--border);
        color: var(--text);
    }
    .top-table tr:last-child td { border-bottom: none; }
    .top-table tbody tr:hover { background: var(--bg); }

    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px; height: 22px;
        border-radius: 50%;
        font-size: 11px;
        font-weight: 700;
        background: var(--green-xl);
        color: var(--green);
        margin-right: 6px;
    }

    .rank-badge.gold   { background: #fff3cd; color: #856404; }
    .rank-badge.silver { background: #e9ecef; color: #495057; }
    .rank-badge.bronze { background: #fde8d8; color: #8c4e0a; }

    .margin-pill {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: var(--green-xl);
        color: var(--green);
        font-family: 'DM Mono', monospace;
    }

    /* EMPTY STATE */
    .empty-state {
        text-align: center;
        padding: 32px 20px;
        color: var(--muted);
        font-size: 13px;
    }

    .empty-state .emoji { font-size: 32px; display: block; margin-bottom: 8px; }

    /* QUICK ACTIONS */
    .quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .qa-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        text-decoration: none;
        color: var(--text);
        font-size: 13px;
        font-weight: 500;
        transition: all 0.15s;
    }

    .qa-btn:hover {
        background: var(--green-xl);
        border-color: var(--green-l);
        color: var(--green);
    }

    .qa-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        background: var(--card);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        border: 1px solid var(--border);
    }

    @media (max-width: 1100px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .grid-2, .grid-3 { grid-template-columns: 1fr; }
    }
</style>

<!-- KPI CARDS -->
<div class="kpi-grid">
    <div class="kpi-card green">
        <div class="kpi-icon">💰</div>
        <div class="kpi-label">Fatturato Oggi</div>
        <div class="kpi-value">€ {{ number_format($revenue ?? 0, 2, ',', '.') }}</div>
        <div class="kpi-sub">{{ $docs_count ?? 0 }} documenti emessi</div>
    </div>
    <div class="kpi-card orange">
        <div class="kpi-icon">📦</div>
        <div class="kpi-label">Costo Merce</div>
        <div class="kpi-value">€ {{ number_format($cost ?? 0, 2, ',', '.') }}</div>
        <div class="kpi-sub">costo acquisto prodotti</div>
    </div>
    <div class="kpi-card blue">
        <div class="kpi-icon">📈</div>
        <div class="kpi-label">Margine</div>
        <div class="kpi-value">€ {{ number_format($margin ?? 0, 2, ',', '.') }}</div>
        <div class="kpi-sub">{{ number_format($margin_percent ?? 0, 1, ',', '.') }}% sul fatturato</div>
    </div>
    <div class="kpi-card red">
        <div class="kpi-icon">⚠️</div>
        <div class="kpi-label">Sotto Scorta</div>
        <div class="kpi-value">{{ count($low_stock ?? []) }}</div>
        <div class="kpi-sub">prodotti da riordinare</div>
    </div>
</div>

<!-- GRAFICO + AZIONI RAPIDE -->
<div class="grid-3">
    <div class="section-card">
        <div class="section-head">
            <h3>📊 Vendite ultimi mesi</h3>
        </div>
        <div class="section-body">
            <div class="chart-wrap">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="section-card">
        <div class="section-head">
            <h3>⚡ Azioni rapide</h3>
        </div>
        <div class="section-body">
            <div class="quick-actions">
                <a href="{{ url('/documents/create') }}" class="qa-btn">
                    <div class="qa-icon">📄</div>
                    <span>Nuovo Documento</span>
                </a>
                <a href="{{ url('/clients/create') }}" class="qa-btn">
                    <div class="qa-icon">👤</div>
                    <span>Nuovo Cliente</span>
                </a>
                <a href="{{ url('/carico-magazzino') }}" class="qa-btn">
                    <div class="qa-icon">🚛</div>
                    <span>Carica Merce</span>
                </a>
                <a href="{{ url('/routes/create') }}" class="qa-btn">
                    <div class="qa-icon">🗺️</div>
                    <span>Nuovo Giro</span>
                </a>
                <a href="{{ url('/magazzino') }}" class="qa-btn">
                    <div class="qa-icon">📦</div>
                    <span>Magazzino</span>
                </a>
                <a href="{{ url('/report-vendite') }}" class="qa-btn">
                    <div class="qa-icon">📈</div>
                    <span>Report Vendite</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- TOP CLIENTI + TOP PRODOTTI -->
<div class="grid-2">
    <div class="section-card">
        <div class="section-head">
            <h3>🏆 Top Clienti — Margine</h3>
            <a href="{{ url('/report/clienti') }}" class="btn btn-secondary" style="padding:5px 12px; font-size:12px;">Vedi tutti</a>
        </div>
        @if(count($top_clients ?? []) > 0)
        <table class="top-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Margine</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_clients as $i => $c)
                <tr>
                    <td>
                        <span class="rank-badge {{ $i==0 ? 'gold' : ($i==1 ? 'silver' : ($i==2 ? 'bronze' : '')) }}">
                            {{ $i+1 }}
                        </span>
                    </td>
                    <td>{{ $c->client }}</td>
                    <td><span class="margin-pill">€ {{ number_format($c->margin ?? 0, 2, ',', '.') }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <div class="empty-state"><span class="emoji">👥</span>Nessun dato disponibile</div>
        @endif
    </div>

    <div class="section-card">
        <div class="section-head">
            <h3>🥦 Top Prodotti — Margine</h3>
            <a href="{{ url('/report/prodotti') }}" class="btn btn-secondary" style="padding:5px 12px; font-size:12px;">Vedi tutti</a>
        </div>
        @if(count($top_products ?? []) > 0)
        <table class="top-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Prodotto</th>
                    <th>Margine</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_products as $i => $p)
                <tr>
                    <td>
                        <span class="rank-badge {{ $i==0 ? 'gold' : ($i==1 ? 'silver' : ($i==2 ? 'bronze' : '')) }}">
                            {{ $i+1 }}
                        </span>
                    </td>
                    <td>{{ $p->product }}</td>
                    <td><span class="margin-pill">€ {{ number_format($p->margin ?? 0, 2, ',', '.') }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <div class="empty-state"><span class="emoji">🥦</span>Nessun dato disponibile</div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart');
const monthNames = ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];
const rawMonths = @json($months ?? []);
const labels = rawMonths.map(m => monthNames[m - 1] || m);
const data   = @json($sales ?? []);

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Vendite €',
            data: data,
            backgroundColor: 'rgba(64,145,108,0.7)',
            borderColor: '#40916c',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => '€ ' + ctx.parsed.y.toLocaleString('it-IT', {minimumFractionDigits:2})
                }
            }
        },
        scales: {
            y: {
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: {
                    callback: v => '€' + v.toLocaleString('it-IT'),
                    font: { size: 11 }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            }
        }
    }
});
</script>

@endsection