

<?php $__env->startSection('page-title', 'Documenti'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <div class="page-title">Documenti</div>
        <div class="page-sub">DDT, fatture e ordini emessi</div>
    </div>
    <a href="<?php echo e(url('/documents/create')); ?>" class="btn btn-primary">＋ Nuovo Documento</a>
</div>


<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">

    <div class="card" style="padding:16px 20px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Totale Documenti</div>
        <div style="font-size:26px;font-weight:700;color:var(--dark)"><?php echo e($documents->count()); ?></div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Fatturato Totale</div>
        <div style="font-size:26px;font-weight:700;color:var(--dark)">€ <?php echo e(number_format($documents->sum('total'),2,',','.')); ?></div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">Questo Mese</div>
        <div style="font-size:26px;font-weight:700;color:var(--dark)">
            <?php echo e($documents->where('date','>=',now()->startOfMonth()->toDateString())->count()); ?>

        </div>
    </div>

    <div class="card" style="padding:16px 20px">
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px">DDT Emessi</div>
        <div style="font-size:26px;font-weight:700;color:var(--dark)">
            <?php echo e($documents->where('type','DDT')->count()); ?>

        </div>
    </div>

</div>


<div class="card" style="padding:0;overflow:hidden">

    
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

        <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

            <tr class="doc-row"
                data-numero="<?php echo e(strtolower($document->number)); ?>"
                data-cliente="<?php echo e(strtolower($document->client->company_name ?? '')); ?>"
                data-tipo="<?php echo e($document->type); ?>">

                <td style="color:var(--muted);font-size:12px">
                    <?php echo e($document->id); ?>

                </td>

                <td>
                    <a href="<?php echo e(url('/documents/'.$document->id)); ?>"
                       style="font-weight:600;color:var(--green);text-decoration:none;font-family:'DM Mono',monospace;font-size:13px">
                        <?php echo e($document->number); ?>

                    </a>
                </td>

                <td>
                    <span style="
                        display:inline-block;
                        padding:3px 10px;
                        border-radius:20px;
                        font-size:11px;
                        font-weight:600;
                        background:<?php echo e($document->type == 'DDT' ? 'var(--green-xl)' : '#fff3e0'); ?>;
                        color:<?php echo e($document->type == 'DDT' ? 'var(--green)' : '#e65100'); ?>

                    ">
                        <?php echo e($document->type); ?>

                    </span>
                </td>

                <td style="font-weight:500">
                    <?php echo e($document->client->company_name ?? '—'); ?>

                </td>

                <td style="color:var(--muted);font-size:13px">
                    <?php echo e(\Carbon\Carbon::parse($document->date)->format('d/m/Y')); ?>

                </td>

                <td style="text-align:right;font-weight:700;font-family:'DM Mono',monospace">
                    € <?php echo e(number_format($document->total,2,',','.')); ?>

                </td>

                <td style="text-align:center">
                    <div style="display:flex;gap:6px;justify-content:center">
                        <a href="<?php echo e(url('/documents/'.$document->id)); ?>"
                           class="btn btn-secondary"
                           style="padding:5px 12px;font-size:12px">
                           👁 Apri
                        </a>

                        <a href="<?php echo e(url('/documents/'.$document->id.'/pdf')); ?>"
                           class="btn btn-secondary"
                           style="padding:5px 12px;font-size:12px"
                           target="_blank">
                           🖨 PDF
                        </a>
                    </div>
                </td>

            </tr>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">
                    Nessun documento trovato.
                </td>
            </tr>

        <?php endif; ?>

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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestionale\resources\views/documents/index.blade.php ENDPATH**/ ?>