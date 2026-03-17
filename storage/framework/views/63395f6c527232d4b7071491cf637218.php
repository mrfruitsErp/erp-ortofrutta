<!DOCTYPE html>

<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ERP Ortofrutta</title>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
--green:#2d6a4f;
--green-l:#40916c;
--green-xl:#d8f3dc;
--accent:#f4a261;
--dark:#1b2d27;
--text:#2c3e35;
--muted:#7a9e8e;
--bg:#f5f7f5;
--card:#ffffff;
--border:#e2ebe5;
--sidebar-w:230px;
}

body{
font-family:"DM Sans",sans-serif;
background:var(--bg);
color:var(--text);
min-height:100vh;
display:flex;
}

/* ---------- SIDEBAR ---------- */

.sidebar{
width:var(--sidebar-w);
min-height:100vh;
background:var(--dark);
display:flex;
flex-direction:column;
position:fixed;
top:0;
left:0;
z-index:100;
}

.sidebar-logo{
padding:24px 20px 20px;
border-bottom:1px solid rgba(255,255,255,0.08);
}

.sidebar-logo a{
text-decoration:none;
display:flex;
align-items:center;
gap:10px;
}

.logo-icon{
width:36px;
height:36px;
background:var(--green-l);
border-radius:8px;
display:flex;
align-items:center;
justify-content:center;
font-size:18px;
}

.logo-text{
font-size:15px;
font-weight:700;
color:#fff;
}

.logo-sub{
font-size:10px;
color:var(--muted);
text-transform:uppercase;
}

.sidebar-nav{
padding:16px 10px;
flex:1;
overflow-y:auto;
}

.nav-section{
margin-bottom:24px;
}

.nav-label{
font-size:10px;
font-weight:600;
text-transform:uppercase;
color:var(--muted);
padding:0 10px;
margin-bottom:6px;
}

.nav-item{
display:flex;
align-items:center;
gap:10px;
padding:9px 12px;
border-radius:8px;
text-decoration:none;
color:rgba(255,255,255,0.65);
font-size:13.5px;
font-weight:500;
margin-bottom:2px;
}

.nav-item:hover{
background:rgba(255,255,255,0.07);
color:#fff;
}

.nav-item.active{
background:var(--green-l);
color:#fff;
}

.nav-item .icon{
font-size:16px;
width:20px;
text-align:center;
}

.sidebar-footer{
padding:16px 10px;
border-top:1px solid rgba(255,255,255,0.08);
}

/* ---------- MAIN ---------- */

.main-wrap{
margin-left:var(--sidebar-w);
flex:1;
display:flex;
flex-direction:column;
min-height:100vh;
}

.topbar{
height:60px;
background:var(--card);
border-bottom:1px solid var(--border);
display:flex;
align-items:center;
justify-content:space-between;
padding:0 28px;
}

.topbar-title{
font-size:15px;
font-weight:600;
}

.btn-home{
background:var(--green-xl);
color:var(--green);
border:none;
border-radius:8px;
padding:7px 14px;
font-size:13px;
text-decoration:none;
font-weight:600;
}

.content{
padding:28px;
flex:1;
}

/* ---------- ALERT ---------- */

.alert{
padding:12px 16px;
border-radius:8px;
margin-bottom:20px;
}

.alert-success{
background:var(--green-xl);
color:var(--green);
}

.alert-error{
background:#fde8e8;
color:#c0392b;
}

/* ---------- PAGE HEADER ---------- */

.page-header{
display:flex;
align-items:center;
justify-content:space-between;
margin-bottom:20px;
}

.page-title{
font-size:20px;
font-weight:700;
color:var(--dark);
}

.page-sub{
font-size:13px;
color:var(--muted);
margin-top:2px;
}

/* ---------- CARD ---------- */

.card{
background:var(--card);
border:1px solid var(--border);
border-radius:12px;
padding:18px 20px;
box-shadow:0 1px 3px rgba(0,0,0,0.04);
}

/* ---------- BUTTONS ---------- */

.btn{
display:inline-block;
padding:8px 14px;
border-radius:8px;
font-size:13px;
font-weight:600;
text-decoration:none;
border:1px solid transparent;
}

.btn-primary{
background:var(--green);
color:#fff;
}

.btn-primary:hover{
background:var(--green-l);
}

.btn-secondary{
background:#fff;
border:1px solid var(--border);
color:var(--text);
}

/* ---------- TABLE ---------- */

table{
width:100%;
border-collapse:collapse;
font-size:13px;
}

thead{
background:#f8faf9;
}

th{
text-align:left;
font-weight:600;
font-size:12px;
padding:10px 12px;
border-bottom:1px solid var(--border);
color:var(--muted);
}

td{
padding:10px 12px;
border-bottom:1px solid var(--border);
}

