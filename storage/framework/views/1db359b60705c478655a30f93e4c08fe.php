<?php $__env->startSection('page-title','Nuovo Ordine'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
<div>
<div class="page-title">🧾 Nuovo Ordine</div>
<div class="page-sub">Crea un ordine cliente</div>
</div>
<a href="/orders" class="btn btn-secondary">← Torna agli ordini</a>
</div>

<form method="POST" action="/orders">
<?php echo csrf_field(); ?>

<div class="card" style="margin-bottom:20px">

<div class="form-group">
<label>Cliente</label>
<select name="client_id" required>

<option value="">Seleziona cliente</option>

<?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<option value="<?php echo e($client->id); ?>">
<?php echo e($client->company_name); ?>

</option>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</select>
</div>

<div class="form-group">
<label>Data ordine</label>
<input type="date" name="date" value="<?php echo e(date('Y-m-d')); ?>" required>
</div>

</div>

<div class="card">

<div style="font-weight:700;margin-bottom:15px">
Prodotti ordine
</div>

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

<td>
<input type="text" name="origin[]" readonly>
</td>

<td>
<input type="number" name="qty[]" step="1" value="1">
</td>

<td>
<input type="number" name="kg_estimated[]" step="0.01" readonly>
</td>

<td>
<input type="number" name="kg_real[]" step="0.01">
</td>

<td>
<input type="number" name="tara[]" step="0.001">
</td>

<td>
<input type="number" name="kg_net[]" step="0.01" readonly>
</td>

<td>
<input type="number" name="price[]" step="0.01">
</td>

<td>
<input type="number" name="total[]" step="0.01" readonly>
</td>

<td>
<button type="button" onclick="removeRow(this)" class="btn btn-secondary">✕</button>
</td>

</tr>

</tbody>

</table>

<button type="button" onclick="addRow()" class="btn btn-secondary" style="margin-top:10px">
+ Aggiungi prodotto
</button>

</div>

<div class="card" style="margin-top:20px;display:flex;justify-content:space-between;align-items:center">

<div style="font-size:18px;font-weight:700">
Totale ordine: € <span id="orderTotal">0.00</span>
</div>

<button type="submit" class="btn btn-primary">
Salva Ordine
</button>

</div>

</form>

<script>

function addRow(){

const table = document.getElementById('orderRows')

const row = table.rows[0].cloneNode(true)

row.querySelectorAll('input').forEach(i=>i.value='')

table.appendChild(row)

}

function removeRow(btn){

const row = btn.closest('tr')
const table = document.getElementById('orderRows')

if(table.rows.length>1){
row.remove()
calculateOrderTotal()
}

}

document.addEventListener('change',function(e){

if(e.target.classList.contains('productSelect')){

const row = e.target.closest('tr')
const option = e.target.selectedOptions[0]

row.querySelector('[name="origin[]"]').value = option.dataset.origin
row.querySelector('[name="tara[]"]').value = option.dataset.tara
row.querySelector('[name="price[]"]').value = option.dataset.price

calculateRow(row)

}

})

document.addEventListener('input',function(e){

const row = e.target.closest('tr')
if(row) calculateRow(row)

})

function calculateRow(row){

const colli = parseFloat(row.querySelector('[name="qty[]"]').value) || 0
const weight = row.querySelector('.productSelect').selectedOptions[0]?.dataset.weight || 0
const kgReal = parseFloat(row.querySelector('[name="kg_real[]"]').value) || 0
const tara = parseFloat(row.querySelector('[name="tara[]"]').value) || 0
const price = parseFloat(row.querySelector('[name="price[]"]').value) || 0

const kgEstimated = colli * weight
row.querySelector('[name="kg_estimated[]"]').value = kgEstimated.toFixed(2)

const taraTot = colli * tara
const kgNet = kgReal ? (kgReal - taraTot) : (kgEstimated - taraTot)

row.querySelector('[name="kg_net[]"]').value = kgNet.toFixed(2)

const total = kgNet * price
row.querySelector('[name="total[]"]').value = total.toFixed(2)

calculateOrderTotal()

}

function calculateOrderTotal(){

let total = 0

document.querySelectorAll('[name="total[]"]').forEach(el=>{
total += parseFloat(el.value) || 0
})

document.getElementById('orderTotal').innerText = total.toFixed(2)

}

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestionale\resources\views/orders/create.blade.php ENDPATH**/ ?>