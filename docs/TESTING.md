# Hasil pengujian aktual

Tanggal: 7 September 2026. Lingkungan: macOS, PHP CLI 8.4.12, CodeIgniter 4.7.4, MariaDB XAMPP pada 127.0.0.1:3307, Chromium desktop dengan viewport emulasi. Database `toko_batik` dibuat di direktori pengembangan terisolasi. Tidak ada deployment.

## Hasil

| Pemeriksaan | Hasil |
| --- | --- |
| `composer install`, `composer validate --strict`, `composer check-platform-reqs` | Lulus pada PHP 8.4.12 |
| `php spark migrate` | Tiga tabel aplikasi dan tabel riwayat migration dibuat; pengulangan tidak mengubah struktur |
| `php spark db:seed ShopSeeder` | Satu admin, tiga kategori, enam produk; pengulangan tidak menggandakan data |
| `php spark admin:password admin` | Berhasil memperbarui hash dari konfigurasi development |
| `php spark routes` | 15 route sesuai kebutuhan; route admin memakai filter `auth`, mutasi hanya POST |
| `php spark serve --host 127.0.0.1 --port 8080` | Berjalan; toko dapat dibuka melalui localhost:8080 |
| Sintaks PHP | 88 file lulus `php -l` pada PHP 8.4.12 dan PHP XAMPP 8.2.4 |
| PHPUnit | 8 tes, 17 assertion lulus, mencakup tes bawaan scaffold dan tiga tes keamanan khusus proyek |
| HTTP `tests/http_smoke.py` | 74 pemeriksaan lulus |
| Chromium `tests/browser.cjs` | 126 pemeriksaan lulus; tanpa console error atau respons HTTP gagal selama alur browser |
| agent-browser | Beranda/katalog dimuat, elemen navigasi terdeteksi, screenshot diambil, console/error kosong |
| `git diff --check` | Lulus setelah whitespace bawaan robots.txt dirapikan |
| Git ignore | `.env`, file upload pengguna, database lokal, dan hasil tes diabaikan |

## Cakupan HTTP

- Halaman publik, aset yang direferensikan, redirect seluruh GET admin sebelum login.
- CSRF hilang menghasilkan HTTP 403; pesan login salah bersifat umum; login sukses meregenerasi session.
- Cache-Control no-store pada admin dan X-Content-Type-Options nosniff.
- Pencarian nama, filter kategori, kombinasi pencarian/filter, hasil kosong, query array, string SQL injection, dan escaping XSS.
- Produk tidak ditemukan menghasilkan 404; placeholder WhatsApp menonaktifkan pemesanan.
- Harga negatif, kategori yang tidak ada, dan nama whitespace ditolak.
- SVG, PHP yang menyamar sebagai gambar, JPEG palsu, serta file >2 MB ditolak.
- PNG valid diterima dan menggunakan nama acak. Edit tanpa gambar mempertahankan gambar sebelumnya. Penggantian menghapus file lama setelah berhasil.
- Deskripsi ter-escape, harga dan status dinamis, GET hapus tidak tersedia. Hapus POST menghapus data dan gambar.
- Pagination publik/admin diuji dengan lima produk sementara; semua fixture dibersihkan.
- Logout dan penolakan akses menggunakan session setelah logout.

## Cakupan browser

Enam halaman publik (termasuk login dan detail) serta empat halaman admin diuji pada lebar **320, 375, 768, 1024, dan 1440 px**. Pengukuran `document.documentElement.scrollWidth <= innerWidth` lulus di seluruh kombinasi. Tabel admin menggunakan area gulir internal untuk kolomnya, bukan memperlebar halaman.

Menu mobile awalnya tertutup, terbuka lewat hamburger, kemudian tertutup setelah memilih tautan. Form pencarian/filter, link detail, login, tambah/edit/hapus produk, pembatalan dialog hapus, pratinjau unggahan, dan logout diuji melalui browser. Reduced motion serta skip link keyboard juga diperiksa. Nama produk sangat panjang juga disimulasikan melalui agent-browser pada 320 px: lebar konten tetap 320 px. Screenshot home dan form admin tersedia di `build/screenshots/`, termasuk 320/375 px yang telah ditinjau visual.

Overflow awal di halaman Tentang pada 320 px disebabkan gutter Bootstrap `g-5`. Gutter horizontal pada HP disesuaikan; pengujian lengkap sesudah perbaikan lulus. Fixture PNG awal pada skrip HTTP rusak dan ditolak GD; diganti PNG valid sebelum pengujian akhir.

## Batas verifikasi

- PHP Apache XAMPP 8.2.4 pada mesin ini belum mempunyai `intl`; runtime Apache XAMPP belum diverifikasi. Runtime yang lulus adalah PHP 8.4.12 dengan MariaDB XAMPP.
- `.htaccess` tidak diproses oleh `spark serve`. Aturan Apache, konfigurasi Nginx, SSL, permission hosting, export/import pada hosting, dan mode production online perlu diuji di server tujuan.
- Tidak mengirim pesan WhatsApp atau membuka percakapan ke nomor asli. Penyusunan URL/pesan, encoding nama/harga, serta penolakan nomor tidak valid diuji secara lokal melalui PHPUnit.
- Viewport Chromium merupakan emulasi, bukan pengujian perangkat fisik Safari/iOS/Android atau audit screen reader lengkap.
- Foto, detail produk, kontak asli, URL portofolio, dan kredensial hosting belum diberikan. Sampel dan placeholder tetap ditandai.
