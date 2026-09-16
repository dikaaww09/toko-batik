<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrders extends Migration
{
    public function up()
    {
        $date = ['type' => 'DATETIME', 'null' => true];
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kode_order' => ['type' => 'VARCHAR', 'constraint' => 30],
            'nama_pembeli' => ['type' => 'VARCHAR', 'constraint' => 120],
            'whatsapp' => ['type' => 'VARCHAR', 'constraint' => 20],
            'alamat' => ['type' => 'TEXT'],
            'catatan' => ['type' => 'TEXT', 'null' => true],
            'total' => ['type' => 'DECIMAL', 'constraint' => '12,0'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'baru'],
            'created_at' => $date,
            'updated_at' => $date,
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_order');
        $this->forge->addKey('status');
        $this->forge->createTable('orders', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_id' => ['type' => 'INT', 'unsigned' => true],
            'product_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'nama_produk' => ['type' => 'VARCHAR', 'constraint' => 150],
            'harga' => ['type' => 'DECIMAL', 'constraint' => '12,0'],
            'qty' => ['type' => 'INT', 'unsigned' => true],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '12,0'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->addKey('product_id');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('order_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('order_items', true);
        $this->forge->dropTable('orders', true);
    }
}
