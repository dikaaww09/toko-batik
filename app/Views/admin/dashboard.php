<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<p class="eyebrow">RUANG PENGELOLA</p>
<h1>Selamat datang, Admin.</h1>
<p class="text-muted">Pantau produk, stok, transaksi, dan statistik penjualan toko.</p>

<div class="row g-3 my-4">
    <div class="col-sm-6 col-lg-3"><div class="stat-card"><span>Produk</span><strong><?= $totalProducts ?></strong><p><?= $totalCategories ?> kategori</p></div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-card"><span>Pesanan baru</span><strong><?= $newOrders ?></strong><p>Perlu ditindaklanjuti</p></div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-card"><span>Stok habis</span><strong><?= $outOfStock ?></strong><p>Produk perlu ditambah</p></div></div>
    <div class="col-sm-6 col-lg-3"><div class="stat-card"><span>Pendapatan selesai</span><strong class="report-money"><?= rupiah($revenue) ?></strong><p>Dari pesanan selesai</p></div></div>
</div>

<div class="form-panel dashboard-chart-panel">
    <div class="section-heading mb-3">
        <div>
            <p class="eyebrow">GRAFIK / STATISTIK</p>
            <h2>7 tanggal penjualan terbaru</h2>
        </div>
        <span class="status-pill">Pesanan selesai</span>
    </div>
    <?php if ($salesChart):
        $maxRev = max(array_column($salesChart, 'revenue')) ?: 1;
    ?>
    <div class="sales-chart-container" role="img" aria-label="Grafik pendapatan dari pesanan selesai berdasarkan tanggal penjualan terbaru">
        <div class="sales-chart-y-axis">
            <span><?= rupiah($maxRev) ?></span>
            <span><?= rupiah((int)($maxRev / 2)) ?></span>
            <span>Rp0</span>
        </div>
        <div class="sales-chart-grid">
            <div class="sales-chart-gridline" style="bottom: 100%;"></div>
            <div class="sales-chart-gridline" style="bottom: 50%;"></div>
            <div class="sales-chart-gridline" style="bottom: 0%;"></div>

            <div class="sales-chart-bars">
            <?php foreach ($salesChart as $day): ?>
                <div class="sales-chart-col">
                    <div class="sales-chart-hitbox" tabindex="0">
                        <div class="sales-chart-bar" style="height: <?= (int) $day['height'] ?>%"></div>
                        <div class="sales-chart-tooltip">
                            <strong class="d-block mb-1"><?= esc($day['label']) ?></strong>
                            <div class="d-flex justify-content-between gap-3 mb-1">
                                <span style="opacity: 0.8">Pendapatan</span>
                                <strong style="color: #ffccaa"><?= rupiah($day['revenue']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between gap-3">
                                <span style="opacity: 0.8">Transaksi</span>
                                <strong><?= (int) $day['orders'] ?></strong>
                            </div>
                        </div>
                    </div>
                    <span class="sales-chart-x-label"><?= esc($day['label']) ?></span>
                </div>
            <?php endforeach ?>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="empty-state compact-empty"><span aria-hidden="true">↗</span><h2>Belum ada transaksi selesai</h2><p>Grafik akan muncul setelah pesanan ditandai selesai.</p></div>
    <?php endif ?>
</div>

<div class="section-heading mt-5">
    <div>
        <p class="eyebrow">TERBARU</p>
        <h2>Pesanan terbaru</h2>
    </div>
    <a class="text-link" href="<?= site_url('admin/pesanan') ?>">Lihat Semua <span aria-hidden="true">↗</span></a>
</div>
<div class="admin-table table-responsive">
    <table class="table mb-0">
        <thead><tr><th>Kode</th><th>Pembeli</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
            <?php foreach ($recentOrders as $order): ?>
                <tr>
                    <td><a href="<?= site_url('admin/pesanan/' . $order['id']) ?>"><strong><?= esc($order['kode_order']) ?></strong></a></td>
                    <td><?= esc($order['nama_pembeli']) ?></td>
                    <td><?= rupiah($order['total']) ?></td>
                    <td><?= ucfirst(esc($order['status'])) ?></td>
                </tr>
            <?php endforeach ?>
            <?php if (! $recentOrders): ?><tr><td colspan="4" class="text-center p-4">Belum ada pesanan.</td></tr><?php endif ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
