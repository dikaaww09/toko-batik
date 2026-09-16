<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{OrderModel, OrderItemModel};
use CodeIgniter\Exceptions\PageNotFoundException;

class Orders extends BaseController
{
    public function index(): string
    {
        $model = new OrderModel();
        return view('admin/orders/index', ['title' => 'Kelola Pesanan', 'orders' => $model->orderBy('id', 'DESC')->paginate(15), 'pager' => $model->pager]);
    }

    public function show(int $id): string
    {
        return view('admin/orders/show', ['title' => 'Detail Pesanan', 'order' => $this->find($id), 'items' => (new OrderItemModel())->where('order_id', $id)->findAll()]);
    }

    public function updateStatus(int $id)
    {
        $this->find($id);
        $status = $this->request->getPost('status');
        if (! is_string($status) || ! in_array($status, ['baru', 'diproses', 'selesai', 'dibatalkan'], true)) {
            return redirect()->back()->with('error', 'Status pesanan tidak valid.');
        }
        (new OrderModel())->update($id, ['status' => $status]);
        return redirect()->to(site_url('admin/pesanan/' . $id))->with('success', 'Status pesanan diperbarui.');
    }

    private function find(int $id): array
    {
        return (new OrderModel())->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }
}
