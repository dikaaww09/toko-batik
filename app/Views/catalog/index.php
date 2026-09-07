<?= $this->extend('layouts/main') ?><?= $this->section('content') ?>
<section class="container section-space"><div class="page-heading"><p class="eyebrow">CORAK UNTUK SETIAP CERITA</p><h1>Koleksi batik</h1><p>Temukan yang terasa paling Anda.</p></div>
<form class="catalog-filters row g-3 align-items-end" method="get" action="<?= site_url('katalog') ?>">
<div class="col-md-6"><label class="form-label" for="q">Cari nama produk</label><input class="form-control" type="search" id="q" name="q" placeholder="Misalnya, batik kawung…" value="<?= esc($q, 'attr') ?>" maxlength="150"></div>
<div class="col-md-3"><label class="form-label" for="kategori">Kategori</label><select class="form-select" id="kategori" name="kategori"><option value="">Semua kategori</option><?php foreach ($categories as $item): ?><option value="<?= $item['id'] ?>" <?= (int) $item['id'] === $category ? 'selected' : '' ?>><?= esc($item['nama']) ?></option><?php endforeach ?></select></div>
<div class="col-md-3 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit">Tampilkan</button><?php if ($q !== '' || $category): ?><a class="btn btn-outline-primary" href="<?= site_url('katalog') ?>">Reset</a><?php endif ?></div></form>
<p class="results-count"><?= $pager->getTotal() ?> produk<?= $q !== '' ? ' untuk “' . esc($q) . '”' : ' dalam koleksi' ?></p>
<div class="row g-4"><?php foreach ($products as $product): ?><div class="col-12 col-sm-6 col-lg-4"><?= view('partials/product_card', ['product' => $product]) ?></div><?php endforeach ?></div>
<?php if (! $products): ?><div class="empty-state"><span aria-hidden="true">◇</span><h2>Belum menemukan corak Anda?</h2><p>Produk tidak ditemukan. Coba nama lain atau pilih semua kategori.</p><a class="btn btn-outline-primary" href="<?= site_url('katalog') ?>">Lihat semua produk</a></div><?php endif ?>
<?= $pager->only(['q', 'kategori'])->links('default', 'shop') ?>
</section><?= $this->endSection() ?>
