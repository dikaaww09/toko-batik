<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<!-- HEADER UNTUK PRINT (KOP SURAT) -->
<div class="print-only-header d-none d-print-block">
    <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px;">
        <h1 style="font-size: 28px; font-family: 'Times New Roman', serif; margin: 0;">BATIK PUSAKA</h1>
        <p style="margin: 0; font-size: 14px;">CORAK NUSANTARA</p>
        <p style="margin: 5px 0 0 0; font-size: 14px; color: #555;">Ponorogo, Jawa Timur | Telp: 081234567890</p>
        <h2 style="font-size: 18px; margin: 20px 0 5px 0; text-transform: uppercase;">Laporan Penjualan Harian</h2>
        <table style="font-size: 14px; margin: 15px auto 0; text-align: left;">
            <tr>
                <td style="padding-right: 15px;">Periode</td>
                <td>: <?= $start ? date('d/m/Y', strtotime($start)) : 'Awal' ?> s.d <?= $end ? date('d/m/Y', strtotime($end)) : 'Sekarang' ?></td>
            </tr>
            <tr>
                <td style="padding-right: 15px;">Toko/Outlet</td>
                <td>: Semua (Online)</td>
            </tr>
        </table>
    </div>
</div>

<!-- HEADER UNTUK WEB -->
<div class="section-heading no-print d-print-none">
    <div>
        <p class="eyebrow">RINGKASAN TOKO</p>
        <h1>Laporan penjualan</h1>
        <p class="text-muted mb-0">Hanya pesanan berstatus selesai yang dihitung.</p>
    </div>
    <button class="btn btn-outline-primary no-print" type="button" onclick="window.print()">Cetak Laporan</button>
</div>

<!-- FILTER UNTUK WEB -->
<form class="form-panel report-filter no-print mb-4 d-print-none" method="get">
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


<!-- ========================================== -->
<!-- BAGIAN 1: TAMPILAN KHUSUS WEB (D-PRINT-NONE) -->
<!-- ========================================== -->
<div class="d-print-none">
    <!-- STAT CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="stat-card">
                <span>Transaksi selesai</span>
                <strong><?= count($orders) ?></strong>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card">
                <span>Produk terjual</span>
                <strong><?= $sold ?></strong>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card">
                <span>Pendapatan</span>
                <strong class="report-money"><?= rupiah($revenue) ?></strong>
            </div>
        </div>
    </div>

    <!-- TABEL TRANSAKSI & TOP PRODUK -->
    <div class="row g-4">
        <div class="col-lg-7">
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
                                <td><a href="<?= site_url('admin/pesanan/' . $order['id']) ?>"><strong><?= esc($order['kode_order']) ?></strong></a></td>
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
        <div class="col-lg-5">
            <h2 class="admin-subheading">Produk Terlaris (Top 10)</h2>
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
</div>


<!-- ============================================== -->
<!-- BAGIAN 2: TAMPILAN KHUSUS CETAK (D-NONE D-PRINT-BLOCK) -->
<!-- ============================================== -->
<div class="d-none d-print-block">
    <div class="admin-table mb-4" style="border: none !important;">
        <table class="table mb-0 table-bordered">
            <thead style="background-color: #8c0000; color: white;">
                <tr>
                    <th class="text-center" style="width: 50px; background-color: #8c0000; color: white;">No</th>
                    <th style="background-color: #8c0000; color: white;">Tanggal</th>
                    <th style="background-color: #8c0000; color: white;">No. Invoice</th>
                    <th style="background-color: #8c0000; color: white;">Produk</th>
                    <th class="text-center" style="background-color: #8c0000; color: white;">Qty</th>
                    <th style="background-color: #8c0000; color: white;">Harga Satuan</th>
                    <th style="background-color: #8c0000; color: white;">Total</th>
                    <th style="background-color: #8c0000; color: white;">Metode Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($reportItems as $item): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
                        <td><?= esc($item['kode_order']) ?></td>
                        <td><?= esc($item['nama_produk']) ?></td>
                        <td class="text-center"><?= (int) $item['qty'] ?></td>
                        <td><?= rupiah($item['harga']) ?></td>
                        <td><?= rupiah($item['subtotal']) ?></td>
                        <td><?= strtoupper(str_replace('_', ' ', esc($item['metode_pembayaran']))) ?></td>
                    </tr>
                <?php endforeach ?>
                <?php if (! $reportItems): ?>
                    <tr><td colspan="8" class="text-center p-4">Belum ada penjualan selesai.</td></tr>
                <?php else: ?>
                    <tr style="background-color: #fffaf5; font-weight: bold;">
                        <td colspan="4" class="text-center">TOTAL</td>
                        <td class="text-center"><?= $totalQty ?></td>
                        <td></td>
                        <td colspan="2"><?= rupiah($totalRevenue) ?></td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>

    <!-- TTD PRINT ONLY -->
    <div style="margin-top: 50px; text-align: right;">
        <p>Ponorogo, <?= date('d F Y') ?></p>
        <br><br><br>
        <p><strong>Admin Toko</strong></p>
    </div>
</div>

<style>
@media print {
    /* Pastikan warna tabel tetap solid */
    .table-bordered { border: 1px solid #ddd !important; }
    .table-bordered th, .table-bordered td { border: 1px solid #ddd !important; padding: 8px !important; }
    th {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
        background-color: #8c0000 !important;
        color: white !important;
    }
    tr[style*="background-color"] {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
}
</style>

<?= $this->endSection() ?>