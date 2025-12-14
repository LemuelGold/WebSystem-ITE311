<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * UserSeeder - Seeds the users table with sample data
 */
class UserSeeder extends Seeder
{
    public function run()
    {
        // Insert sample users matching the users table structure
        $data = [
            [
                'name'       => 'ADMIN',
                'email'      => 'admin@gmail.com',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'role'       => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'John Smith',
                'email'      => 'john@gmail.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Dale Doe',
                'email'      => 'Dale_Doe@gmail.com',
                'password'   => password_hash('teacher123', PASSWORD_DEFAULT),
                'role'       => 'teacher',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
          
        ];

        // Insert data into users table
        $this->db->table('users')->insertBatch($data);
    }
}