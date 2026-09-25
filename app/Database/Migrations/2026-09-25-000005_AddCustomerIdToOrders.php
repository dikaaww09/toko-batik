<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerIdToOrders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders', [
            'customer_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'id'
            ],
        ]);

        // Add foreign key
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->processIndexes('orders');
    }

    public function down()
    {
        $this->forge->dropForeignKey('orders', 'orders_customer_id_foreign');
        $this->forge->dropColumn('orders', 'customer_id');
    }
}
