<?php $__env->startSection('page-title', 'Prodotti'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <div class="page-title">🧺 Prodotti</div>
        <div class="page-sub">Catalogo prodotti e prezzi</div>
    </div>
    <a href="<?php echo e(url('/products/create')); ?>" class="btn btn-primary">+ Nuovo Prodotto</a>
</div>


<div id="massiveBar" style="display:none;background:var(--dark);color:#fff;padding:12px 18px;border-radius:10px;margin-bottom:16px;display:none;align-items:center;gap:12px;flex-wrap:wrap">
    <span id="selectedCount" style="font-weight:700;font-size:14px">0 selezionati</span>

    <div style="display:flex;align-items:center;gap:8px">
        <span style="font-size:13px;color:rgba(255,255,255,0.7)">Prezzo +/-%</span>
        <input type="number" id="prezzoPerc" step="0.1" placeholder="Es. +10 o -5" style="width:110px;margin:0;background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.2);color:#fff">
        <button onclick="massivePrezzo()" class="btn btn-primary" style="padding:7px 14px;font-size:13px">Applica Prezzo</button>
    </div>

    <div style="display:flex;align-items:center;gap:8px">
        <span style="font-size:13px;color:rgba(255,255,255,0.7)">Costo +/-%</span>
        <input type="number" id="costoPerc" step="0.1" placeholder="Es. +10 o -5" style="width:110px;margin:0;background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.2);color:#fff">
        <button onclick="massiveCosto()" class="btn btn-primary" style="padding:7px 14px;font-size:13px">Applica Costo</button>
    </div>

    <div style="display:flex;align-items:center;gap:8px">
        <span style="font-size:13px;color:rgba(255,255,255,0.7)">Scorta Min.</span>
        <input type="number" id="minStockVal" step="0.001" min="0" placeholder="Es. 50" style="width:110px;margin:0;background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.2);color:#fff">
        <button onclick="massiveMinStock()" class="btn btn-primary" style="padding:7px 14px;font-size:13px">Imposta</button>
    </div>

    <button onclick="exportCSV()" class="btn btn-secondary" style="padding:7px 14px;font-size:13px">📥 Esporta CSV</button>
    <button onclick="deselectAll()" class="btn btn-secondary" style="padding:7px 14px;font-size:13px;margin-left:auto">✕ Deseleziona</button>
</div>

<div class="card" style="padding:0;overflow:hidden">

    
    <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;gap:12px;flex-wrap:wrap;align-items:center">
        <input type="text" id="searchInput" placeholder="🔍 Cerca per nome..." style="max-width:220px;margin:0">
        <select id="filterOrigine" style="max-width:120px;margin:0">
            <option value="">Tutte le origini</option>
            <?php $__currentLoopData = $products->pluck('origin')->unique()->filter()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($orig); ?>"><?php echo e($orig); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select id="filterUM" style="max-width:100px;margin:0">
            <option value="">Tutte UM</option>
            <?php $__currentLoopData = $products->pluck('unit')->unique()->filter()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $um): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($um); ?>"><?php echo e($um); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select id="filterStato" style="max-width:140px;margin:0">
            <option value="">Tutti gli stati</option>
            <option value="ok">✓ OK</option>
            <option value="esaurito">⚠ Esaurito</option>
            <option value="sottocosto">⚠ Sotto costo</option>
        </select>
        <button onclick="resetFiltri()" class="btn btn-secondary" style="padding:7px 14px;font-size:13px">✕ Reset</button>
        <span id="countLabel" style="font-size:12px;color:var(--muted);margin-left:auto"></span>
    </div>

    <table id="productsTable">
        <thead>
            <tr>
                <th style="width:40px;text-align:center">
                    <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                </th>
                <th style="cursor:pointer" onclick="sortTable(1)">Nome ↕</th>
                <th style="cursor:pointer" onclick="sortTable(2)">Origine ↕</th>
                <th>UM</th>
                <th style="text-align:right;cursor:pointer" onclick="sortTable(4)">Tara ↕</th>
                <th style="text-align:right;cursor:pointer" onclick="sortTable(5)">Peso Cassa ↕</th>
                <th style="text-align:right;cursor:pointer" onclick="sortTable(6)">Costo ↕</th>
                <th style="text-align:right;cursor:pointer" onclick="sortTable(7)">Prezzo ↕</th>
                <th style="text-align:right;cursor:pointer" onclick="sortTable(8)">Margine ↕</th>
                <th style="text-align:right;cursor:pointer" onclick="sortTable(9)">Stock ↕</th>
                <th style="text-align:center">Stato</th>
                <th style="text-align:center;width:100px">Azioni</th>
            </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

        <?php
            $cost      = $product->cost_price ?? 0;
            $price     = $product->price ?? 0;
            $margin    = $price > 0 ? (($price - $cost) / $price) * 100 : 0;
            $stock_qty = $product->stock->quantity ?? 0;
            if ($stock_qty <= 0) $stato = 'esaurito';
            elseif ($price < $cost) $stato = 'sottocosto';
            else $stato = 'ok';
        ?>

        <tr class="product-row"
            data-id="<?php echo e($product->id); ?>"
            data-name="<?php echo e($product->name); ?>"
            data-cost="<?php echo e($cost); ?>"
            data-price="<?php echo e($price); ?>"
            data-unit="<?php echo e($product->unit ?? 'kg'); ?>"
            data-stock="<?php echo e($stock_qty); ?>"
            data-name-lower="<?php echo e(strtolower($product->name)); ?>"
            data-origine="<?php echo e(strtolower($product->origin ?? '')); ?>"
            data-um="<?php echo e(strtolower($product->unit ?? 'kg')); ?>"
            data-stato="<?php echo e($stato); ?>">

            <td style="text-align:center">
                <input type="checkbox" class="row-check" data-id="<?php echo e($product->id); ?>" onchange="updateSelection()">
            </td>
            <td style="font-weight:700;color:var(--dark)"><?php echo e($product->name); ?></td>
            <td style="color:var(--muted);font-size:13px"><?php echo e($product->origin ?? '—'); ?></td>
            <td>
                <span style="background:var(--bg);border:1px solid var(--border);padding:2px 8px;border-radius:6px;font-size:12px;font-weight:600">
                    <?php echo e($product->unit ?? 'kg'); ?>

                </span>
            </td>
            <td style="text-align:right;font-family:'DM Mono',monospace;font-size:13px" data-val="<?php echo e($product->tara ?? 0); ?>">
                <?php echo e(number_format($product->tara ?? 0, 3, ',', '.')); ?> kg
            </td>
            <td style="text-align:right;font-family:'DM Mono',monospace;font-size:13px" data-val="<?php echo e($product->avg_box_weight ?? 0); ?>">
                <?php echo e(number_format($product->avg_box_weight ?? 0, 3, ',', '.')); ?> kg
            </td>
            <td style="text-align:right;font-family:'DM Mono',monospace;font-size:13px" data-val="<?php echo e($cost); ?>">
                € <?php echo e(number_format($cost, 2, ',', '.')); ?>

            </td>
            <td style="text-align:right;font-family:'DM Mono',monospace;font-size:13px;font-weight:700" data-val="<?php echo e($price); ?>">
                € <?php echo e(number_format($price, 2, ',', '.')); ?>

            </td>
            <td style="text-align:right" data-val="<?php echo e($margin); ?>">
                <span style="display:inline-block;padding:3px 8px;border-radius:20px;font-size:12px;font-weight:700;font-family:'DM Mono',monospace;
                    background:<?php echo e($margin >= 15 ? 'var(--green-xl)' : '#fde8e8'); ?>;
                    color:<?php echo e($margin >= 15 ? 'var(--green)' : '#c0392b'); ?>">
                    <?php echo e(number_format($margin, 1, ',', '.')); ?>%
                </span>
            </td>
            <td style="text-align:right;font-family:'DM Mono',monospace;font-size:13px" data-val="<?php echo e($stock_qty); ?>">
                <?php echo e(number_format($stock_qty, 2, ',', '.')); ?> <?php echo e($product->unit ?? 'kg'); ?>

            </td>
            <td style="text-align:center">
                <?php if($stato == 'esaurito'): ?>
                    <span style="background:#fde8e8;color:#c0392b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">⚠ Esaurito</span>
                <?php elseif($stato == 'sottocosto'): ?>
                    <span style="background:#fff3e0;color:#e65100;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">⚠ Sotto costo</span>
                <?php else: ?>
                    <span style="background:var(--green-xl);color:var(--green);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">✓ OK</span>
                <?php endif; ?>
            </td>
            <td style="text-align:center">
                <a href="<?php echo e(url('/products/' . $product->id . '/edit')); ?>" class="btn btn-secondary" style="padding:5px 12px;font-size:12px">✏️ Modifica</a>
            </td>
        </tr>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="12" style="text-align:center;padding:40px;color:var(--muted)">
                Nessun prodotto ancora. <a href="<?php echo e(url('/products/create')); ?>">Aggiungi il primo →</a>
            </td>
        </tr>
        <?php endif; ?>
        </tbody>
    </table>

