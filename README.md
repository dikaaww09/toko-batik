# Batik Pusaka

Website katalog batik untuk Uji Sertifikasi Kompetensi Junior Web Developer. Dibangun dengan **CodeIgniter 4.7.4, PHP ≥8.2, MySQL/MariaDB, Bootstrap 5.3.8, CSS, dan JavaScript vanilla**. Tidak membutuhkan Node.js untuk menjalankan aplikasi.

## Fitur

- Beranda, katalog dari database, pencarian nama, filter kategori, pagination, detail, Tentang, dan Kontak.
- Tautan WhatsApp dengan nama dan harga produk. Tombol nonaktif jika nomor belum dikonfigurasi.
- Login/logout admin, dashboard jumlah produk/kategori, CRUD produk, dan satu gambar utama.
- CSRF berbasis session, regenerasi session saat login, pembatasan percobaan login, filter admin, validasi server, escaping output, serta header keamanan.
- Unggahan JPG/JPEG, PNG, WebP maksimal 2 MB dan 3.000 × 3.000 px. Gambar dikodekan ulang dengan GD, nama acak, dan file lama dibersihkan setelah perubahan database berhasil. Tanpa gambar baru, gambar lama tetap digunakan.
- Ilustrasi batik SVG lokal merupakan aset buatan proyek, bukan unggahan admin. SVG tidak diterima sebagai unggahan. Semua aset Bootstrap disimpan lokal; tidak ada ketergantungan CDN saat aplikasi berjalan.

Tidak ada keranjang, checkout, pembayaran, registrasi pelanggan, pesanan, atau fitur penjualan lainnya. Kategori disediakan melalui seeder; CRUD kategori tidak ditambahkan karena tiga kategori sudah mencukupi tugas.

## Persyaratan

PHP 8.2 atau lebih baru, Composer 2, MySQL atau MariaDB, serta ekstensi `intl`, `mbstring`, `mysqli`, `fileinfo`, `gd`, `json`, dan `dom`/`xml` untuk pengujian. Aktifkan dukungan JPEG, PNG, dan WebP pada GD. `composer.lock` dipertahankan dengan target platform PHP 8.2.4.

```bash
php -v
php -m
composer install
composer check-platform-reqs
```

## Menjalankan di Mac dengan XAMPP

1. Buka XAMPP Manager, jalankan MySQL. Apache juga dijalankan jika memakai virtual host; `spark serve` cukup untuk server pengembangan.
2. Masuk ke direktori proyek. Untuk memakai PHP XAMPP:

   ```bash
   export PATH="/Applications/XAMPP/xamppfiles/bin:$PATH"
   php -v
   php -m
   ```

   **Catatan mesin saat implementasi:** PHP XAMPP 8.2.4 belum memiliki ekstensi `intl`. Aplikasi telah dijalankan memakai PHP Homebrew 8.4.12 yang memiliki ekstensi lengkap, dengan MariaDB dari XAMPP. Jika XAMPP Anda juga tidak memiliki `intl`, sediakan PHP yang mempunyai ekstensi tersebut; sekadar menambahkan `extension=intl` tidak cukup jika modulnya belum terpasang. Contoh menggunakan PHP Homebrew yang sudah tersedia: `/opt/homebrew/bin/php spark serve`. Atur PATH ke PHP tersebut saat menjalankan Composer. Penggunaan Apache XAMPP tetap membutuhkan ekstensi pada PHP Apache, bukan hanya PHP CLI.
