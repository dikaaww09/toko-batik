<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\{ProductModel, CategoryModel};
use App\Libraries\ProductImages;
use CodeIgniter\Exceptions\PageNotFoundException;
class Products extends BaseController
{
    public function index(): string
    {
        $model = (new ProductModel())->withCategory();
        return view('admin/products/index', ['title' => 'Kelola Produk', 'products' => $model->orderBy('products.id', 'DESC')->paginate(10), 'pager' => $model->pager]);
    }
    public function create(): string { return $this->form(); }
    public function edit(int $id): string { return $this->form($this->find($id)); }
    public function store() { return $this->persist(); }
    public function update(int $id) { return $this->persist($this->find($id)); }
    private function find(int $id): array
    {
        return (new ProductModel())->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }
    private function form(?array $product = null): string
    {
        return view('admin/products/form', ['title' => $product ? 'Edit Produk' : 'Tambah Produk', 'product' => $product, 'categories' => (new CategoryModel())->findAll()]);
    }
    private function persist(?array $product = null)
    {
        $back = $product ? 'admin/produk/edit/' . $product['id'] : 'admin/produk/tambah';
        $rules = [
            'nama_produk' => ['label' => 'Nama produk', 'rules' => 'required|min_length[3]|max_length[150]'],
            'kategori_id' => ['label' => 'Kategori', 'rules' => 'required|is_natural_no_zero|is_not_unique[categories.id]'],
            'harga' => ['label' => 'Harga', 'rules' => 'required|is_natural_no_zero|less_than_equal_to[999999999999]'],
            'deskripsi' => ['label' => 'Deskripsi', 'rules' => 'required|min_length[10]|max_length[5000]'],
            'status_ketersediaan' => ['label' => 'Ketersediaan', 'rules' => 'required|in_list[tersedia,tidak_tersedia]'],
        ];
        $upload = $this->request->getFile('gambar');
        $hasUpload = $upload && $upload->getError() !== UPLOAD_ERR_NO_FILE;
        if ($hasUpload) {
            $rules['gambar'] = ['label' => 'Gambar', 'rules' => 'uploaded[gambar]|is_image[gambar]|mime_in[gambar,image/jpeg,image/png,image/webp]|ext_in[gambar,jpg,jpeg,png,webp]|max_size[gambar,2048]|max_dims[gambar,3000,3000]'];
        }
        $input = $this->request->getPost();
        foreach (['nama_produk', 'deskripsi'] as $field) {
            if (isset($input[$field]) && is_string($input[$field])) { $input[$field] = trim($input[$field]); }
        }
        if (! $this->validateData($input, $rules)) {
            return redirect()->to(site_url($back))->withInput()->with('errors', $this->validator->getErrors());
        }
        $data = array_intersect_key($input, array_flip(['nama_produk', 'kategori_id', 'harga', 'deskripsi', 'status_ketersediaan']));
        $model = new ProductModel();
        $images = new ProductImages();
        $newImage = null;
        try {
            if ($hasUpload) { $newImage = $images->store($upload); $data['gambar'] = $newImage; }
            if (! $product) {
                $base = url_title($data['nama_produk'], '-', true) ?: 'produk';
                $data['slug'] = $base . '-' . bin2hex(random_bytes(4));
            }
            $saved = $product ? $model->update($product['id'], $data) : $model->insert($data);
            if (! $saved) { throw new \RuntimeException('Produk gagal disimpan.'); }
        } catch (\Throwable $error) {
            $images->delete($newImage);
            log_message('error', 'Penyimpanan produk gagal: {message}', ['message' => $error->getMessage()]);
            return redirect()->to(site_url($back))->withInput()->with('error', 'Produk belum tersimpan. Periksa gambar dan coba lagi.');
        }
        if ($newImage && $product) { $images->delete($product['gambar']); }
        return redirect()->to(site_url('admin/produk'))->with('success', 'Produk berhasil disimpan.');
    }
    public function delete(int $id)
    {
        $product = $this->find($id);
        try {
            if (! (new ProductModel())->delete($id)) { throw new \RuntimeException('Hapus gagal.'); }
        } catch (\Throwable $error) {
            log_message('error', 'Penghapusan produk gagal: {message}', ['message' => $error->getMessage()]);
            return redirect()->to(site_url('admin/produk'))->with('error', 'Produk belum terhapus. Coba lagi.');
        }
        (new ProductImages())->delete($product['gambar']);
        return redirect()->to(site_url('admin/produk'))->with('success', 'Produk berhasil dihapus.');
    }
}
