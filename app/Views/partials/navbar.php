<header class="site-header">
<nav class="navbar navbar-expand-md container" aria-label="Navigasi utama">
<a class="navbar-brand brand" href="<?= site_url('/') ?>"><img src="<?= base_url('assets/images/mark.svg') ?>" alt="" width="36" height="36"><span>Batik Pusaka<span class="brand-sub">CORAK NUSANTARA</span></span></a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Buka navigasi"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="main-nav"><ul class="navbar-nav ms-auto align-items-md-center gap-md-3">
<?php foreach (['beranda' => ['', 'Beranda'], 'katalog' => ['katalog', 'Katalog'], 'tentang' => ['tentang', 'Tentang'], 'kontak' => ['kontak', 'Kontak']] as $key => [$path, $label]): ?>
<li class="nav-item"><a class="nav-link <?= ($active ?? '') === $key ? 'active' : '' ?>" <?= ($active ?? '') === $key ? 'aria-current="page"' : '' ?> href="<?= site_url($path) ?>"><?= esc($label) ?></a></li>
<?php endforeach ?>
<?php $cartCount = (new \App\Libraries\ShoppingCart())->count(); ?>
<li class="nav-item"><a class="nav-link cart-link <?= ($active ?? '') === 'keranjang' ? 'active' : '' ?>" href="<?= site_url('keranjang') ?>" aria-label="Keranjang, <?= $cartCount ?> barang">Keranjang<?php if ($cartCount > 0): ?><span class="cart-count"><?= $cartCount ?></span><?php endif ?></a></li>
<li class="nav-item ms-md-4"><a class="nav-link admin-link" href="<?= site_url('admin') ?>">Admin <span aria-hidden="true">↗</span></a></li>
</ul></div></nav></header>
