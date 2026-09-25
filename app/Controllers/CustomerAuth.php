<?php

namespace App\Controllers;

use App\Models\CustomerModel;

class CustomerAuth extends BaseController
{
    public function login()
    {
        if (session()->get('customer_id')) {
            return redirect()->to(site_url('/'));
        }
        $this->response->setHeader('Cache-Control', 'no-store');
        return view('auth/login_customer', ['title' => 'Masuk Pelanggan', 'active' => 'login']);
    }

    public function authenticate()
    {
        if (session()->get('customer_id')) {
            return redirect()->to(site_url('/'));
        }

        if (! service('throttler')->check('login-customer-' . hash('sha256', $this->request->getIPAddress()), 5, MINUTE)) {
            return redirect()->to(site_url('login'))->with('error', 'Terlalu banyak percobaan. Coba lagi dalam satu menit.');
        }

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required'
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->to(site_url('login'))->with('error', 'Masukkan email dan password yang valid.');
        }

        $customerModel = new CustomerModel();
        $customer = $customerModel->where('email', trim($this->request->getPost('email')))->first();

        if (! $customer || ! password_verify($this->request->getPost('password'), $customer['password'])) {
            return redirect()->to(site_url('login'))->with('error', 'Email atau password salah.');
        }

        session()->regenerate(true);
        session()->set('customer_id', (int) $customer['id']);
        session()->set('customer_name', $customer['nama']);

        // Redirect back to checkout if they were heading there
        if (session()->get('redirect_url')) {
            $redirectUrl = session()->get('redirect_url');
            session()->remove('redirect_url');
            return redirect()->to($redirectUrl);
        }

        return redirect()->to(site_url('/'));
    }

    public function register()
    {
        if (session()->get('customer_id')) {
            return redirect()->to(site_url('/'));
        }
        return view('auth/register_customer', ['title' => 'Daftar Pelanggan', 'active' => 'register']);
    }

    public function store()
    {
        if (session()->get('customer_id')) {
            return redirect()->to(site_url('/'));
        }

        $customerModel = new CustomerModel();

        $input = $this->request->getPost();
        $rules = $customerModel->getValidationRules();
        $rules['password_confirm'] = 'required|matches[password]';

        if (! $this->validateData($input, $rules)) {
            return redirect()->to(site_url('register'))->withInput()->with('errors', $this->validator->getErrors());
        }

        $customerModel->insert([
            'nama'     => trim($input['nama']),
            'email'    => trim($input['email']),
            'password' => password_hash($input['password'], PASSWORD_DEFAULT),
            'whatsapp' => trim($input['whatsapp']),
            'alamat'   => trim($input['alamat']),
        ]);

        return redirect()->to(site_url('login'))->with('success', 'Pendaftaran berhasil. Silakan login.');
    }

    public function logout()
    {
        session()->remove(['customer_id', 'customer_name']);
        return redirect()->to(site_url('/'));
    }
}
