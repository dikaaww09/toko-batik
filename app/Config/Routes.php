<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->get('/', 'Home::index');
$routes->get('tentang', 'Home::about');
$routes->get('kontak', 'Home::contact');
$routes->get('katalog', 'Catalog::index');
$routes->get('katalog/(:segment)', 'Catalog::show/$1');
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
});
