<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($title) ?> — Batik Pusaka</title>
<meta name="description" content="Jelajahi katalog Batik Pusaka dari Ponorogo. Temukan kain, kemeja, dan blus batik; tanyakan produk melalui WhatsApp.">
<link rel="icon" href="<?= base_url('assets/images/mark.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/app.css?v=20260925-1') ?>">
<script defer src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script defer src="<?= base_url('assets/js/app.js?v=20260925-fixcart') ?>"></script>
</head>
<body>
<a class="skip-link" href="#main-content">Langsung ke konten</a>
<?= $this->include('partials/navbar') ?>
<div class="container site-messages"><?= $this->include('partials/messages') ?></div>
<main id="main-content"><?= $this->renderSection('content') ?></main>
<?= $this->include('partials/footer') ?>
</body></html>
