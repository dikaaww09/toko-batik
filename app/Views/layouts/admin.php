<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= esc($title) ?> — Admin Batik Pusaka</title>
    <link rel="icon" href="<?= base_url('assets/images/mark.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css?v=20260925-newchart2') ?>">
    <script defer src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
    <script defer src="<?= base_url('assets/js/app.js?v=20260925-1') ?>"></script>
</head>
<body class="admin-body">
<a class="skip-link" href="#main-content">Langsung ke konten</a>
<header class="admin-header">
    <div class="container">
        <a class="brand" href="<?= site_url('admin') ?>">
            <img src="<?= base_url('assets/images/mark.svg') ?>" width="32" height="32" alt="">
            Batik Pusaka <span class="admin-label">ADMIN</span>
        </a>
        <a class="text-link" href="<?= site_url('/') ?>">Lihat toko <span aria-hidden="true">↗</span></a>
    </div>
</header>
<nav class="admin-nav container" aria-label="Navigasi admin">
    <?php foreach (['' => 'Dashboard', 'produk' => 'Produk', 'kategori' => 'Kategori', 'pesanan' => 'Pesanan', 'laporan' => 'Laporan'] as $path => $label): ?>
        <?php $activeAdmin = $path === '' ? uri_string() === 'admin' : str_starts_with(uri_string(), 'admin/' . $path); ?>
        <a class="<?= $activeAdmin ? 'active' : '' ?>" href="<?= site_url('admin' . ($path ? '/' . $path : '')) ?>" <?= $activeAdmin ? 'aria-current="page"' : '' ?>><?= $label ?></a>
    <?php endforeach ?>
    <form class="ms-auto" method="post" action="<?= site_url('admin/logout') ?>">
        <?= csrf_field() ?>
        <button class="btn btn-sm btn-outline-primary" type="submit">Keluar</button>
    </form>
</nav>
<main id="main-content" class="container admin-main">
    <?= $this->include('partials/messages') ?>
    <?= $this->renderSection('content') ?>
</main>
<footer class="container admin-footer">© <?= date('Y') ?> Batik Pusaka · Ruang pengelola</footer>
</body>
</html>
