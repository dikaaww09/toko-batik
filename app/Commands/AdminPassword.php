<?php
namespace App\Commands;
use App\Models\AdminModel;
use CodeIgniter\CLI\{BaseCommand, CLI};
class AdminPassword extends BaseCommand
{
    protected $group = 'Batik Pusaka';
    protected $name = 'admin:password';
    protected $description = 'Mengganti password admin dari seed.adminPassword di .env.';
    protected $usage = 'admin:password [username]';
    public function run(array $params)
    {
        $username = $params[0] ?? (string) env('seed.adminUsername', 'admin');
        $password = (string) env('seed.adminPassword', '');
        if (strlen($password) < 12 || (ENVIRONMENT === 'production' && $password === 'BatikDemo!2026')) {
            CLI::error('Isi seed.adminPassword dengan password unik minimal 12 karakter.');
            return EXIT_ERROR;
        }
        $model = new AdminModel();
        $admin = $model->where('username', $username)->first();
        if (! $admin) { CLI::error('Admin tidak ditemukan.'); return EXIT_ERROR; }
        $model->update($admin['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
        CLI::write('Password admin diperbarui. Hapus seed.adminPassword dari .env setelah selesai.', 'green');
        return EXIT_SUCCESS;
    }
}