3. Buat database dari phpMyAdmin (`http://localhost/phpmyadmin`) atau MySQL CLI:

   ```sql
   CREATE DATABASE toko_batik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. Salin konfigurasi jika `.env` belum ada:

   ```bash
   cp .env.example .env
   ```

   Jangan menimpa `.env` yang telah dikonfigurasi. Isikan:

   ```ini
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost:8080/'
   database.default.hostname = 127.0.0.1
   database.default.database = toko_batik
   database.default.username = root
   database.default.password = ''
   database.default.DBDriver = MySQLi
   database.default.port = 3306
   ```

   `root` tanpa password hanya contoh instalasi lokal XAMPP. Sesuaikan dengan akun database Anda. Jangan gunakan akun ini di hosting.
5. Jalankan:

   ```bash
   composer install
   php spark migrate
   php spark db:seed ShopSeeder
   php spark routes
   php spark serve --host 127.0.0.1 --port 8080
   ```

6. Buka [toko lokal](http://localhost:8080) dan [login admin](http://localhost:8080/admin/login). Gunakan host yang sama dengan `app.baseURL` agar session konsisten.

Jika menggunakan Apache, arahkan **DocumentRoot ke `public/`**, aktifkan `mod_rewrite`, serta `AllowOverride All` agar `.htaccess` berlaku. Jangan melayani root proyek sebagai direktori publik. Folder `writable/` dan `public/uploads/products/` harus dapat ditulis oleh pengguna PHP/Apache.

### Lingkungan lokal yang disiapkan saat implementasi

`.env` lokal memakai MariaDB XAMPP terisolasi pada `127.0.0.1:3307`, database `toko_batik`, akun khusus lokal `batik_dev`, dan data di `writable/local-mysql/`. Konfigurasi ini tidak masuk Git dan tidak mengubah database XAMPP yang sudah ada. Server PHP berjalan di port 8080. Untuk menjalankannya kembali setelah proses berhenti, dari root proyek:

```bash
/Applications/XAMPP/xamppfiles/sbin/mysqld --no-defaults \
  --basedir=/Applications/XAMPP/xamppfiles \
  --datadir="$PWD/writable/local-mysql" \
  --socket=/tmp/batik-pusaka-mysql.sock \
  --port=3307 --bind-address=127.0.0.1 \
  --pid-file="$PWD/writable/local-mysql/server.pid" \
  --log-error="$PWD/writable/local-mysql/server.log"
