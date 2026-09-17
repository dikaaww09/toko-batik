<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStockAndPayment extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'stok' => ['type' => 'INT', 'unsigned' => true, 'default' => 10, 'after' => 'harga'],
        ]);
        $this->forge->addColumn('orders', [
            'metode_pembayaran' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'cod', 'after' => 'total'],
            'status_pembayaran' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'belum_dibayar', 'after' => 'metode_pembayaran'],
            'stok_dikembalikan' => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'default' => 0, 'after' => 'status'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', ['metode_pembayaran', 'status_pembayaran', 'stok_dikembalikan']);
        $this->forge->dropColumn('products', 'stok');
    }
}
