<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\OrderModel;

class Account extends BaseController
{
    public function index()
    {
        if (! session()->get('customer_id')) {
            return redirect()->to(site_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $customerId = session()->get('customer_id');
        $customerModel = new CustomerModel();
        $orderModel = new OrderModel();

        $customer = $customerModel->find($customerId);
        $orders = $orderModel->where('customer_id', $customerId)
                             ->orderBy('created_at', 'DESC')
                             ->findAll();

        return view('account/index', [
            'title' => 'Akun Saya',
            'active' => 'akun',
            'customer' => $customer,
            'orders' => $orders
        ]);
    }
}
