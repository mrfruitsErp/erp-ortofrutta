<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="utf-8">
<title><?php echo e($document->number); ?></title>

<style>

* { box-sizing:border-box;margin:0;padding:0 }

body{
font-family:DejaVu Sans, Arial;
font-size:11px;
color:#1b2d27;
background:#fff;
}

.page{
padding:32px 36px;
max-width:860px;
margin:0 auto;
}

/* HEADER */

.header{
display:flex;
justify-content:space-between;
align-items:flex-start;
margin-bottom:28px;
padding-bottom:20px;
border-bottom:2px solid #2d6a4f;
}

.company-name{
font-size:20px;
font-weight:700;
color:#2d6a4f;
}

.doc-type{
font-size:22px;
font-weight:700;
color:#2d6a4f;
}

.doc-number{
font-size:12px;
font-family:monospace;
margin-top:4px;
}

.doc-date{
font-size:10px;
color:#7a9e8e;
}

/* CLIENTE */

.info-grid{
display:flex;
gap:24px;
margin-bottom:24px;
}

.info-box{
flex:1;
background:#f5f7f5;
border:1px solid #e2ebe5;
border-radius:8px;
padding:14px 16px;
}

.info-label{
font-size:9px;
font-weight:700;
text-transform:uppercase;
color:#7a9e8e;
margin-bottom:6px;
}

.info-value{
font-size:13px;
font-weight:700;
}

/* TABELLA */

table{
width:100%;
border-collapse:collapse;
margin-bottom:20px;
}

thead tr{
background:#2d6a4f;
color:#fff;
}

thead th{
padding:9px 10px;
font-size:9px;
font-weight:700;
text-transform:uppercase;
}

tbody td{
padding:8px 10px;
border-bottom:1px solid #e2ebe5;
font-size:11px;
}

tbody tr:nth-child(even){
background:#f9fbf9;
}

.center{text-align:center}
.right{text-align:right;font-family:monospace}
.left{text-align:left}

/* TOTALE */

.total-box{
margin-left:auto;
width:260px;
border:2px solid #2d6a4f;
border-radius:8px;
overflow:hidden;
}

.total-row{
display:flex;
justify-content:space-between;
padding:8px 14px;
font-size:11px;
border-bottom:1px solid #e2ebe5;
}

.total-row:last-child{
background:#2d6a4f;
color:#fff;
font-weight:700;
font-size:13px;
}

.total-row span:last-child{
font-family:monospace;
}

/* FIRMA */

.footer-grid{
display:flex;
gap:24px;
margin-top:40px;
}

.firma-box{
flex:1;
border:1px solid #e2ebe5;
border-radius:8px;
padding:14px;
text-align:center;
}

.firma-label{
font-size:9px;
color:#7a9e8e;
margin-bottom:40px;
}

.firma-line{
border-top:1px solid #2d6a4f;
margin-top:8px;
}

</style>
</head>

<body>

<div class="page">

<div class="header">

<div>
<div class="company-name">OrtoPro ERP</div>
</div>

<div>
<div class="doc-type"><?php echo e($document->type); ?></div>
<div class="doc-number"><?php echo e($document->number); ?></div>
<div class="doc-date">
<?php echo e(\Carbon\Carbon::parse($document->date)->format('d/m/Y')); ?>

</div>
</div>

</div>


<div class="info-grid">

<div class="info-box">
<div class="info-label">Cliente</div>
<div class="info-value"><?php echo e($document->client->company_name ?? '—'); ?></div>
<div><?php echo e($document->client->address ?? ''); ?></div>
<div><?php echo e($document->client->city ?? ''); ?></div>
</div>

<div class="info-box">
<div class="info-label">P.IVA</div>
<div class="info-value"><?php echo e($document->client->vat ?? '—'); ?></div>
</div>

</div>


<table>

<thead>
<tr>
<th class="center" style="width:70px">Colli</th>
<th class="left">Descrizione</th>
<th class="center" style="width:90px">Origine</th>
<th class="right" style="width:80px">Tara</th>
<th class="right" style="width:90px">Kg</th>
<th class="right" style="width:90px">€/Kg</th>
<th class="right" style="width:110px">Importo</th>
</tr>
</thead>

<tbody>

<?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<tr>

<td class="center">
<?php echo e($row->boxes); ?>

</td>

<td class="left">
<?php echo e($row->product->name ?? '—'); ?>

</td>

<td class="center">
<?php echo e($row->product->origin ?? ''); ?>

</td>

<td class="right">
<?php echo e(number_format($row->product->tara ?? 0,2,',','.')); ?>

</td>

<td class="right">
<?php echo e(number_format($row->kg_real ?? $row->kg_estimated,2,',','.')); ?>

</td>

<td class="right">
<?php echo e(number_format($row->price_per_kg,2,',','.')); ?>

</td>

<td class="right">
€ <?php echo e(number_format($row->total,2,',','.')); ?>

</td>

</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>

</table>


<div style="display:flex;justify-content:flex-end;margin-bottom:32px">

<div class="total-box">

<div class="total-row">
<span>Imponibile</span>
<span>€ <?php echo e(number_format($rows->sum('total'),2,',','.')); ?></span>
</div>

<div class="total-row">
<span>IVA</span>
<span>—</span>
</div>

<div class="total-row">
<span>TOTALE</span>
<span>€ <?php echo e(number_format($document->total,2,',','.')); ?></span>
</div>

</div>

</div>


<div class="footer-grid">

<div class="firma-box">
<div class="firma-label">Firma del destinatario</div>
<div class="firma-line"></div>
</div>

</div>

</div>

</body>
</html><?php /**PATH C:\xampp\htdocs\gestionale\resources\views/documents/pdf.blade.php ENDPATH**/ ?>