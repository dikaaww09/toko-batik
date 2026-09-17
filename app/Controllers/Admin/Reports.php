<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Reports extends BaseController
{
    public function index(): string
    {
        $start = $this->validDate($this->request->getGet('mulai'));
        $end = $this->validDate($this->request->getGet('selesai'));
        $db = db_connect();
        $orders = $db->table('orders')->where('status', 'selesai');
        if ($start) { $orders->where('created_at >=', $start . ' 00:00:00'); }
        if ($end) { $orders->where('created_at <=', $end . ' 23:59:59'); }
        $completed = $orders->orderBy('created_at', 'DESC')->get()->getResultArray();
        $orderIds = array_column($completed, 'id');
        $sold = 0;
        $bestProducts = [];
        if ($orderIds) {
            $items = $db->table('order_items')->select('nama_produk, SUM(qty) AS terjual, SUM(subtotal) AS omzet')->whereIn('order_id', $orderIds)->groupBy('nama_produk')->orderBy('terjual', 'DESC')->get()->getResultArray();
            $sold = array_sum(array_map(static fn ($item) => (int) $item['terjual'], $items));
            $bestProducts = array_slice($items, 0, 10);
        }
        return view('admin/reports/index', [
            'title' => 'Laporan Penjualan', 'start' => $start, 'end' => $end, 'orders' => $completed,
            'revenue' => array_sum(array_map(static fn ($order) => (int) $order['total'], $completed)),
            'sold' => $sold, 'bestProducts' => $bestProducts,
        ]);
    }

    private function validDate($value): string
    {
        if (! is_string($value) || $value === '') { return ''; }
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : '';
    }
}
