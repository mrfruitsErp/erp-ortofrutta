<?php $__env->startSection('page-title','Dettaglio Ordine'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">

    <div>
        <div class="page-title">Ordine <?php echo e($order->number); ?></div>
        <div class="page-sub">Cliente: <strong><?php echo e($order->client->company_name ?? ''); ?></strong></div>
    </div>

    <div style="display:flex;gap:10px;align-items:center">

        <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-secondary">
            ← Torna agli ordini
        </a>

        <?php if($order->status == 'draft'): ?>

            <a href="<?php echo e(route('orders.edit', $order->id)); ?>" class="btn btn-warning">
                ✏️ Modifica
            </a>

            <a href="<?php echo e(route('orders.confirm', $order->id)); ?>"
               class="btn btn-primary"
               onclick="return confirm('Confermare l\'ordine <?php echo e($order->number); ?>?')">
                ✅ Conferma ordine
            </a>

        <?php endif; ?>

        <?php if($order->status == 'confirmed'): ?>

            <a href="<?php echo e(route('orders.edit', $order->id)); ?>" class="btn btn-warning">
                ✏️ Modifica
            </a>

            <a href="<?php echo e(route('orders.generateDocument', $order->id)); ?>"
               class="btn btn-success"
               onclick="return confirm('Generare DDT dall\'ordine <?php echo e($order->number); ?>?')">
                📄 Genera DDT
            </a>

        <?php endif; ?>

        <?php if($order->status == 'invoiced'): ?>

            <span class="btn btn-secondary" style="opacity:0.6;cursor:default">
                ✔ DDT generato
            </span>

        <?php endif; ?>

    </div>

</div>



<?php if(session('success')): ?>
    <div class="alert alert-success" style="margin-bottom:20px">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>



<div class="card" style="margin-bottom:20px">
    <div style="display:flex;gap:40px;flex-wrap:wrap">

        <div>
            <strong>Numero ordine</strong><br>
            <?php echo e($order->number); ?>

        </div>

        <div>
            <strong>Data</strong><br>
            <?php echo e(\Carbon\Carbon::parse($order->date)->format('d/m/Y')); ?>

        </div>

        <?php if($order->delivery_date): ?>
        <div>
            <strong>Data consegna</strong><br>
            <?php echo e(\Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y')); ?>

        </div>
        <?php endif; ?>

        <?php if($order->delivery_slot): ?>
        <div>
            <strong>Fascia oraria</strong><br>
            <?php echo e($order->delivery_slot); ?>

        </div>
        <?php endif; ?>

        <div>
            <strong>Stato</strong><br>
            <?php if($order->status == 'draft'): ?>
                <span style="color:#f59e0b;font-weight:600">● Bozza</span>
            <?php elseif($order->status == 'confirmed'): ?>
                <span style="color:#3b82f6;font-weight:600">● Confermato</span>
            <?php elseif($order->status == 'invoiced'): ?>
                <span style="color:#10b981;font-weight:600">● Evaso</span>
            <?php else: ?>
                <?php echo e($order->status); ?>

            <?php endif; ?>
        </div>

    </div>
</div>



<div class="card">

    <table>
        <thead>
            <tr>
                <th>Prodotto</th>
                <th>Origine</th>
                <th style="width:80px;text-align:center">Colli</th>
                <th style="width:100px;text-align:right">Kg stimati</th>
                <th style="width:100px;text-align:right">Kg reali</th>
                <th style="width:100px;text-align:right">Kg netti</th>
                <th style="width:100px;text-align:right">€/kg</th>
                <th style="width:110px;text-align:right">Totale</th>
            </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($item->product->name ?? '—'); ?></td>
                <td><?php echo e($item->origin ?? '—'); ?></td>
                <td style="text-align:center"><?php echo e($item->qty); ?></td>
                <td style="text-align:right"><?php echo e(number_format($item->kg_estimated,2,',','.')); ?></td>
                <td style="text-align:right"><?php echo e($item->kg_real ? number_format($item->kg_real,2,',','.') : '—'); ?></td>
                <td style="text-align:right"><?php echo e(number_format($item->kg_net,2,',','.')); ?></td>
                <td style="text-align:right">€ <?php echo e(number_format($item->price,2,',','.')); ?></td>
                <td style="text-align:right">€ <?php echo e(number_format($item->total,2,',','.')); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="8" style="text-align:center;color:#999;padding:20px">
                    Nessun prodotto in questo ordine
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

</div>



<div class="card" style="margin-top:20px;display:flex;justify-content:flex-end;align-items:center">
    <div style="font-size:20px;font-weight:700">
        Totale ordine &nbsp; € <?php echo e(number_format($order->total,2,',','.')); ?>

    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestionale\resources\views/orders/show.blade.php ENDPATH**/ ?>