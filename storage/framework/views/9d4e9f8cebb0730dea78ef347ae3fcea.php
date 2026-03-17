

<?php $__env->startSection('page-title','Modifica Ordine'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <div class="page-title">✏️ Modifica Ordine <?php echo e($order->number); ?></div>
        <div class="page-sub">Cliente: <?php echo e($order->client->company_name); ?></div>
    </div>
    <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="btn btn-secondary">← Torna all'ordine</a>
</div>

<form method="POST" action="<?php echo e(route('orders.update', $order->id)); ?>">
<?php echo csrf_field(); ?>
<?php echo method_field('PUT'); ?>

<div class="card" style="margin-bottom:20px">

    <div class="form-group">
        <label>Cliente</label>
        <select name="client_id" required>
            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($client->id); ?>"
                    <?php if($order->client_id == $client->id): ?> selected <?php endif; ?>>
                    <?php echo e($client->company_name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="form-group">
        <label>Data ordine</label>
        <input type="date" name="date" value="<?php echo e($order->date); ?>" required>
    </div>

</div>

<div class="card">

    <div style="font-weight:700;margin-bottom:15px">Prodotti ordine</div>

    <table id="productsTable">
        <thead>
            <tr>
                <th>Prodotto</th>
                <th>Origine</th>
                <th>Colli</th>
                <th>Kg stimati</th>
                <th>Kg reali</th>
                <th>Tara</th>
                <th>Kg netti</th>
                <th>€/kg</th>
                <th>Totale</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="orderRows">

            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <select name="product_id[]" class="productSelect" required>
                        <option value="">Prodotto</option>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($product->id); ?>"
                                data-origin="<?php echo e($product->origin); ?>"
                                data-weight="<?php echo e($product->avg_box_weight); ?>"
                                data-tara="<?php echo e($product->tara); ?>"
                                data-price="<?php echo e($product->price); ?>"
                                <?php if($item->product_id == $product->id): ?> selected <?php endif; ?>>
                                <?php echo e($product->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </td>
                <td><input type="text" name="origin[]" value="<?php echo e($item->origin); ?>" readonly></td>
                <td><input type="number" name="qty[]" step="1" value="<?php echo e($item->qty); ?>"></td>
                <td><input type="number" name="kg_estimated[]" step="0.01" value="<?php echo e($item->kg_estimated); ?>" readonly></td>
                <td><input type="number" name="kg_real[]" step="0.01" value="<?php echo e($item->kg_real); ?>"></td>
                <td><input type="number" name="tara[]" step="0.001" value="<?php echo e($item->tara); ?>"></td>
                <td><input type="number" name="kg_net[]" step="0.01" value="<?php echo e($item->kg_net); ?>" readonly></td>
                <td><input type="number" name="price[]" step="0.01" value="<?php echo e($item->price); ?>"></td>
                <td><input type="number" name="total[]" step="0.01" value="<?php echo e($item->total); ?>" readonly></td>
                <td><button type="button" onclick="removeRow(this)" class="btn btn-secondary">✕</button></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </tbody>
    </table>

    <button type="button" onclick="addRow()" class="btn btn-secondary" style="margin-top:10px">
        + Aggiungi prodotto
    </button>

</div>

<div class="card" style="margin-top:20px;display:flex;justify-content:space-between;align-items:center">
    <div style="font-size:18px;font-weight:700">
        Totale ordine: € <span id="orderTotal"><?php echo e(number_format($order->total,2,',','.')); ?></span>
    </div>
    <button type="submit" class="btn btn-primary">Aggiorna Ordine</button>
</div>

</form>


<template id="rowTemplate">
    <tr>
        <td>
            <select name="product_id[]" class="productSelect" required>
                <option value="">Prodotto</option>
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($product->id); ?>"
                        data-origin="<?php echo e($product->origin); ?>"
                        data-weight="<?php echo e($product->avg_box_weight); ?>"
                        data-tara="<?php echo e($product->tara); ?>"
                        data-price="<?php echo e($product->price); ?>">
                        <?php echo e($product->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </td>
        <td><input type="text" name="origin[]" readonly></td>
        <td><input type="number" name="qty[]" step="1" value="1"></td>
        <td><input type="number" name="kg_estimated[]" step="0.01" readonly></td>
        <td><input type="number" name="kg_real[]" step="0.01"></td>
        <td><input type="number" name="tara[]" step="0.001"></td>
        <td><input type="number" name="kg_net[]" step="0.01" readonly></td>
        <td><input type="number" name="price[]" step="0.01"></td>
        <td><input type="number" name="total[]" step="0.01" readonly></td>
        <td><button type="button" onclick="removeRow(this)" class="btn btn-secondary">✕</button></td>
    </tr>
</template>

<script>

function addRow() {
    const template = document.getElementById('rowTemplate');
    const clone = template.content.cloneNode(true);
    document.getElementById('orderRows').appendChild(clone);
}

function removeRow(btn) {
    const table = document.getElementById('orderRows');
    if (table.rows.length > 1) {
        btn.closest('tr').remove();
        calculateOrderTotal();
    }
}

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('productSelect')) {
        const row = e.target.closest('tr');
        const option = e.target.selectedOptions[0];
        row.querySelector('[name="origin[]"]').value = option.dataset.origin || '';
        row.querySelector('[name="tara[]"]').value   = option.dataset.tara  || '';
        row.querySelector('[name="price[]"]').value  = option.dataset.price || '';
        calculateRow(row);
    }
});

document.addEventListener('input', function(e) {
    const row = e.target.closest('tr');
    if (row && row.closest('#orderRows')) calculateRow(row);
});

function calculateRow(row) {
    const colli  = parseFloat(row.querySelector('[name="qty[]"]').value) || 0;
    const weight = parseFloat(row.querySelector('.productSelect').selectedOptions[0]?.dataset.weight) || 0;
    const kgReal = parseFloat(row.querySelector('[name="kg_real[]"]').value) || 0;
    const tara   = parseFloat(row.querySelector('[name="tara[]"]').value) || 0;
    const price  = parseFloat(row.querySelector('[name="price[]"]').value) || 0;

    const kgEstimated = colli * weight;
    row.querySelector('[name="kg_estimated[]"]').value = kgEstimated.toFixed(2);

    const taraTot = colli * tara;
    const kgNet   = kgReal ? (kgReal - taraTot) : (kgEstimated - taraTot);
    row.querySelector('[name="kg_net[]"]').value = kgNet.toFixed(2);

    const total = kgNet * price;
    row.querySelector('[name="total[]"]').value = total.toFixed(2);

    calculateOrderTotal();
}

function calculateOrderTotal() {
    let total = 0;
    document.querySelectorAll('[name="total[]"]').forEach(el => {
        total += parseFloat(el.value) || 0;
    });
    document.getElementById('orderTotal').innerText = total.toFixed(2).replace('.',',');
}

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestionale\resources\views/orders/edit.blade.php ENDPATH**/ ?>