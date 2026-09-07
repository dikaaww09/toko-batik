<?php
namespace App\Controllers;
use App\Models\AdminModel;
class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('admin_id')) { return redirect()->to(site_url('admin')); }
        $this->response->setHeader('Cache-Control', 'no-store');
        return view('auth/login', ['title' => 'Masuk Admin', 'active' => 'admin']);
    }
    public function authenticate()
    {
        if (session()->get('admin_id')) { return redirect()->to(site_url('admin')); }
        if (! service('throttler')->check('login-' . hash('sha256', $this->request->getIPAddress()), 5, MINUTE)) {
            return redirect()->to(site_url('admin/login'))->with('error', 'Terlalu banyak percobaan. Coba lagi dalam satu menit.');
        }
        if (! $this->validateData($this->request->getPost(), ['username' => 'required|max_length[80]', 'password' => 'required|max_length[200]'])) {
            return redirect()->to(site_url('admin/login'))->with('error', 'Masukkan username dan password yang valid.');
        }
        $admin = (new AdminModel())->where('username', trim($this->request->getPost('username')))->first();
        $hash = $admin['password'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        if (! password_verify($this->request->getPost('password'), $hash) || ! $admin) {
            return redirect()->to(site_url('admin/login'))->with('error', 'Username atau password salah.');
        }
        session()->regenerate(true);
        session()->set('admin_id', (int) $admin['id']);
        return redirect()->to(site_url('admin'));
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('admin/login'));
    }
}
