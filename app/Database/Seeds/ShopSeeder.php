<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
use App\Models\{AdminModel, CategoryModel, ProductModel};
class ShopSeeder extends Seeder
{
    public function run()
    {
        $username = (string) env('seed.adminUsername', 'admin');
        $password = (string) env('seed.adminPassword', '');
        if (strlen($password) < 12 || (ENVIRONMENT === 'production' && $password === 'BatikDemo!2026')) {
            throw new \RuntimeException('Isi seed.adminPassword dengan password unik minimal 12 karakter. Password demo dilarang di production.');
        }
        $admins = new AdminModel();
        if (! $admins->where('username', $username)->first()) {
            $admins->insert(['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
        }
        $categories = new CategoryModel();
        $ids = [];
        foreach (['Kain Batik', 'Kemeja Batik', 'Blus Batik'] as $name) {
            $row = $categories->where('nama', $name)->first();
            $ids[$name] = $row ? $row['id'] : $categories->insert(['nama' => $name]);
        }
        $products = new ProductModel();
        $samples = [
            ['Kain Batik Kawung Sogan', 'kain-batik-kawung-sogan', 'Kain Batik', 185000],
            ['Kemeja Batik Parang Bumi', 'kemeja-batik-parang-bumi', 'Kemeja Batik', 245000],
            ['Blus Batik Sekar Krem', 'blus-batik-sekar-krem', 'Blus Batik', 225000],
            ['Kain Batik Lereng Terakota', 'kain-batik-lereng-terakota', 'Kain Batik', 195000],
            ['Kemeja Batik Kawung Malam', 'kemeja-batik-kawung-malam', 'Kemeja Batik', 265000],
            ['Blus Batik Puspa Tanah', 'blus-batik-puspa-tanah', 'Blus Batik', 235000],
        ];
        foreach ($samples as $index => [$name, $slug, $category, $price]) {
            if (! $products->where('slug', $slug)->first()) {
                $products->insert([
                    'nama_produk' => $name, 'slug' => $slug, 'kategori_id' => $ids[$category], 'harga' => $price,
                    'deskripsi' => 'Produk contoh untuk katalog Batik Pusaka. Perpaduan corak batik dan warna hangat untuk melengkapi gaya Anda. Hubungi kami untuk menanyakan bahan, ukuran, dan ketersediaan. Gambar berupa ilustrasi; ganti dengan foto serta spesifikasi produk asli sebelum toko dipublikasikan.',
                    'gambar' => 'sample-' . ($index + 1) . '.svg',
                    'status_ketersediaan' => $index === 5 ? 'tidak_tersedia' : 'tersedia',
                ]);
            }
        }
    }
}