</div>

<script>
const csrfToken = '<?php echo e(csrf_token()); ?>';
let sortDir = {};

// ─── SELEZIONE ───────────────────────────────────────────
function getSelected() {
    return Array.from(document.querySelectorAll('.row-check:checked')).map(c => c.dataset.id);
}

function updateSelection() {
    const ids = getSelected();
    const bar = document.getElementById('massiveBar');
    bar.style.display = ids.length > 0 ? 'flex' : 'none';
    document.getElementById('selectedCount').textContent = ids.length + ' selezionati';
}

function toggleAll(cb) {
    document.querySelectorAll('.product-row:not([style*="display: none"]) .row-check').forEach(c => c.checked = cb.checked);
    updateSelection();
}

function deselectAll() {
    document.querySelectorAll('.row-check').forEach(c => c.checked = false);
    document.getElementById('selectAll').checked = false;
    updateSelection();
}

// ─── AGGIORNA PREZZO % ───────────────────────────────────
function massivePrezzo() {
    const perc = parseFloat(document.getElementById('prezzoPerc').value);
    if (isNaN(perc)) { alert('Inserisci una percentuale valida'); return; }
    const ids = getSelected();
    if (!ids.length) return;

    fetch('/products/massive-update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ ids, action: 'price_percent', value: perc })
    }).then(r => r.json()).then(d => {
        if (d.success) { location.reload(); }
        else alert('Errore: ' + d.message);
    });
}

