<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'first_name' => 'Admin',
                'last_name'  => 'User',
                'email'      => 'admin@gmail.com',
                'password'   => password_hash('admin123', PASSWORD_BCRYPT),
                'role'       => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'first_name' => 'Dale',
                'last_name'  => 'Doe',
                'email'      => 'dale.doe@gmail.com',
                'password'   => password_hash('teacher123', PASSWORD_BCRYPT),
                'role'       => 'instructor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'first_name' => 'Jane',
                'last_name'  => 'Smith',
                'email'      => 'jane.smith@gmail.com',
                'password'   => password_hash('student123', PASSWORD_BCRYPT),
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'first_name' => 'Dale',
                'last_name'  => 'Brown',
                'email'      => 'david.brown@gmail.com',
                'password'   => password_hash('student123', PASSWORD_BCRYPT),
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert data into the 'users' table
        $this->db->table('users')->insertBatch($data);
    }
}
