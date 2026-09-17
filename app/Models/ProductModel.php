<?php
namespace App\Models;
use CodeIgniter\Model;
class ProductModel extends Model
{
    protected $table = 'products';
    protected $allowedFields = ['nama_produk', 'slug', 'kategori_id', 'harga', 'stok', 'deskripsi', 'gambar', 'status_ketersediaan'];
    protected $beforeInsert = ['normalizeAvailability'];
    protected $beforeUpdate = ['normalizeAvailability'];

    protected function normalizeAvailability(array $data): array
    {
        if (array_key_exists('stok', $data['data'] ?? []) && (int) $data['data']['stok'] === 0) {
            $data['data']['status_ketersediaan'] = 'tidak_tersedia';
        }

        return $data;
    }
    protected $useTimestamps = true;

    public function withCategory(): self
    {
        return $this->select('products.*, categories.nama AS kategori')->join('categories', 'categories.id = products.kategori_id');
    }
}