// ─── AGGIORNA COSTO % ────────────────────────────────────
function massiveCosto() {
    const perc = parseFloat(document.getElementById('costoPerc').value);
    if (isNaN(perc)) { alert('Inserisci una percentuale valida'); return; }
    const ids = getSelected();
    if (!ids.length) return;

    fetch('/products/massive-update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ ids, action: 'cost_percent', value: perc })
    }).then(r => r.json()).then(d => {
        if (d.success) { location.reload(); }
        else alert('Errore: ' + d.message);
    });
}

// ─── IMPOSTA SCORTA MINIMA ───────────────────────────────
function massiveMinStock() {
    const val = parseFloat(document.getElementById('minStockVal').value);
    if (isNaN(val) || val < 0) { alert('Inserisci un valore valido'); return; }
    const ids = getSelected();
    if (!ids.length) return;

    fetch('/products/massive-update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ ids, action: 'min_stock', value: val })
    }).then(r => r.json()).then(d => {
        if (d.success) { location.reload(); }
        else alert('Errore: ' + d.message);
    });
}

// ─── ESPORTA CSV ─────────────────────────────────────────
function exportCSV() {
    const ids = getSelected();
    const rows = ids.length > 0
        ? Array.from(document.querySelectorAll('.product-row')).filter(r => ids.includes(r.dataset.id))
        : Array.from(document.querySelectorAll('.product-row:not([style*="display: none"])'));

    const headers = ['Nome','Origine','UM','Tara','Peso Cassa','Costo','Prezzo','Margine%','Stock'];
    const lines   = [headers.join(';')];

    rows.forEach(r => {
        lines.push([
            r.dataset.name,
            r.dataset.origine,
            r.dataset.unit,
            r.dataset.cost,
            r.dataset.price,
            r.dataset.stock,
        ].join(';'));
    });

    // Usa tutti i data attributes disponibili
    const csvRows = [['Nome','Origine','UM','Costo','Prezzo','Stock'].join(';')];
    rows.forEach(r => {
        csvRows.push([
            '"' + r.dataset.name + '"',
            r.dataset.origine || '',
            r.dataset.um || '',
            r.dataset.cost || '',
            r.dataset.price || '',
            r.dataset.stock || '',
        ].join(';'));
    });

    const blob = new Blob(['\uFEFF' + csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = 'prodotti_' + new Date().toISOString().slice(0,10) + '.csv';
    a.click();
    URL.revokeObjectURL(url);
}

// ─── FILTRI ──────────────────────────────────────────────
function filterRows() {
    const q       = document.getElementById('searchInput').value.toLowerCase();
    const origine = document.getElementById('filterOrigine').value.toLowerCase();
    const um      = document.getElementById('filterUM').value.toLowerCase();
    const stato   = document.getElementById('filterStato').value;
    let visible   = 0;

    document.querySelectorAll('.product-row').forEach(row => {
        const match =
            (!q       || row.dataset.nameLower.includes(q)) &&
            (!origine || row.dataset.origine === origine) &&
            (!um      || row.dataset.um === um) &&
            (!stato   || row.dataset.stato === stato);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    document.getElementById('countLabel').textContent = visible + ' prodotti';
}

function resetFiltri() {
    document.getElementById('searchInput').value   = '';
    document.getElementById('filterOrigine').value = '';
    document.getElementById('filterUM').value      = '';
    document.getElementById('filterStato').value   = '';
    filterRows();
}

// ─── ORDINAMENTO ─────────────────────────────────────────
function sortTable(colIdx) {
    const tbody = document.querySelector('#productsTable tbody');
    const rows  = Array.from(tbody.querySelectorAll('.product-row'));
    sortDir[colIdx] = !sortDir[colIdx];

    rows.sort((a, b) => {
        const aCell = a.cells[colIdx];
        const bCell = b.cells[colIdx];
        const aVal  = aCell.dataset.val !== undefined ? parseFloat(aCell.dataset.val) : aCell.textContent.trim().toLowerCase();
        const bVal  = bCell.dataset.val !== undefined ? parseFloat(bCell.dataset.val) : bCell.textContent.trim().toLowerCase();

        if (!isNaN(aVal) && !isNaN(bVal)) return sortDir[colIdx] ? aVal - bVal : bVal - aVal;
        return sortDir[colIdx] ? String(aVal).localeCompare(String(bVal)) : String(bVal).localeCompare(String(aVal));
    });

    rows.forEach(r => tbody.appendChild(r));
}

document.getElementById('searchInput').addEventListener('input', filterRows);
document.getElementById('filterOrigine').addEventListener('change', filterRows);
document.getElementById('filterUM').addEventListener('change', filterRows);
document.getElementById('filterStato').addEventListener('change', filterRows);

filterRows();
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestionale\resources\views/products/index.blade.php ENDPATH**/ ?>