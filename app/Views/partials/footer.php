<footer class="site-footer"><div class="container">
<div class="row gy-4 pb-4"><div class="col-md-6"><a class="brand footer-brand" href="<?= site_url('/') ?>">Batik Pusaka</a><p class="mt-3 mb-0">Corak yang bercerita.<br>Batik untuk setiap langkah Anda.</p></div>
<div class="col-6 col-md-3"><h2 class="footer-title">Jelajahi</h2><a href="<?= site_url('katalog') ?>">Katalog</a><a href="<?= site_url('tentang') ?>">Tentang kami</a></div>
<div class="col-6 col-md-3"><h2 class="footer-title">Temui kami</h2><span>Ponorogo, Jawa Timur</span><a href="<?= site_url('kontak') ?>">Hubungi kami</a></div></div>
<div class="footer-bottom"><span>© <?= date('Y') ?> Batik Pusaka. Semua hak dilindungi.</span>
<?php $portfolio = config('Shop')->portfolioURL; if (filter_var($portfolio, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $portfolio)): ?><a href="<?= esc($portfolio, 'attr') ?>" target="_blank" rel="noopener noreferrer">Portofolio</a><?php else: ?><span>Dari Ponorogo, untuk Anda.</span><?php endif ?></div>
</div></footer>
