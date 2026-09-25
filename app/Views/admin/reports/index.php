<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<!-- PRINT ONLY HEADER (KOP SURAT) -->
<div class="print-only-header d-none d-print-block">
    <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px;">
        <h1 style="font-size: 28px; font-family: 'Times New Roman', serif; margin: 0;">BATIK PUSAKA</h1>
        <p style="margin: 0; font-size: 14px;">CORAK NUSANTARA</p>
        <p style="margin: 5px 0 0 0; font-size: 14px; color: #555;">Ponorogo, Jawa Timur | Telp: 081234567890</p>
        <h2 style="font-size: 18px; margin: 20px 0 5px 0; text-transform: uppercase;">Laporan Penjualan</h2>
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

<div class="admin-table table-responsive mb-4">
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
<div class="print-only-footer d-none d-print-block" style="margin-top: 50px; text-align: right;">
    <p>Ponorogo, <?= date('d F Y') ?></p>
    <br><br><br>
    <p><strong>Admin Toko</strong></p>
</div>

<style>
@media print {
    .table-bordered { border: 1px solid #ddd !important; }
    .table-bordered th, .table-bordered td { border: 1px solid #ddd !important; }
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
