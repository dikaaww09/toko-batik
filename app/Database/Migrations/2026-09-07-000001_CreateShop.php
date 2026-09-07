<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateShop extends Migration
{
    public function up()
    {
        $id = ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true];
        $date = ['type' => 'DATETIME', 'null' => true];
        $this->forge->addField([
            'id' => $id,
            'username' => ['type' => 'VARCHAR', 'constraint' => 80],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at' => $date, 'updated_at' => $date,
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('admins', true);
        $this->forge->addField(['id' => $id, 'nama' => ['type' => 'VARCHAR', 'constraint' => 80]]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nama');
        $this->forge->createTable('categories', true);
        $this->forge->addField([
            'id' => $id,
            'nama_produk' => ['type' => 'VARCHAR', 'constraint' => 150],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 180],
            'kategori_id' => ['type' => 'INT', 'unsigned' => true],
            'harga' => ['type' => 'DECIMAL', 'constraint' => '12,0'],
            'deskripsi' => ['type' => 'TEXT'],
            'gambar' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status_ketersediaan' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'tersedia'],
            'created_at' => $date, 'updated_at' => $date,
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('kategori_id');
        $this->forge->addForeignKey('kategori_id', 'categories', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('products', true);
    }
    public function down()
    {
        $this->forge->dropTable('products', true);
        $this->forge->dropTable('categories', true);
        $this->forge->dropTable('admins', true);
    }
}
