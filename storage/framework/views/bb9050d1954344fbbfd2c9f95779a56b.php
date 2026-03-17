

<?php $__env->startSection('page-title','Ordini'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">

<div>

<div class="page-title">Ordini</div>

<div class="page-sub">Ordini clienti</div>

</div>

<a href="/orders/create" class="btn btn-primary">
＋ Nuovo Ordine
</a>

</div>

<div class="card">

<table>

<thead>

<tr>

<th>#</th>

<th>Numero</th>

<th>Cliente</th>

<th>Data</th>

<th style="text-align:right">Totale</th>

</tr>

</thead>

<tbody>

<?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<tr>

<td><?php echo e($order->id); ?></td>

<td>
<a href="/orders/<?php echo e($order->id); ?>" style="font-weight:600;color:var(--green);text-decoration:none">
<?php echo e($order->number); ?>

</a>
</td>

<td><?php echo e($order->client->company_name ?? ''); ?></td>

<td><?php echo e($order->date); ?></td>

<td style="text-align:right">
€ <?php echo e(number_format($order->total,2,',','.')); ?>

</td>

</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>

</table>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestionale\resources\views/orders/index.blade.php ENDPATH**/ ?>