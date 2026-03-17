

<?php $__env->startSection('page-title', 'Modifica ' . $document->number); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <div class="page-title">✏️ Modifica <?php echo e($document->number); ?></div>
        <div class="page-sub">Modifica cliente, data e righe del documento</div>
    </div>
    <a href="<?php echo e(url('/documents/' . $document->id)); ?>" class="btn btn-secondary">← Annulla</a>
</div>

<form method="POST" action="<?php echo e(url('/documents/' . $document->id)); ?>">
<?php echo csrf_field(); ?>
<?php echo method_field('PUT'); ?>

<div class="card" style="padding:20px;margin-bottom:20px">

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

<div class="form-group">
<label>Cliente</label>
<select name="client_id" required>

<option value="">-- seleziona cliente --</option>

<?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<option value="<?php echo e($client->id); ?>" <?php echo e($document->client_id == $client->id ? 'selected' : ''); ?>>
<?php echo e($client->company_name); ?>

</option>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</select>
</div>

<div class="form-group">
<label>Data</label>

<input type="date"
name="date"
value="<?php echo e(\Carbon\Carbon::parse($document->date)->format('Y-m-d')); ?>"
required>

</div>

</div>

</div>


<div class="card" style="padding:0;overflow:hidden;margin-bottom:20px">

<div style="padding:16px 20px;border-bottom:1px solid var(--border);font-weight:700;font-size:14px;color:var(--dark)">
Righe documento
</div>

<table>

<thead>

<tr>

<th>DESCRIZIONE</th>
<th>ORIG</th>
<th>UM</th>
<th>TARA</th>
<th style="text-align:right">COLLI</th>
<th style="text-align:right">KG</th>
<th style="text-align:right">€/KG</th>
<th style="text-align:right">IMPORTO</th>
<th style="text-align:center;width:80px">ELIMINA</th>

</tr>

</thead>

<tbody id="existing-rows">

<?php $__currentLoopData = $document->rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<tr id="existing-row-<?php echo e($row->id); ?>">

<input type="hidden" name="existing_row_id[]" value="<?php echo e($row->id); ?>">

<td>

<select
name="existing_product[]"
class="existing-product"
data-row="<?php echo e($row->id); ?>"
onchange="updateExistingRow(<?php echo e($row->id); ?>)">

<?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<option
value="<?php echo e($product->id); ?>"
data-weight="<?php echo e($product->avg_box_weight); ?>"
data-tara="<?php echo e($product->tara); ?>"
data-origin="<?php echo e($product->origin); ?>"
data-unit="<?php echo e($product->unit); ?>"
<?php echo e($row->product_id == $product->id ? 'selected' : ''); ?>>

<?php echo e($product->name); ?>


</option>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</select>

</td>

<td class="origin">
<?php echo e($row->product->origin ?? ''); ?>

</td>

<td class="unit">
<?php echo e(strtoupper($row->product->unit ?? '')); ?>

</td>

<td class="tara">
<?php echo e(number_format($row->product->tara ?? 0,3,',','.')); ?>

</td>

<td style="text-align:right">

<input
type="number"
name="existing_boxes[]"
class="existing-boxes"
data-row="<?php echo e($row->id); ?>"
value="<?php echo e($row->boxes); ?>"
min="0"
style="width:80px;text-align:right"
oninput="updateExistingRow(<?php echo e($row->id); ?>)">

</td>

<td
style="text-align:right;font-family:'DM Mono',monospace;color:var(--muted)"
id="kg-<?php echo e($row->id); ?>">

<?php echo e(number_format($row->kg_estimated,3,',','.')); ?>


</td>

<td style="text-align:right">

<input
type="number"
name="existing_price[]"
class="existing-price"
data-row="<?php echo e($row->id); ?>"
step="0.01"
value="<?php echo e($row->price_per_kg); ?>"
min="0"
style="width:90px;text-align:right"
oninput="updateExistingRow(<?php echo e($row->id); ?>)">

</td>

<td
style="text-align:right;font-family:'DM Mono',monospace;font-weight:700"
id="total-<?php echo e($row->id); ?>">

€ <?php echo e(number_format($row->total,2,',','.')); ?>


</td>

<td style="text-align:center">

<button
type="button"
onclick="deleteRow(<?php echo e($row->id); ?>)"
style="background:none;border:none;cursor:pointer;font-size:18px;color:#e74c3c">

🗑

</button>

<input
type="checkbox"
name="delete_rows[]"
value="<?php echo e($row->id); ?>"
id="delete-<?php echo e($row->id); ?>"
style="display:none">

</td>

</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>

</table>

</div>


<div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px">

<div style="font-size:18px;font-weight:700;color:var(--dark)">

TOTALE DOCUMENTO:

€ <span id="grandTotal">

<?php echo e(number_format($document->total,2,',','.')); ?>


</span>

</div>

<button
type="submit"
class="btn btn-primary"
style="padding:12px 32px;font-size:15px">

💾 Salva modifiche

</button>

</div>

</form>


<script>

function updateExistingRow(rowId){

const row = document.getElementById('existing-row-'+rowId)

const product = row.querySelector('.existing-product')

const boxes = parseFloat(row.querySelector('.existing-boxes').value)||0

const price = parseFloat(row.querySelector('.existing-price').value)||0

const opt = product.selectedOptions[0]

const weight = parseFloat(opt?.dataset.weight||0)
const tara = parseFloat(opt?.dataset.tara||0)

const origin = opt?.dataset.origin || ''
const unit = opt?.dataset.unit || ''

row.querySelector('.origin').textContent = origin
row.querySelector('.unit').textContent = unit.toUpperCase()
row.querySelector('.tara').textContent = parseFloat(tara).toFixed(3)

const kg = (boxes*weight)-(boxes*tara)

const total = kg*price

document.getElementById('kg-'+rowId).textContent =
kg.toFixed(3).replace('.',',')

document.getElementById('total-'+rowId).textContent =
'€ '+total.toFixed(2).replace('.',',')

recalcTotal()

}

function deleteRow(rowId){

const row=document.getElementById('existing-row-'+rowId)

const checkbox=document.getElementById('delete-'+rowId)

checkbox.checked=true

row.style.opacity='0.3'

row.style.textDecoration='line-through'

row.querySelectorAll('input,select,button').forEach(el=>el.disabled=true)

checkbox.disabled=false

recalcTotal()

}

function recalcTotal(){

let total=0

document.querySelectorAll('#existing-rows tr').forEach(row=>{

const checkbox=row.querySelector('input[type=checkbox]')

if(checkbox && checkbox.checked) return

const totalCell=row.querySelector('td:nth-child(8)')

if(totalCell){

const val=totalCell.textContent.replace('€','').replace('.','').replace(',','.').trim()

total+=parseFloat(val)||0

}

})

document.getElementById('grandTotal').textContent=
total.toLocaleString('it-IT',{minimumFractionDigits:2,maximumFractionDigits:2})

}

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\gestionale\resources\views/documents/edit.blade.php ENDPATH**/ ?>