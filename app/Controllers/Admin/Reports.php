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

        // 1. DATA UNTUK TAMPILAN WEB (Ringkasan & Top Produk)
        $ordersQuery = $db->table('orders')->where('status', 'selesai');
        if ($start) { $ordersQuery->where('created_at >=', $start . ' 00:00:00'); }
        if ($end) { $ordersQuery->where('created_at <=', $end . ' 23:59:59'); }
        $completed = $ordersQuery->orderBy('created_at', 'DESC')->get()->getResultArray();

        $orderIds = array_column($completed, 'id');
        $sold = 0;
        $bestProducts = [];
        if ($orderIds) {
            $items = $db->table('order_items')
                ->select('nama_produk, SUM(qty) AS terjual, SUM(subtotal) AS omzet')
                ->whereIn('order_id', $orderIds)
                ->groupBy('nama_produk')
                ->orderBy('terjual', 'DESC')
                ->get()->getResultArray();
            $sold = array_sum(array_map(static fn ($item) => (int) $item['terjual'], $items));
            $bestProducts = array_slice($items, 0, 10);
        }
        $revenue = array_sum(array_map(static fn ($order) => (int) $order['total'], $completed));

        // 2. DATA UNTUK TAMPILAN CETAK (Tabel Rincian Panjang)
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
            'orders' => $completed,
            'sold' => $sold,
            'revenue' => $revenue,
            'bestProducts' => $bestProducts,
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
