<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->get('/', 'Home::index');
$routes->get('tentang', 'Home::about');
$routes->get('kontak', 'Home::contact');
$routes->get('katalog', 'Catalog::index');
$routes->get('katalog/(:segment)', 'Catalog::show/$1');
$routes->get('keranjang', 'Cart::index');
$routes->post('keranjang/tambah/(:num)', 'Cart::add/$1');
$routes->post('keranjang/perbarui', 'Cart::update');
$routes->post('keranjang/hapus/(:num)', 'Cart::remove/$1');
$routes->get('checkout', 'Checkout::index');
$routes->post('checkout', 'Checkout::store');
$routes->get('checkout/berhasil', 'Checkout::success');

// Customer Auth Routes
$routes->get('login', 'CustomerAuth::login');
$routes->post('login', 'CustomerAuth::authenticate');
$routes->get('register', 'CustomerAuth::register');
$routes->post('register', 'CustomerAuth::store');
$routes->post('logout', 'CustomerAuth::logout');
$routes->get('akun', 'Account::index');

$routes->get('admin/login', 'Auth::login');
$routes->post('admin/login', 'Auth::authenticate');
$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    $routes->post('logout', 'Auth::logout');
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('produk', 'Admin\Products::index');
    $routes->get('produk/tambah', 'Admin\Products::create');
    $routes->post('produk/simpan', 'Admin\Products::store');
    $routes->get('produk/edit/(:num)', 'Admin\Products::edit/$1');
    $routes->post('produk/update/(:num)', 'Admin\Products::update/$1');
    $routes->post('produk/hapus/(:num)', 'Admin\Products::delete/$1');
    $routes->get('kategori', 'Admin\Categories::index');
    $routes->post('kategori/simpan', 'Admin\Categories::store');
    $routes->post('kategori/update/(:num)', 'Admin\Categories::update/$1');
    $routes->post('kategori/hapus/(:num)', 'Admin\Categories::delete/$1');
    $routes->get('pesanan', 'Admin\Orders::index');
    $routes->get('pesanan/(:num)', 'Admin\Orders::show/$1');
    $routes->post('pesanan/status/(:num)', 'Admin\Orders::updateStatus/$1');
    $routes->get('laporan', 'Admin\Reports::index');
});
