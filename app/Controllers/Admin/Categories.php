<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Categories extends BaseController
{
    public function index(): string
    {
        return view('admin/categories/index', ['title' => 'Kelola Kategori', 'categories' => (new CategoryModel())->orderBy('nama')->findAll()]);
    }

    public function store()
    {
        $name = trim((string) $this->request->getPost('nama'));
        if (! $this->validateData(['nama' => $name], ['nama' => ['label' => 'Nama kategori', 'rules' => 'required|min_length[3]|max_length[80]|is_unique[categories.nama]']])) {
            return redirect()->to(site_url('admin/kategori'))->withInput()->with('errors', $this->validator->getErrors());
        }
        (new CategoryModel())->insert(['nama' => $name]);
        return redirect()->to(site_url('admin/kategori'))->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $model = new CategoryModel();
        $category = $model->find($id) ?? throw PageNotFoundException::forPageNotFound();
        $name = trim((string) $this->request->getPost('nama'));
        if (! $this->validateData(['nama' => $name], ['nama' => ['label' => 'Nama kategori', 'rules' => "required|min_length[3]|max_length[80]|is_unique[categories.nama,id,{$id}]"]])) {
            return redirect()->to(site_url('admin/kategori'))->with('errors', $this->validator->getErrors());
        }
        $model->update($category['id'], ['nama' => $name]);
        return redirect()->to(site_url('admin/kategori'))->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $model = new CategoryModel();
        $model->find($id) ?? throw PageNotFoundException::forPageNotFound();
        try {
            $model->delete($id);
        } catch (\Throwable $error) {
            return redirect()->to(site_url('admin/kategori'))->with('error', 'Kategori masih digunakan produk dan tidak dapat dihapus.');
        }
        return redirect()->to(site_url('admin/kategori'))->with('success', 'Kategori berhasil dihapus.');
    }
}
