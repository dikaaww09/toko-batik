<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<!-- PRINT ONLY HEADER (KOP SURAT) -->
<div class="print-only-header d-none d-print-block">
    <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px;">
        <h1 style="font-size: 28px; font-family: 'Times New Roman', serif; margin: 0;">BATIK PUSAKA</h1>
        <p style="margin: 0; font-size: 14px;">CORAK NUSANTARA</p>
        <p style="margin: 5px 0 0 0; font-size: 14px; color: #555;">Ponorogo, Jawa Timur | Telp: 081234567890</p>
        <h2 style="font-size: 18px; margin: 20px 0 5px 0; text-transform: uppercase;">Laporan Penjualan</h2>
        <p style="margin: 0; font-size: 14px;">
            Periode: <?= $start ? date('d/m/Y', strtotime($start)) : 'Awal' ?> s.d <?= $end ? date('d/m/Y', strtotime($end)) : 'Sekarang' ?>
        </p>
    </div>
</div>

<div class="section-heading no-print">
    <div>
        <p class="eyebrow">RINGKASAN TOKO</p>
        <h1>Laporan penjualan</h1>
        <p class="text-muted mb-0">Hanya pesanan berstatus selesai yang dihitung.</p>
    </div>
    <button class="btn btn-outline-primary no-print" type="button" onclick="window.print()">Cetak Laporan</button>
</div>

<form class="form-panel report-filter no-print mb-4" method="get">
    <div>
        <label class="form-label" for="mulai">Tanggal mulai</label>
        <input class="form-control" id="mulai" type="date" name="mulai" value="<?= esc($start, 'attr') ?>">
    </div>
    <div>
        <label class="form-label" for="selesai">Tanggal selesai</label>
        <input class="form-control" id="selesai" type="date" name="selesai" value="<?= esc($end, 'attr') ?>">
    </div>
    <button class="btn btn-primary" type="submit">Tampilkan</button>
    <a class="btn btn-outline-primary" href="<?= site_url('admin/laporan') ?>">Reset</a>
</form>

<div class="row g-3 mb-4 print-row">
    <div class="col-sm-4 print-col-4">
        <div class="stat-card">
            <span>Transaksi selesai</span>
            <strong><?= count($orders) ?></strong>
        </div>
    </div>
    <div class="col-sm-4 print-col-4">
        <div class="stat-card">
            <span>Produk terjual</span>
            <strong><?= $sold ?></strong>
        </div>
    </div>
    <div class="col-sm-4 print-col-4">
        <div class="stat-card">
            <span>Pendapatan</span>
            <strong class="report-money"><?= rupiah($revenue) ?></strong>
        </div>
    </div>
</div>

<div class="row g-4 print-stack">
    <div class="col-lg-7 print-col-12">
        <h2 class="admin-subheading">Daftar Transaksi Selesai</h2>
        <div class="admin-table table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Pembeli</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= esc($order['kode_order']) ?></td>
                            <td><?= date('d-m-Y', strtotime($order['created_at'])) ?></td>
                            <td><?= esc($order['nama_pembeli']) ?></td>
                            <td><?= rupiah($order['total']) ?></td>
                        </tr>
                    <?php endforeach ?>
                    <?php if (! $orders): ?>
                        <tr><td colspan="4" class="text-center p-4">Belum ada penjualan selesai.</td></tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-5 print-col-12">
        <h2 class="admin-subheading mt-print-4">Produk Terlaris (Top 10)</h2>
        <div class="admin-table table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Terjual</th>
                        <th>Omzet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bestProducts as $item): ?>
                        <tr>
                            <td><?= esc($item['nama_produk']) ?></td>
                            <td><?= (int) $item['terjual'] ?></td>
                            <td><?= rupiah($item['omzet']) ?></td>
                        </tr>
                    <?php endforeach ?>
                    <?php if (! $bestProducts): ?>
                        <tr><td colspan="3" class="text-center p-4">Belum ada data.</td></tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TTD PRINT ONLY -->
<div class="print-only-footer d-none d-print-block" style="margin-top: 50px; text-align: right;">
    <p>Ponorogo, <?= date('d F Y') ?></p>
    <br><br><br>
    <p><strong>Admin Toko</strong></p>
</div>

<?= $this->endSection() ?>