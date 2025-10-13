<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * CoursesSeeder - Seeds the courses table with sample data for enrollment testing
 */
class CoursesSeeder extends Seeder
{
    public function run()
    {
        // Get teacher user IDs from the users table
        $teacherQuery = $this->db->table('users')->where('role', 'teacher')->get();
        $teachers = $teacherQuery->getResultArray();
        
        if (empty($teachers)) {
            echo "Warning: No teachers found. Please run UserSeeder first.\n";
            return;
        }

        // Sample courses matching the courses table structure
        $data = [
            [
                'title'         => 'Web Development Fundamentals',
                'description'   => 'Learn the basics of HTML, CSS, and JavaScript. Build responsive websites and understand modern web development practices.',
                'instructor_id' => $teachers[0]['id'], // First teacher
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title'         => 'Database Management Systems',
                'description'   => 'Introduction to SQL, database design, normalization, and database administration using MySQL and other systems.',
                'instructor_id' => $teachers[0]['id'], // First teacher
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title'         => 'Software Engineering Principles',
                'description'   => 'Software development lifecycle, methodologies, project management, and best practices in software engineering.',
                'instructor_id' => count($teachers) > 1 ? $teachers[1]['id'] : $teachers[0]['id'], // Second teacher if available
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title'         => 'Computer Networks and Security',
                'description'   => 'Network protocols, security fundamentals, ethical hacking, and cybersecurity best practices.',
                'instructor_id' => count($teachers) > 1 ? $teachers[1]['id'] : $teachers[0]['id'], // Second teacher if available
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title'         => 'Data Structures and Algorithms',
                'description'   => 'Fundamental data structures, algorithmic thinking, complexity analysis, and problem-solving techniques.',
                'instructor_id' => $teachers[0]['id'], // First teacher
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title'         => 'Mobile App Development',
                'description'   => 'Build native and cross-platform mobile applications using modern frameworks and development tools.',
                'instructor_id' => count($teachers) > 1 ? $teachers[1]['id'] : $teachers[0]['id'], // Second teacher if available
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title'         => 'Inactive Course Example',
                'description'   => 'This course is inactive and should not appear in available courses for enrollment.',
                'instructor_id' => $teachers[0]['id'], // First teacher
                'status'        => 'inactive',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]
        ];

        // Insert data into courses table
        $this->db->table('courses')->insertBatch($data);
        
        echo "Courses seeded successfully! " . count($data) . " courses added.\n";
    }
}