```

Di terminal lain:

```bash
/opt/homebrew/bin/php spark serve --host 127.0.0.1 --port 8080
```

Perintah khusus mesin ini hanya untuk melanjutkan lingkungan pengujian yang telah dibuat. Instalasi baru memakai langkah XAMPP standar di atas. Data lokal tidak ikut diunggah ke hosting.

## Admin development dan mengganti password

- Username: `admin`
- Password contoh development: `BatikDemo!2026`

Database menyimpan hasil `password_hash()`, autentikasi memakai `password_verify()`. Seeder dapat dijalankan ulang tanpa menggandakan sampel atau menimpa akun yang sudah ada.

Untuk mengganti password:

1. Isi `seed.adminPassword` dalam `.env` dengan password unik minimal 12 karakter. Jangan menaruh password asli dalam source code atau perintah shell.
2. Jalankan `php spark admin:password admin`.
3. Hapus nilai `seed.adminPassword` dari `.env` setelah selesai. Jangan menjalankan seeder lagi tanpa mengisi nilai tersebut.

Untuk instalasi baru, `seed.adminUsername` menentukan username awal. Seeder menolak password demo saat environment production. Perubahan password melalui CLI memang ditujukan untuk pemilik server; tidak ada halaman manajemen akun tambahan.

## Struktur database

Database `toko_batik` menggunakan migration sebagai sumber struktur:

| Tabel | Kolom utama |
| --- | --- |
| `admins` | `id`, `username` unik, `password` hash, `created_at`, `updated_at` |
| `categories` | `id`, `nama` unik |
| `products` | `id`, `nama_produk`, `slug` unik, `kategori_id`, `harga` DECIMAL(12,0), `deskripsi`, `gambar`, `status_ketersediaan`, `created_at`, `updated_at` |
| `migrations` | Riwayat migration, dikelola CodeIgniter |

Relasi `products.kategori_id` ke `categories.id` memakai foreign key. Kategori yang masih dipakai tidak dapat dihapus. Status adalah `tersedia` atau `tidak_tersedia`; tidak ada penghitungan stok. Slug dibuat otomatis dengan suffix acak dan dipertahankan saat edit supaya URL tetap stabil.

Seeder menghasilkan satu admin, tiga kategori (Kain Batik, Kemeja Batik, Blus Batik), dan enam produk contoh. Semua harga, deskripsi, dan ilustrasi sampel harus ditinjau sebelum publikasi. Tidak ada klaim penjualan atau testimoni.

## File utama

```text
app/Config/{Routes,Filters,Security,Shop,Pager}.php
app/Controllers/{Home,Catalog,Auth}.php
app/Controllers/Admin/{Dashboard,Products}.php
app/Models/{Admin,Category,Product}Model.php
app/Filters/AuthFilter.php
app/Libraries/ProductImages.php
app/Helpers/shop_helper.php
app/Commands/AdminPassword.php
app/Database/Migrations/2026-09-07-000001_CreateShop.php
app/Database/Seeds/ShopSeeder.php
app/Views/{layouts,partials,home,catalog,auth,admin,pagers}/
app/Language/id/Validation.php
public/assets/{css,js,images,vendor}/
public/uploads/products/.htaccess
.env.example
tests/http_smoke.py
tests/browser.cjs
tests/unit/ShopSecurityTest.php
```

## Placeholder yang harus diganti

Di `.env`:

- `shop.whatsapp`: nomor internasional tanpa `+`, spasi, atau tanda baca. Kosong berarti tombol pemesanan nonaktif. Pesan otomatis: `Halo, saya tertarik dengan produk [nama produk] seharga [harga]. Apakah produk ini masih tersedia?`
- `shop.email`: ganti `ganti-email@example.com` dengan alamat toko.
- `shop.portfolioURL`: URL HTTPS portofolio Vercel; kosong berarti link tidak ditampilkan.
- `app.baseURL`: alamat toko beserta garis miring penutup.
- Kredensial `database.default.*`, environment, dan akun admin sesuai server.

Alamat kontak hanya Ponorogo, Jawa Timur. Jam operasional tidak ditampilkan karena belum tersedia. Ganti ilustrasi contoh dengan foto asli melalui admin, lalu perbarui deskripsi dan harga.

## Upload ke hosting PHP–MySQL

1. Pilih hosting yang menyediakan PHP ≥8.2 dengan ekstensi di atas dan MySQL/MariaDB. Buat database dan akun khusus melalui panel hosting; nama database mungkin mendapat prefix akun.
2. Siapkan cadangan, tentukan domain/subdomain toko, dan aktifkan sertifikat HTTPS.
3. Jalankan `composer install --no-dev --optimize-autoloader` pada salinan deployment atau melalui SSH hosting. Jika hosting tidak menyediakan Composer, unggah juga `vendor/` hasil instalasi production. Jangan menjalankan `composer update` di hosting.
4. Unggah `app/`, `public/`, `vendor/`, `writable/` (direktori kosong yang diperlukan), `spark`, `composer.json`, dan `composer.lock`. **Jangan unggah** `.git`, `tests`, `build`, log/session lokal, `.env` development, atau `writable/local-mysql`.
5. Arahkan document root domain ke folder `public/`. `app/`, `vendor/`, `.env`, dan `writable/` harus berada di luar document root. Jika panel tidak mengizinkan ini, minta penyedia mengaturnya atau gunakan hosting yang mendukung; jangan membuka root proyek ke internet.
6. Buat `.env` pada server:

   ```ini
   CI_ENVIRONMENT = production
   app.baseURL = 'https://GANTI-DOMAIN-TOKO/'
   app.indexPage = ''
   app.forceGlobalSecureRequests = true
   cookie.secure = true
   database.default.hostname = GANTI-HOST-DATABASE
   database.default.database = GANTI-NAMA-DATABASE
   database.default.username = GANTI-USER-DATABASE
   database.default.password = 'GANTI-PASSWORD-DATABASE'
   database.default.port = 3306
   database.default.DBDebug = false
   ```

   Isi kontak dan URL portofolio. Lindungi `.env` agar hanya pengguna server yang perlu dapat membacanya.
7. Untuk instalasi kosong, jalankan `php spark migrate`, isi kredensial admin unik melalui `seed.adminUsername`/`seed.adminPassword`, lalu `php spark db:seed ShopSeeder`. Hapus nilai password seeder setelah selesai. Bila memakai data lokal, gunakan prosedur export/import di bawah dan ganti password demo sebelum dibuka ke publik.
8. Berikan hak tulis ke `writable/` dan `public/uploads/products/` sesuai pengguna PHP. Umumnya direktori 750/755 dan file 640/644, tetapi kepemilikan/grup harus disesuaikan hosting. Hindari permission 777.
9. Pada Apache pastikan `.htaccess` disalin, `mod_rewrite` aktif, dan directory listing mati. Pada Nginx, aturan `.htaccess` tidak berlaku: gunakan `try_files $uri $uri/ /index.php?$query_string;`, `autoindex off;`, hanya eksekusi `public/index.php` sebagai PHP, dan pastikan direktori uploads hanya menyajikan file statis.
10. Periksa login/logout, seluruh CRUD, unggahan, HTTPS, route admin tanpa login, serta akses `.env`/folder privat dari browser. Pengujian Apache/Nginx dan hosting sesungguhnya tetap harus dilakukan di server tujuan.
11. Di website portofolio Vercel, isi href tombol **Kunjungi Website Toko** dengan URL HTTPS toko. Portofolio tetap di Vercel; aplikasi PHP dan database berada di hosting PHP–MySQL.

Belum ada deployment atau perubahan pada portofolio dilakukan oleh proyek ini.

### Export database lokal dan import hosting

Migration tetap sumber struktur. SQL export tidak disimpan dalam repository karena berisi data termasuk hash akun.

- **phpMyAdmin:** pilih `toko_batik`, Export format SQL; di hosting pilih database tujuan lalu Import. Sertakan tabel `migrations` jika memindahkan seluruh database.
- **CLI:** gunakan password interaktif melalui `-p`, bukan menuliskannya di command:

  ```bash
  mysqldump -h 127.0.0.1 -u USER_LOKAL -p --single-transaction toko_batik > toko_batik.sql
  mysql -h HOST_DATABASE -u USER_HOSTING -p DATABASE_HOSTING < toko_batik.sql
  ```

  Sesuaikan `-P 3307` jika menggunakan database pengujian lokal yang dibuat di mesin ini. Simpan export di luar repository, hapus salinan publik, dan jangan import menimpa database berisi data penting tanpa cadangan.

- Salin juga gambar dari `public/uploads/products/`; SQL saja tidak memindahkan file gambar. Ilustrasi sampel sudah ada di `public/assets/images/`.
- Setelah import, jalankan `php spark migrate` untuk migration yang belum diterapkan. Jangan menjalankan seeder pada data produksi hanya untuk memperbarui password; gunakan `admin:password`.

## Pengujian

```bash
composer install
php spark migrate
php spark db:seed ShopSeeder
php spark routes
vendor/bin/phpunit --no-coverage
python3 tests/http_smoke.py
```

`http_smoke.py` menggunakan Python standard library dan server yang sudah berjalan. Skrip hanya mengizinkan localhost/127.0.0.1, membutuhkan enam data sampel, serta membuat/menghapus produk uji sendiri. Jalankan pada database development, bukan data penting. Kredensial dapat diatur melalui `TEST_ADMIN_USERNAME` dan `TEST_ADMIN_PASSWORD`; URL melalui `TEST_BASE_URL`. Pembatasan login lima percobaan per menit dapat membuat pengulangan tes terlalu cepat ditolak.

Pengujian browser menggunakan Playwright sebagai **alat pengujian terpisah**, bukan backend atau dependensi aplikasi. Siapkan Playwright dan Chromium di lingkungan alat Anda, kemudian:

```bash
PLAYWRIGHT_MODULE=/path/ke/node_modules/playwright node tests/browser.cjs
```

Skrip memeriksa 320, 375, 768, 1024, dan 1440 px, halaman publik/admin, navbar, form, dialog hapus, reduced motion, console, dan request aset. Screenshot disimpan di `build/screenshots/` yang diabaikan Git. Alur ini juga diverifikasi secara langsung menggunakan agent-browser. Lihat [hasil pengujian aktual](docs/TESTING.md).

## Referensi dan lisensi

- [Dokumentasi filter CodeIgniter](https://codeigniter.com/user_guide/incoming/filters.html) dan [CSRF/session](https://codeigniter.com/user_guide/libraries/security.html).
- [Bootstrap 5.3](https://getbootstrap.com/docs/5.3/getting-started/introduction/); lisensi MIT disertakan di `public/assets/vendor/bootstrap/LICENSE`.
- Ilustrasi SVG dan CSS khusus dibuat untuk proyek ini. Tidak ada gambar pelanggan, testimoni, atau data pribadi yang dikarang.
