<?php
namespace App\Controllers;
use App\Models\{ProductModel, CategoryModel};
use CodeIgniter\Exceptions\PageNotFoundException;
class Catalog extends BaseController
{
    public function index(): string
    {
        $raw = $this->request->getGet('q');
        $q = is_string($raw) ? mb_substr(trim($raw), 0, 150) : '';
        $rawCategory = $this->request->getGet('kategori');
        $category = is_scalar($rawCategory) ? max(0, (int) $rawCategory) : 0;
        $model = (new ProductModel())->withCategory();
        if ($q !== '') { $model->like('nama_produk', $q); }
        if ($category > 0) { $model->where('kategori_id', $category); }
        return view('catalog/index', [
            'title' => 'Koleksi Batik', 'active' => 'katalog', 'q' => $q, 'category' => $category,
            'categories' => (new CategoryModel())->findAll(),
            'products' => $model->orderBy('products.id', 'DESC')->paginate(9), 'pager' => $model->pager,
        ]);
    }
    public function show(string $slug): string
    {
        $product = (new ProductModel())->withCategory()->where('slug', $slug)->first();
        if (! $product) { throw PageNotFoundException::forPageNotFound(); }
        return view('catalog/show', ['title' => $product['nama_produk'], 'active' => 'katalog', 'product' => $product]);
    }
}
