<?php
namespace App\Models;
use CodeIgniter\Model;
class ProductModel extends Model
{
    protected $table = 'products';
    protected $allowedFields = ['nama_produk', 'slug', 'kategori_id', 'harga', 'deskripsi', 'gambar', 'status_ketersediaan'];
    protected $useTimestamps = true;

    public function withCategory(): self
    {
        return $this->select('products.*, categories.nama AS kategori')->join('categories', 'categories.id = products.kategori_id');
    }
}
