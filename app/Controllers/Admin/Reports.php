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

        $builder = $db->table('order_items')
            ->select('orders.created_at, orders.kode_order, order_items.nama_produk, order_items.qty, order_items.harga, order_items.subtotal, orders.metode_pembayaran')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('orders.status', 'selesai');

        if ($start) { $builder->where('orders.created_at >=', $start . ' 00:00:00'); }
        if ($end) { $builder->where('orders.created_at <=', $end . ' 23:59:59'); }

        $reportItems = $builder->orderBy('orders.created_at', 'ASC')->get()->getResultArray();

        $totalQty = 0;
        $totalRevenue = 0;
        foreach ($reportItems as $item) {
            $totalQty += (int) $item['qty'];
            $totalRevenue += (int) $item['subtotal'];
        }

        return view('admin/reports/index', [
            'title' => 'Laporan Penjualan',
            'start' => $start,
            'end' => $end,
            'reportItems' => $reportItems,
            'totalQty' => $totalQty,
            'totalRevenue' => $totalRevenue
        ]);
    }

    private function validDate($value): string
    {
        if (! is_string($value) || $value === '') { return ''; }
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value ? $value : '';
    }
}
