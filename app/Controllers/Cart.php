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
        if (! $product || $product['status_ketersediaan'] !== 'tersedia' || (int) $product['stok'] < 1) {
            return redirect()->back()->with('error', 'Produk tidak tersedia untuk ditambahkan.');
        }
        $cart = new ShoppingCart();
        $quantity = max(1, min(99, (int) $this->request->getPost('qty')));
        $newQuantity = ($cart->quantities()[$id] ?? 0) + $quantity;
        if ($newQuantity > (int) $product['stok']) {
            return redirect()->back()->with('error', 'Jumlah melebihi stok. Stok ' . $product['nama_produk'] . ' tersisa ' . $product['stok'] . '.');
        }
        $cart->set($id, $newQuantity);
        $returnTo = (string) $this->request->getPost('return_to');
        if (! preg_match('#^katalog(?:/[a-z0-9-]+)?$#D', $returnTo)) { $returnTo = 'katalog'; }
        return redirect()->to(site_url($returnTo))->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update()
    {
        $quantities = $this->request->getPost('qty');
        if (is_array($quantities)) {
            $cart = new ShoppingCart();
            foreach ($cart->quantities() as $id => $_) {
                if (array_key_exists($id, $quantities) && is_scalar($quantities[$id])) {
                    $product = (new ProductModel())->find((int) $id);
                    if (! $product || (int) $product['stok'] < 1) {
                        $cart->set((int) $id, 0);
                        continue;
                    }
                    $quantity = max(1, min(99, (int) $quantities[$id]));
                    $cart->set((int) $id, min($quantity, (int) $product['stok']));
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
