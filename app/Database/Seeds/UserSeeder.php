<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Insert sample users
        $data = [
            [
                'username'   => 'admin',
                'email'      => 'admin@lms.com',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'role'       => 'admin',
                'first_name' => 'System',
                'last_name'  => 'Administrator',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'instructor1',
                'email'      => 'instructor1@lms.com',
                'password'   => password_hash('inst123', PASSWORD_DEFAULT),
                'role'       => 'instructor',
                'first_name' => 'John',
                'last_name'  => 'Smith',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'student1',
                'email'      => 'student1@lms.com',
                'password'   => password_hash('stud123', PASSWORD_DEFAULT),
                'role'       => 'student',
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'student2',
                'email'      => 'student2@lms.com',
                'password'   => password_hash('stud123', PASSWORD_DEFAULT),
                'role'       => 'student',
                'first_name' => 'Bob',
                'last_name'  => 'Johnson',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert data into users table
        $this->db->table('users')->insertBatch($data);
    }
}