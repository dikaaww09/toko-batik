<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="container section-space">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="form-panel mt-4">
                <div class="text-center mb-4">
                    <h1 class="h3">Daftar Pelanggan Baru</h1>
                    <p class="text-muted">Buat akun untuk mempercepat proses checkout</p>
                </div>

                <form action="<?= site_url('register') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama') ?>" required maxlength="120">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="whatsapp" class="form-label">Nomor WhatsApp</label>
                            <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?= old('whatsapp') ?>" required inputmode="tel" placeholder="Contoh: 081234567890">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required minlength="6">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required maxlength="1000"><?= old('alamat') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Daftar Akun</button>

                    <div class="mt-4 text-center">
                        <p class="mb-0">Sudah punya akun? <a href="<?= site_url('login') ?>" class="text-link">Masuk di sini</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
