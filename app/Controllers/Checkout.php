<?php

namespace App\Controllers;

use App\Libraries\ShoppingCart;
use App\Models\{OrderModel, OrderItemModel};

class Checkout extends BaseController
{
    public function index()
    {
        $cart = (new ShoppingCart())->details();
        if ($cart['items'] === []) {
            return redirect()->to(site_url('keranjang'))->with('error', 'Keranjang masih kosong.');
        }
        return view('checkout/index', ['title' => 'Checkout', 'active' => 'keranjang'] + $cart);
    }

    public function store()
    {
        $cartService = new ShoppingCart();
        $cart = $cartService->details();
        if ($cart['items'] === []) {
            return redirect()->to(site_url('keranjang'))->with('error', 'Keranjang masih kosong.');
        }
        $input = $this->request->getPost();
        foreach (['nama_pembeli', 'whatsapp', 'alamat', 'catatan'] as $field) {
            if (isset($input[$field]) && is_string($input[$field])) { $input[$field] = trim($input[$field]); }
        }
        $rules = [
            'nama_pembeli' => ['label' => 'Nama lengkap', 'rules' => 'required|min_length[3]|max_length[120]'],
            'whatsapp' => ['label' => 'Nomor WhatsApp', 'rules' => 'required|regex_match[/^(?:\+62|62|0)8[0-9]{7,12}$/]'],
            'alamat' => ['label' => 'Alamat lengkap', 'rules' => 'required|min_length[10]|max_length[1000]'],
            'catatan' => ['label' => 'Catatan', 'rules' => 'permit_empty|max_length[500]'],
            'metode_pembayaran' => ['label' => 'Metode pembayaran', 'rules' => 'required|in_list[cod]'],
        ];
        if (! $this->validateData($input, $rules)) {
            return redirect()->to(site_url('checkout'))->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();
        $db->transBegin();
        try {
            $lockedItems = [];
            $total = 0;
            foreach ($cart['items'] as $item) {
                $product = $db->query('SELECT * FROM products WHERE id = ? FOR UPDATE', [$item['product']['id']])->getRowArray();
                if (! $product || $product['status_ketersediaan'] !== 'tersedia' || (int) $product['stok'] < $item['qty']) {
                    $remaining = $product ? (int) $product['stok'] : 0;
                    throw new \DomainException('Stok ' . $item['product']['nama_produk'] . ' tersisa ' . $remaining . '. Perbarui keranjang Anda.');
                }
                $subtotal = (int) $product['harga'] * $item['qty'];
                $lockedItems[] = ['product' => $product, 'qty' => $item['qty'], 'subtotal' => $subtotal];
                $total += $subtotal;
            }

            $code = 'BP-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $orderId = (new OrderModel())->insert([
                'kode_order' => $code, 'nama_pembeli' => $input['nama_pembeli'], 'whatsapp' => $input['whatsapp'],
                'alamat' => $input['alamat'], 'catatan' => $input['catatan'] ?: null, 'total' => $total,
                'metode_pembayaran' => 'cod', 'status_pembayaran' => 'belum_dibayar', 'status' => 'baru',
            ], true);
            $itemModel = new OrderItemModel();
            foreach ($lockedItems as $item) {
                $itemModel->insert([
                    'order_id' => $orderId, 'product_id' => $item['product']['id'], 'nama_produk' => $item['product']['nama_produk'],
                    'harga' => $item['product']['harga'], 'qty' => $item['qty'], 'subtotal' => $item['subtotal'],
                ]);
                $remaining = (int) $item['product']['stok'] - $item['qty'];
                $db->table('products')->where('id', $item['product']['id'])->update([
                    'stok' => $remaining,
                    'status_ketersediaan' => $remaining === 0 ? 'tidak_tersedia' : $item['product']['status_ketersediaan'],
                ]);
            }
            $db->transCommit();
        } catch (\DomainException $error) {
            $db->transRollback();
            return redirect()->to(site_url('keranjang'))->with('error', $error->getMessage());
        } catch (\Throwable $error) {
            $db->transRollback();
            log_message('error', 'Checkout gagal: {message}', ['message' => $error->getMessage()]);
            return redirect()->to(site_url('checkout'))->withInput()->with('error', 'Pesanan belum tersimpan. Silakan coba lagi.');
        }

        $cartService->clear();
        session()->setFlashdata('completed_order', ['code' => $code, 'name' => $input['nama_pembeli'], 'total' => $total, 'payment' => 'COD']);
        return redirect()->to(site_url('checkout/berhasil'));
    }

    public function success()
    {
        $order = session()->getFlashdata('completed_order');
        if (! is_array($order)) { return redirect()->to(site_url('katalog')); }
        return view('checkout/success', ['title' => 'Pesanan Berhasil', 'active' => 'keranjang', 'order' => $order]);
    }
}
