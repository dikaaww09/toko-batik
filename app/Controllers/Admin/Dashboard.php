<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\{ProductModel, CategoryModel, OrderModel};
class Dashboard extends BaseController
{
    public function index(): string
    {
        $orders = new OrderModel();
        return view('admin/dashboard', [
            'title' => 'Dashboard',
            'totalProducts' => (new ProductModel())->countAllResults(),
            'totalCategories' => (new CategoryModel())->countAllResults(),
            'newOrders' => (clone $orders)->where('status', 'baru')->countAllResults(),
            'outOfStock' => (new ProductModel())->where('stok', 0)->countAllResults(),
            'revenue' => (int) ((new OrderModel())->selectSum('total')->where('status', 'selesai')->first()['total'] ?? 0),
            'recentOrders' => (new OrderModel())->orderBy('id', 'DESC')->findAll(5),
        ]);
    }
}
