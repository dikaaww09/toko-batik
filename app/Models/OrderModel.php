<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $allowedFields = ['customer_id', 'kode_order', 'nama_pembeli', 'whatsapp', 'alamat', 'catatan', 'total', 'metode_pembayaran', 'status_pembayaran', 'status', 'stok_dikembalikan'];
    protected $useTimestamps = true;
}
