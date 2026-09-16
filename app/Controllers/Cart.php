<?php

namespace App\Controllers;

use App\Libraries\ShoppingCart;
use App\Models\ProductModel;

class Cart extends BaseController
{
    public function index(): string
    {
        return view('cart/index', ['title' => 'Keranjang', 'active' => 'keranjang'] + (new ShoppingCart())->details());
    }

    public function add(int $id)
    {
        $product = (new ProductModel())->find($id);
        if (! $product || $product['status_ketersediaan'] !== 'tersedia') {
            return redirect()->back()->with('error', 'Produk tidak tersedia untuk ditambahkan.');
        }
        $cart = new ShoppingCart();
        $quantity = max(1, min(99, (int) $this->request->getPost('qty')));
        $cart->set($id, ($cart->quantities()[$id] ?? 0) + $quantity);
        return redirect()->to(site_url('keranjang'))->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update()
    {
        $quantities = $this->request->getPost('qty');
        if (is_array($quantities)) {
            $cart = new ShoppingCart();
            foreach ($cart->quantities() as $id => $_) {
                if (array_key_exists($id, $quantities) && is_scalar($quantities[$id])) {
                    $cart->set((int) $id, max(0, min(99, (int) $quantities[$id])));
                }
            }
        }
        return redirect()->to(site_url('keranjang'))->with('success', 'Keranjang diperbarui.');
    }

    public function remove(int $id)
    {
        (new ShoppingCart())->set($id, 0);
        return redirect()->to(site_url('keranjang'))->with('success', 'Produk dihapus dari keranjang.');
    }
}
