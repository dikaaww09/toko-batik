<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $allowedFields = ['kode_order', 'nama_pembeli', 'whatsapp', 'alamat', 'catatan', 'total', 'status'];
    protected $useTimestamps = true;
}
