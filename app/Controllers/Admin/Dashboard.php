<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{CategoryModel, OrderModel, ProductModel};

class Dashboard extends BaseController
{
    public function index(): string
    {
        $orders = new OrderModel();
        $period = $this->request->getGet('period') === '30days' ? 30 : 7;

        return view('admin/dashboard', [
            'title' => 'Dashboard',
            'period' => $period,
            'totalProducts' => (new ProductModel())->countAllResults(),
            'totalCategories' => (new CategoryModel())->countAllResults(),
            'newOrders' => (clone $orders)->where('status', 'baru')->countAllResults(),
            'outOfStock' => (new ProductModel())->where('stok', 0)->countAllResults(),
            'revenue' => (int) ((new OrderModel())->selectSum('total')->where('status', 'selesai')->first()['total'] ?? 0),
            'recentOrders' => (new OrderModel())->orderBy('id', 'DESC')->findAll(5),
            'salesChart' => $this->salesChart($period),
            'topProducts' => $this->topProducts(),
        ]);
    }

    private function topProducts(): array
    {
        $rows = db_connect()->table('order_items')
            ->select('order_items.nama_produk, SUM(order_items.qty) as total_qty, SUM(order_items.subtotal) as total_revenue')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('orders.status', 'selesai')
            ->groupBy('order_items.product_id, order_items.nama_produk')
            ->orderBy('total_qty', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        if ($rows === []) return [];

        $maxQty = max(array_column($rows, 'total_qty')) ?: 1;
        foreach ($rows as &$row) {
            $row['width'] = max(5, (int) round(($row['total_qty'] / $maxQty) * 100));
        }
        return $rows;
    }

    private function salesChart(int $daysLimit = 7): array
    {
        $rows = db_connect()->table('orders')
            ->select('DATE(created_at) AS sale_date, COUNT(*) AS total_orders, COALESCE(SUM(total), 0) AS total_revenue', false)
            ->where('status', 'selesai')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'DESC')
            ->limit($daysLimit)
            ->get()
            ->getResultArray();

        $rows = array_reverse($rows);
        $days = [];

        foreach ($rows as $row) {
            $date = (string) $row['sale_date'];
            $label = \DateTimeImmutable::createFromFormat('!Y-m-d', $date)?->format('d M') ?? $date;
            $days[] = [
                'date' => $date,
                'label' => $label,
                'orders' => (int) $row['total_orders'],
                'revenue' => (int) $row['total_revenue'],
                'height' => 0,
            ];
        }

        if ($days === []) {
            return [];
        }

        $maxRevenue = max(array_column($days, 'revenue')) ?: 1;

        foreach ($days as &$day) {
            $day['height'] = $day['revenue'] > 0 ? max(10, (int) round(($day['revenue'] / $maxRevenue) * 100)) : 0;
        }
        unset($day);

        return $days;
    }
}
