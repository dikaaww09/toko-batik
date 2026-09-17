<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php
$errors = session('errors') ?? [];
$value = static function ($field, $default = '') use ($product) {
    $v = old($field, $product[$field] ?? $default, false);
    return is_scalar($v) ? (string) $v : '';
};
$storedImage = (string) ($product['gambar'] ?? '');
$defaultSource = filter_var($storedImage, FILTER_VALIDATE_URL) ? 'url' : 'upload';
$imageSource = $value('image_source', $defaultSource);
$imageUrl = $value('gambar_url', $defaultSource === 'url' ? $storedImage : '');
?>
<a class="text-link" href="<?= site_url('admin/produk') ?>">← Kembali ke daftar produk</a>
<h1 class="mt-4 mb-4"><?= esc($title) ?></h1>
<form class="product-form" method="post" enctype="multipart/form-data" action="<?= site_url($product ? 'admin/produk/update/' . $product['id'] : 'admin/produk/simpan') ?>">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="form-panel">
                <h2>Informasi produk</h2>
                <?php foreach (['nama_produk' => ['Nama produk', 'text', 150, 3, ''], 'harga' => ['Harga (Rp)', 'number', 999999999999, 1, '1'], 'stok' => ['Stok', 'number', 999999, 0, '1']] as $field => [$label, $type, $max, $min, $step]): ?>
                    <div class="mb-4">
                        <label class="form-label" for="<?= $field ?>"><?= $label ?></label>
                        <input class="form-control <?= isset($errors[$field]) ? 'is-invalid' : '' ?>" id="<?= $field ?>" name="<?= $field ?>" type="<?= $type ?>" value="<?= esc($value($field, $field === 'stok' ? '10' : ''), 'attr') ?>" required min="<?= $min ?>" max="<?= $max ?>" <?= $step ? 'step="' . $step . '"' : '' ?> <?= isset($errors[$field]) ? 'aria-invalid="true" aria-describedby="error-' . $field . '"' : '' ?>>
                        <?php if (isset($errors[$field])): ?><div class="invalid-feedback" id="error-<?= $field ?>"><?= esc($errors[$field]) ?></div><?php endif ?>
                    </div>
                <?php endforeach ?>
                <div class="mb-4">
                    <label class="form-label" for="kategori_id">Kategori</label>
                    <select class="form-select" id="kategori_id" name="kategori_id" required>
                        <option value="">Pilih kategori</option>
                        <?php foreach ($categories as $category): ?><option value="<?= $category['id'] ?>" <?= (string) $value('kategori_id') === (string) $category['id'] ? 'selected' : '' ?>><?= esc($category['nama']) ?></option><?php endforeach ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="status_ketersediaan">Ketersediaan</label>
                    <select class="form-select" id="status_ketersediaan" name="status_ketersediaan" required>
                        <option value="tersedia" <?= $value('status_ketersediaan', 'tersedia') === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                        <option value="tidak_tersedia" <?= $value('status_ketersediaan') === 'tidak_tersedia' ? 'selected' : '' ?>>Tidak tersedia</option>
                    </select>
                    <p class="form-text">Stok 0 otomatis membuat produk tidak tersedia.</p>
                </div>
                <div>
                    <label class="form-label" for="deskripsi">Deskripsi produk</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="7" required minlength="10" maxlength="5000"><?= esc($value('deskripsi')) ?></textarea>
                    <p class="form-text">Jelaskan bahan, ukuran, dan detail produk. Maksimal 5.000 karakter.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-panel">
                <h2>Gambar utama</h2>
                <img id="image-preview" class="upload-preview" src="<?= esc(product_image($product['gambar'] ?? null), 'attr') ?>" alt="Pratinjau gambar produk" width="300" height="340">
                <fieldset class="mt-3">
                    <legend class="form-label">Sumber gambar</legend>
                    <div class="btn-group image-source-control w-100" role="group">
                        <input class="btn-check" type="radio" name="image_source" id="source-upload" value="upload" <?= $imageSource !== 'url' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-primary" for="source-upload">Unggah file</label>
                        <input class="btn-check" type="radio" name="image_source" id="source-url" value="url" <?= $imageSource === 'url' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-primary" for="source-url">URL gambar</label>
                    </div>
                </fieldset>
                <div class="mt-3" data-image-panel="upload">
                    <label class="form-label" for="gambar">Unggah gambar (opsional)</label>
                    <input class="form-control" type="file" name="gambar" id="gambar" accept=".jpg,.jpeg,.png,.webp" aria-describedby="image-help">
                    <p class="form-text" id="image-help">JPG, PNG, atau WebP. Maksimal 2 MB dan 3.000 × 3.000 px.</p>
                </div>
                <div class="mt-3" data-image-panel="url">
                    <label class="form-label" for="gambar_url">URL gambar</label>
                    <input class="form-control <?= isset($errors['gambar_url']) ? 'is-invalid' : '' ?>" type="url" name="gambar_url" id="gambar_url" value="<?= esc($imageUrl, 'attr') ?>" maxlength="255" placeholder="https://..." inputmode="url">
                    <?php if (isset($errors['gambar_url'])): ?><div class="invalid-feedback"><?= esc($errors['gambar_url']) ?></div><?php endif ?>
                    <p class="form-text">Gunakan URL gambar publik atau link berbagi Google Drive.</p>
                </div>
                <p id="image-status" class="small" role="status"></p>
            </div>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-3 mt-4">
        <button class="btn btn-primary" type="submit">Simpan Produk</button>
        <a class="btn btn-outline-primary" href="<?= site_url('admin/produk') ?>">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
