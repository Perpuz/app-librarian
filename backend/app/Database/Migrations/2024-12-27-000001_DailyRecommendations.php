<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DailyRecommendations extends Migration
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
            'book_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'display_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at datetime default current_timestamp',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('book_id', 'books', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('daily_recommendations');
    }

    public function down()
    {
        $this->forge->dropTable('daily_recommendations');
    }
}