tr:hover{
background:#fafdfb;
}

/* ---------- FORM ---------- */

.form-group{
display:flex;
flex-direction:column;
margin-bottom:16px;
}

label{
font-size:12px;
font-weight:600;
color:var(--muted);
margin-bottom:6px;
}

input,
select{
width:100%;
padding:9px 10px;
border:1px solid var(--border);
border-radius:8px;
font-size:13px;
background:#fff;
color:var(--text);
}

input:focus,
select:focus{
outline:none;
border-color:var(--green);
box-shadow:0 0 0 2px rgba(45,106,79,0.1);
}

input::placeholder{
color:#9aa8a0;
}

</style>

</head>

<body>

<aside class="sidebar">

<div class="sidebar-logo">

<a href="<?php echo e(url('/dashboard')); ?>">

<div class="logo-icon">🥦</div>

<div>
<div class="logo-text">OrtoPro ERP</div>
<div class="logo-sub">Gestionale B2B</div>
</div>

</a>

</div>

<nav class="sidebar-nav">

<div class="nav-section">
<div class="nav-label">Principale</div>

<a href="<?php echo e(url('/dashboard')); ?>" class="nav-item <?php echo e(request()->is('dashboard') ? 'active' : ''); ?>">
<span class="icon">📊</span> Dashboard
</a>

</div>

<div class="nav-section">

<div class="nav-label">Vendite</div>

<a href="<?php echo e(url('/orders')); ?>" class="nav-item <?php echo e(request()->is('orders*') ? 'active' : ''); ?>">
<span class="icon">🧾</span> Ordini
</a>

<a href="<?php echo e(url('/documents')); ?>" class="nav-item <?php echo e(request()->is('documents*') ? 'active' : ''); ?>">
<span class="icon">📄</span> Documenti
</a>

<a href="<?php echo e(url('/clients')); ?>" class="nav-item <?php echo e(request()->is('clients*') ? 'active' : ''); ?>">
<span class="icon">👥</span> Clienti
</a>

<a href="<?php echo e(url('/payments')); ?>" class="nav-item <?php echo e(request()->is('payments*') ? 'active' : ''); ?>">
<span class="icon">💶</span> Pagamenti
</a>

</div>

<div class="nav-section">

<div class="nav-label">Magazzino</div>

<a href="<?php echo e(url('/products')); ?>" class="nav-item <?php echo e(request()->is('products*') ? 'active' : ''); ?>">
<span class="icon">🧺</span> Prodotti
</a>

<a href="<?php echo e(url('/magazzino')); ?>" class="nav-item <?php echo e(request()->is('magazzino*') ? 'active' : ''); ?>">
<span class="icon">📦</span> Magazzino
</a>

<a href="<?php echo e(url('/movimenti-magazzino')); ?>" class="nav-item <?php echo e(request()->is('movimenti*') ? 'active' : ''); ?>">
<span class="icon">🔄</span> Movimenti
</a>

<a href="<?php echo e(url('/carico-magazzino')); ?>" class="nav-item <?php echo e(request()->is('carico*') ? 'active' : ''); ?>">
<span class="icon">🚛</span> Carico Merce
</a>

</div>

<div class="nav-section">

<div class="nav-label">Fornitori</div>

<a href="<?php echo e(url('/suppliers')); ?>" class="nav-item <?php echo e(request()->is('suppliers*') ? 'active' : ''); ?>">
<span class="icon">🏭</span> Fornitori
</a>

<a href="<?php echo e(url('/purchases')); ?>" class="nav-item <?php echo e(request()->is('purchases*') ? 'active' : ''); ?>">
<span class="icon">🛒</span> Acquisti
</a>

</div>

</nav>

<div class="sidebar-footer">

<form method="POST" action="<?php echo e(route('logout')); ?>">
<?php echo csrf_field(); ?>
<button type="submit" class="nav-item" style="width:100%;border:none;background:transparent">
<span class="icon">🚪</span> Logout
</button>
</form>

</div>

</aside>

<div class="main-wrap">

<header class="topbar">

<span class="topbar-title">
<?php echo $__env->yieldContent('page-title','ERP Ortofrutta'); ?>
</span>

<a href="<?php echo e(url('/dashboard')); ?>" class="btn-home">🏠 Dashboard</a>

</header>

<main class="content">

<?php if(session('success')): ?>

<div class="alert alert-success">✅ <?php echo e(session('success')); ?></div>
<?php endif; ?>

<?php if(session('error')): ?>

<div class="alert alert-error">❌ <?php echo e(session('error')); ?></div>
<?php endif; ?>

<?php echo $__env->yieldContent('content'); ?>

</main>

</div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\gestionale\resources\views/layouts/app.blade.php ENDPATH**/ ?>