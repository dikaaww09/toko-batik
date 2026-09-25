<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="container section-space">
    <div class="page-heading">
        <p class="eyebrow">DASHBOARD PELANGGAN</p>
        <h1>Akun Saya</h1>
        <p>Kelola profil dan lihat riwayat pesanan Anda.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Profil Saya</h2>
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Nama</dt>
                        <dd class="col-sm-8 fw-medium"><?= esc($customer['nama']) ?></dd>

                        <dt class="col-sm-4 text-muted">Email</dt>
                        <dd class="col-sm-8"><?= esc($customer['email']) ?></dd>

                        <dt class="col-sm-4 text-muted">WhatsApp</dt>
                        <dd class="col-sm-8"><?= esc($customer['whatsapp']) ?></dd>

                        <dt class="col-sm-4 text-muted">Alamat</dt>
                        <dd class="col-sm-8"><?= esc($customer['alamat']) ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Riwayat Pesanan</h2>

                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5 text-muted">
                            <p>Belum ada riwayat pesanan.</p>
                            <a href="<?= site_url('katalog') ?>" class="btn btn-outline-primary mt-2">Belanja Sekarang</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Kode Pesanan</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status Pesanan</th>
                                        <th>Status Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><strong><?= esc($order['kode_order']) ?></strong></td>
                                        <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                        <td><?= rupiah($order['total']) ?></td>
                                        <td>
                                            <?php
                                            $statusBadges = [
                                                'baru' => 'bg-info',
                                                'diproses' => 'bg-primary',
                                                'dikirim' => 'bg-warning text-dark',
                                                'selesai' => 'bg-success',
                                                'dibatalkan' => 'bg-danger'
                                            ];
                                            $badgeClass = $statusBadges[$order['status']] ?? 'bg-secondary';
                                            $statusLabels = [
                                                'baru' => 'Baru',
                                                'diproses' => 'Diproses',
                                                'dikirim' => 'Dikirim',
                                                'selesai' => 'Selesai',
                                                'dibatalkan' => 'Dibatalkan'
                                            ];
                                            $statusText = $statusLabels[$order['status']] ?? $order['status'];
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= esc($statusText) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($order['status_pembayaran'] === 'sudah_dibayar'): ?>
                                                <span class="text-success"><i class="bi bi-check-circle"></i> Lunas</span>
                                            <?php else: ?>
                                                <span class="text-warning"><i class="bi bi-clock"></i> Belum Dibayar</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
