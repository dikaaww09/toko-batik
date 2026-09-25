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

<div class="row g-4">
    <div class="col-lg-8">
        <div class="form-panel dashboard-chart-panel h-100">
            <div class="section-heading mb-3 d-flex flex-wrap gap-3">
                <div>
                    <p class="eyebrow">GRAFIK / STATISTIK</p>
                    <h2>Penjualan <?= $period === 30 ? '30 Hari' : '7 Hari' ?> Terakhir</h2>
                </div>
                <div class="ms-auto d-flex gap-2 align-items-center">
                    <span class="status-pill d-none d-sm-inline-block">Pesanan selesai</span>
                    <form method="get" action="<?= site_url('admin') ?>">
                        <select name="period" class="form-select form-select-sm w-auto d-inline-block" onchange="this.form.submit()">
                            <option value="7days" <?= $period === 7 ? 'selected' : '' ?>>7 Hari</option>
                            <option value="30days" <?= $period === 30 ? 'selected' : '' ?>>30 Hari</option>
                        </select>
                    </form>
                </div>
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

                    <div class="sales-chart-bars" style="--chart-cols: <?= count($salesChart) ?>;">
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
                <div class="empty-state compact-empty"><h2>Belum ada transaksi selesai</h2><p>Grafik akan muncul setelah pesanan ditandai selesai.</p></div>
            <?php endif ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-panel h-100">
            <p class="eyebrow">TERLARIS</p>
            <h2 class="h5 mb-4">Top 5 Produk</h2>

            <?php if ($topProducts): ?>
                <div class="top-products-list">
                    <?php foreach ($topProducts as $item): ?>
                        <div class="top-product-item">
                            <div class="d-flex justify-content-between mb-1">
                                <strong class="text-truncate me-2" title="<?= esc($item['nama_produk']) ?>"><?= esc($item['nama_produk']) ?></strong>
                                <span class="text-muted small"><?= $item['total_qty'] ?> trjual</span>
                            </div>
                            <div class="top-product-track">
                                <div class="top-product-bar" style="width: <?= (int)$item['width'] ?>%"></div>
                            </div>
                            <div class="text-end mt-1">
                                <span class="small text-muted"><?= rupiah($item['total_revenue']) ?></span>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <p class="text-muted small">Belum ada data produk terjual.</p>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="section-heading mt-5">
    <div>
        <p class="eyebrow">TERBARU</p>
        <h2>Pesanan terbaru</h2>
    </div>
    <a class="text-link" href="<?= site_url('admin/pesanan') ?>">Lihat Semua</a>
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
