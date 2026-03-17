<?php $__env->startSection('page-title', 'Dashboard — ' . date('d/m/Y')); ?>

<?php $__env->startSection('content'); ?>

<style>

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.kpi-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 18px 20px;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}

.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
}

.kpi-card.green::before  { background: var(--green-l); }
.kpi-card.orange::before { background: #f4a261; }
.kpi-card.blue::before   { background: #4a9eed; }
.kpi-card.red::before    { background: #e74c3c; }

.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
}

.kpi-icon {
    position: absolute;
    top: 14px; right: 14px;
    font-size: 22px;
    opacity: .18;
}

.kpi-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--muted);
    margin-bottom: 8px;
}

.kpi-value {
    font-size: 26px;
    font-weight: 700;
    color: var(--dark);
    font-family: 'DM Mono', monospace;
    letter-spacing: -1px;
}

.kpi-sub {
    font-size: 12px;
    color: var(--muted);
    margin-top: 5px;
}

.kpi-sub a {
    color: var(--green-l);
    text-decoration: none;
    font-weight: 600;
}

.dash-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.dash-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.section-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
}

.section-head {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    font-weight: 700;
    font-size: 13px;
    color: var(--dark);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-head a {
    font-size: 12px;
    font-weight: 500;
    color: var(--green-l);
    text-decoration: none;
}

.section-body { padding: 16px 18px; }

.chart-wrap { height: 240px; }

.quick-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.quick-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: var(--bg);
    text-decoration: none;
    color: var(--dark);
    font-weight: 600;
    font-size: 13px;
    transition: all .15s;
}

.quick-btn:hover {
    background: var(--green-xl);
    border-color: var(--green-l);
    color: var(--green);
}

.quick-btn .q-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: var(--green-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.top-table { width: 100%; border-collapse: collapse; }
.top-table th {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--muted);
    padding: 8px 10px;
    border-bottom: 1px solid var(--border);
}
.top-table td {
    padding: 10px;
    border-bottom: 1px solid #f5f5f5;
    font-size: 13px;
}
.top-table tr:last-child td { border-bottom: none; }

.rank-badge {
    width: 22px; height: 22px;
    border-radius: 50%;
    background: var(--green-xl);
    color: var(--green);
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.margin-pill {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    background: var(--green-xl);
    color: var(--green);
    font-size: 12px;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
}

@media(max-width:1000px) {
    .dash-grid  { grid-template-columns: 1fr; }
    .dash-grid-2 { grid-template-columns: 1fr; }
}

</style>


<div class="kpi-grid">

    <div class="kpi-card green">
        <div class="kpi-icon">💰</div>
        <div class="kpi-label">Fatturato Oggi</div>
        <div class="kpi-value">€ <?php echo e(number_format($revenue,2,',','.')); ?></div>
        <div class="kpi-sub"><?php echo e($docs_count); ?> documenti emessi</div>
    </div>

    <div class="kpi-card orange">
        <div class="kpi-icon">📦</div>
        <div class="kpi-label">Costo Merce</div>
        <div class="kpi-value">€ <?php echo e(number_format($cost,2,',','.')); ?></div>
        <div class="kpi-sub">costo acquisto prodotti</div>
    </div>

    <div class="kpi-card blue">
        <div class="kpi-icon">📈</div>
        <div class="kpi-label">Margine</div>
        <div class="kpi-value">€ <?php echo e(number_format($margin,2,',','.')); ?></div>
        <div class="kpi-sub"><?php echo e(number_format($margin_percent,1,',','.')); ?>% sul fatturato</div>
    </div>

    <div class="kpi-card green">
        <div class="kpi-icon">💳</div>
        <div class="kpi-label">Crediti Clienti</div>
        <div class="kpi-value">€ <?php echo e(number_format($crediti,2,',','.')); ?></div>
        <div class="kpi-sub">
            <a href="<?php echo e(url('/documents')); ?>"><?php echo e($crediti_count); ?> documenti aperti →</a>
        </div>
    </div>

    <div class="kpi-card red">
        <div class="kpi-icon">⚠️</div>
        <div class="kpi-label">Sotto Scorta</div>
        <div class="kpi-value"><?php echo e($low_stock->count()); ?></div>
        <div class="kpi-sub">
            <a href="<?php echo e(url('/magazzino')); ?>">Vedi magazzino →</a>
        </div>
    </div>

</div>


<div class="dash-grid">

    <div class="section-card">
        <div class="section-head">
            <span>📊 Vendite ultimi 6 mesi</span>
        </div>
        <div class="section-body">
            <div class="chart-wrap">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="section-card">
        <div class="section-head">
            <span>⚡ Azioni rapide</span>
        </div>
        <div class="section-body">
            <div class="quick-grid">
                <a href="<?php echo e(url('/documents/create')); ?>" class="quick-btn">
                    <span class="q-icon">📄</span> Nuovo Documento
                </a>
                <a href="<?php echo e(url('/clients/create')); ?>" class="quick-btn">
                    <span class="q-icon">👤</span> Nuovo Cliente
                </a>
                <a href="<?php echo e(url('/carico-magazzino')); ?>" class="quick-btn">
                    <span class="q-icon">🚛</span> Carica Merce
                </a>
                <a href="<?php echo e(url('/products/create')); ?>" class="quick-btn">
                    <span class="q-icon">🧺</span> Nuovo Prodotto
                </a>
                <a href="<?php echo e(url('/magazzino')); ?>" class="quick-btn">
                    <span class="q-icon">📦</span> Magazzino
                </a>
                <a href="<?php echo e(url('/movimenti-magazzino')); ?>" class="quick-btn">
                    <span class="q-icon">🔄</span> Movimenti
                </a>
            </div>
        </div>
    </div>

</div>


<div class="dash-grid-2">

    <div class="section-card">
        <div class="section-head">
            <span>🏆 Top Clienti — ultimi 30gg</span>
            <a href="<?php echo e(url('/report/clienti')); ?>">Vedi tutti</a>
        </div>
        <div class="section-body" style="padding:0">
            <table class="top-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Cliente</th>
                        <th style="text-align:right">Margine</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $top_clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><span class="rank-badge"><?php echo e($i+1); ?></span></td>
                        <td style="font-weight:600"><?php echo e($client->client); ?></td>
                        <td style="text-align:right">
                            <span class="margin-pill">€ <?php echo e(number_format($client->margin,2,',','.')); ?></span>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="3" style="text-align:center;padding:20px;color:var(--muted)">Nessun dato</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-card">
        <div class="section-head">
            <span>🥦 Top Prodotti — ultimi 30gg</span>
            <a href="<?php echo e(url('/report/prodotti')); ?>">Vedi tutti</a>
        </div>
        <div class="section-body" style="padding:0">
            <table class="top-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Prodotto</th>
                        <th style="text-align:right">Margine</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $top_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><span class="rank-badge"><?php echo e($i+1); ?></span></td>
                        <td style="font-weight:600"><?php echo e($product->product); ?></td>
                        <td style="text-align:right">
                            <span class="margin-pill">€ <?php echo e(number_format($product->margin,2,',','.')); ?></span>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="3" style="text-align:center;padding:20px;color:var(--muted)">Nessun dato</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months, 15, 512) ?>,
        datasets: [{
            data: <?php echo json_encode($sales, 15, 512) ?>,
            backgroundColor: '#40916c',
            borderRadius: 6,
            borderSkipped: false,
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
                grid: { color: '#f0f0f0' },
                ticks: {
                    callback: val => '€' + val.toLocaleString('it-IT')
                }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestionale\resources\views/dashboard.blade.php ENDPATH**/ ?>