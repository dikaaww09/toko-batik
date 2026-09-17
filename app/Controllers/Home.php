<?php
namespace App\Controllers;
use App\Models\ProductModel;
class Home extends BaseController
{
    public function index(): string
    {
        return view('home/index', ['title' => 'Warisan dalam setiap corak', 'active' => 'beranda', 'products' => (new ProductModel())->withCategory()->orderBy("CASE WHEN products.stok = 0 OR products.status_ketersediaan = 'tidak_tersedia' THEN 1 ELSE 0 END", '', false)->orderBy('products.created_at', 'DESC')->orderBy('products.id', 'ASC')->findAll(4)]);
    }
    public function about(): string
    {
        return view('home/about', ['title' => 'Tentang Kami', 'active' => 'tentang']);
    }
    public function contact(): string
    {
        return view('home/contact', ['title' => 'Hubungi Kami', 'active' => 'kontak']);
    }
}
