<?php
namespace App\Models;
use CodeIgniter\Model;
class AdminModel extends Model
{
    protected $table = 'admins';
    protected $allowedFields = ['username', 'password'];
    protected $useTimestamps = true;
}
