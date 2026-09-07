<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\{ProductModel, CategoryModel};
class Dashboard extends BaseController
{
    public function index(): string
    {
        return view('admin/dashboard', ['title' => 'Dashboard', 'totalProducts' => (new ProductModel())->countAllResults(), 'totalCategories' => (new CategoryModel())->countAllResults()]);
    }
}
