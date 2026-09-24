<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{OrderModel, OrderItemModel};
use CodeIgniter\Exceptions\PageNotFoundException;

class Orders extends BaseController
{
    public function index(): string
    {
        $query = trim((string) $this->request->getGet('q'));
        $status = (string) $this->request->getGet('status');
        $model = new OrderModel();
        if ($query !== '') {
            $model->groupStart()->like('kode_order', mb_substr($query, 0, 120))->orLike('nama_pembeli', mb_substr($query, 0, 120))->orLike('whatsapp', mb_substr($query, 0, 20))->groupEnd();
        }
        if (in_array($status, ['baru', 'diproses', 'dikirim', 'selesai', 'dibatalkan'], true)) { $model->where('status', $status); }
        return view('admin/orders/index', ['title' => 'Kelola Pesanan', 'orders' => $model->orderBy('id', 'DESC')->paginate(15), 'pager' => $model->pager, 'q' => $query, 'selectedStatus' => $status]);
    }

    public function show(int $id): string
    {
        return view('admin/orders/show', ['title' => 'Detail Pesanan', 'order' => $this->find($id), 'items' => (new OrderItemModel())->where('order_id', $id)->findAll()]);
    }

    public function updateStatus(int $id)
    {
        $order = $this->find($id);
        $status = $this->request->getPost('status');
        $payment = $this->request->getPost('status_pembayaran');
        if (! is_string($status) || ! in_array($status, ['baru', 'diproses', 'dikirim', 'selesai', 'dibatalkan'], true)
            || ! is_string($payment) || ! in_array($payment, ['belum_dibayar', 'sudah_dibayar'], true)) {
            return redirect()->back()->with('error', 'Status pesanan atau pembayaran tidak valid.');
        }
        if ($order['status'] === 'dibatalkan' && $status !== 'dibatalkan') {
            return redirect()->back()->with('error', 'Pesanan yang sudah dibatalkan tidak dapat diaktifkan kembali.');
        }

        $db = db_connect();
        $db->transBegin();
        try {
            $data = ['status' => $status, 'status_pembayaran' => $payment];
            if ($status === 'dibatalkan' && ! (int) $order['stok_dikembalikan']) {
                $items = (new OrderItemModel())->where('order_id', $id)->findAll();
                foreach ($items as $item) {
                    if (! $item['product_id']) { continue; }
                    $product = $db->table('products')->where('id', $item['product_id'])->get()->getRowArray();
                    if ($product) {
                        $db->query('UPDATE products SET stok = stok + ?, status_ketersediaan = ? WHERE id = ?', [$item['qty'], 'tersedia', $item['product_id']]);
                    }
                }
                $data['stok_dikembalikan'] = 1;
            }
            (new OrderModel())->update($id, $data);
            $db->transCommit();
        } catch (\Throwable $error) {
            $db->transRollback();
            log_message('error', 'Perubahan status pesanan gagal: {message}', ['message' => $error->getMessage()]);
            return redirect()->back()->with('error', 'Status belum berhasil diperbarui.');
        }
        return redirect()->to(site_url('admin/pesanan/' . $id))->with('success', 'Status pesanan diperbarui.');
    }

    private function find(int $id): array
    {
        return (new OrderModel())->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }
}
