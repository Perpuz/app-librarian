<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Transactions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'book_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'borrow_date' => [
                'type' => 'DATE',
            ],
            'due_date' => [
                'type' => 'DATE',
            ],
            'return_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['borrowed', 'returned', 'overdue'],
                'default'    => 'borrowed',
            ],
            'fine' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'created_at datetime default current_timestamp',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('member_id', 'members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('book_id', 'books', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('transactions');
    }

    public function down()
    {
        $this->forge->dropTable('transactions');
    }
}
