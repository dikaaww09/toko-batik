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
            if (isset($input[$field]) && is_string($input[$field])) {
                $input[$field] = trim($input[$field]);
            }
        }
        $rules = [
            'nama_pembeli' => ['label' => 'Nama lengkap', 'rules' => 'required|min_length[3]|max_length[120]'],
            'whatsapp' => ['label' => 'Nomor WhatsApp', 'rules' => 'required|regex_match[/^(?:\+62|62|0)8[0-9]{7,12}$/]'],
            'alamat' => ['label' => 'Alamat lengkap', 'rules' => 'required|min_length[10]|max_length[1000]'],
            'catatan' => ['label' => 'Catatan', 'rules' => 'permit_empty|max_length[500]'],
        ];
        if (! $this->validateData($input, $rules)) {
            return redirect()->to(site_url('checkout'))->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();
        $db->transStart();
        $orderModel = new OrderModel();
        $code = 'BP-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $orderId = $orderModel->insert([
            'kode_order' => $code,
            'nama_pembeli' => $input['nama_pembeli'],
            'whatsapp' => $input['whatsapp'],
            'alamat' => $input['alamat'],
            'catatan' => $input['catatan'] ?: null,
            'total' => $cart['total'],
            'status' => 'baru',
        ], true);
        $itemModel = new OrderItemModel();
        foreach ($cart['items'] as $item) {
            $itemModel->insert([
                'order_id' => $orderId,
                'product_id' => $item['product']['id'],
                'nama_produk' => $item['product']['nama_produk'],
                'harga' => $item['product']['harga'],
                'qty' => $item['qty'],
                'subtotal' => $item['subtotal'],
            ]);
        }
        $db->transComplete();
        if (! $db->transStatus()) {
            log_message('error', 'Checkout gagal disimpan untuk kode {code}', ['code' => $code]);
            return redirect()->to(site_url('checkout'))->withInput()->with('error', 'Pesanan belum tersimpan. Silakan coba lagi.');
        }
        $cartService->clear();
        session()->setFlashdata('completed_order', ['code' => $code, 'name' => $input['nama_pembeli'], 'total' => $cart['total']]);
        return redirect()->to(site_url('checkout/berhasil'));
    }

    public function success()
    {
        $order = session()->getFlashdata('completed_order');
        if (! is_array($order)) {
            return redirect()->to(site_url('katalog'));
        }
        return view('checkout/success', ['title' => 'Pesanan Berhasil', 'active' => 'keranjang', 'order' => $order]);
    }
}
