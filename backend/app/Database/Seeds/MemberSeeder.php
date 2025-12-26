<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'member_code' => 'M001',
                'name'        => 'John Doe',
                'email'       => 'john@example.com',
                'phone'       => '081234567890',
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'member_code' => 'M002',
                'name'        => 'Jane Smith',
                'email'       => 'jane@example.com',
                'phone'       => '081234567891',
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'member_code' => 'M003',
                'name'        => 'Michael Brown',
                'email'       => 'michael@example.com',
                'phone'       => '081234567892',
                'status'      => 'inactive',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'member_code' => 'M004',
                'name'        => 'Emily Davis',
                'email'       => 'emily@example.com',
                'phone'       => '081234567893',
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'member_code' => 'M005',
                'name'        => 'Daniel Wilson',
                'email'       => 'daniel@example.com',
                'phone'       => '081234567894',
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('members')->insertBatch($data);
    }
}
