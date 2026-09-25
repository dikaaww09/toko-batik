<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="container section-space">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="form-panel mt-4">
                <div class="text-center mb-4">
                    <h1 class="h3">Masuk Pelanggan</h1>
                    <p class="text-muted">Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                <form action="<?= site_url('login') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Masuk</button>

                    <div class="mt-4 text-center">
                        <p class="mb-0">Belum punya akun? <a href="<?= site_url('register') ?>" class="text-link">Daftar sekarang</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
