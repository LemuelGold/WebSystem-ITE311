<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Get teacher user ID (assuming Dale Doe is the teacher)
        $db = \Config\Database::connect();
        $teacher = $db->table('users')->where('email', 'dale.doe@gmail.com')->get()->getRowArray();
        
        if (!$teacher) {
            // If teacher doesn't exist, get first user with teacher role
            $teacher = $db->table('users')->where('role', 'teacher')->get()->getRowArray();
        }
        
        // If still no teacher, use admin as instructor
        if (!$teacher) {
            $teacher = $db->table('users')->where('role', 'admin')->get()->getRowArray();
        }
        
        $instructorId = $teacher['id'] ?? 1; // Default to user ID 1 if no teacher found
        
        $data = [
            [
                'title' => 'Introduction to Web Development',
                'description' => 'Learn the fundamentals of web development including HTML, CSS, and JavaScript. This course covers basic concepts and hands-on projects to build your first website.',
                'instructor_id' => $instructorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Database Management Systems',
                'description' => 'Comprehensive course on database design, SQL queries, and database administration. Learn to create and manage efficient database systems.',
                'instructor_id' => $instructorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Object-Oriented Programming',
                'description' => 'Master object-oriented programming concepts including classes, inheritance, polymorphism, and encapsulation. Build robust applications using OOP principles.',
                'instructor_id' => $instructorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Software Engineering Principles',
                'description' => 'Learn software development methodologies, design patterns, and best practices. Understand the software development lifecycle and project management.',
                'instructor_id' => $instructorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Mobile Application Development',
                'description' => 'Build mobile applications for iOS and Android platforms. Learn mobile UI/UX design, app architecture, and deployment strategies.',
                'instructor_id' => $instructorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert data into the 'courses' table
        $this->db->table('courses')->insertBatch($data);
    }
}

