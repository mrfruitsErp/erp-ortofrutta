

<?php $__env->startSection('content'); ?>

<div class="card" style="padding:20px">

<h2 style="margin-bottom:20px">Pagamenti</h2>

<table>

<thead>

<tr>
<th>ID</th>
<th>Documento</th>
<th>Importo</th>
<th>Metodo</th>
<th>Data</th>
</tr>

</thead>

<tbody>

<?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<tr>

<td><?php echo e($payment->id); ?></td>

<td><?php echo e($payment->document_id); ?></td>

<td>€ <?php echo e(number_format($payment->amount,2,',','.')); ?></td>

<td><?php echo e($payment->method); ?></td>

<td><?php echo e($payment->payment_date); ?></td>

</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>

</table>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestionale\resources\views/payments/index.blade.php ENDPATH**/ ?>