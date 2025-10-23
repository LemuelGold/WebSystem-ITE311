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
        $teacherQuery = $this->db->table('users')->where('role', 'teacher')->orderBy('id', 'ASC')->get();
        $teachers = $teacherQuery->getResultArray();
        
        if (empty($teachers)) {
            echo "Warning: No teachers found. Please run UserSeeder first.\n";
            return;
        }

        // Make sure we have at least 4 teachers
        if (count($teachers) < 4) {
            echo "Warning: Only " . count($teachers) . " teacher(s) found. Some courses may use the same teacher.\n";
        }

        // Assign teacher IDs safely
        $teacher1 = $teachers[0]['id'];
        $teacher2 = isset($teachers[1]) ? $teachers[1]['id'] : $teachers[0]['id'];
        $teacher3 = isset($teachers[2]) ? $teachers[2]['id'] : $teachers[0]['id'];
        $teacher4 = isset($teachers[3]) ? $teachers[3]['id'] : $teachers[0]['id'];

        // Sample courses matching the courses table structure
        $data = [
            [
                'id'            => 1001,
                'title'         => 'Web Development Fundamentals',
                'description'   => 'Learn the basics of HTML, CSS, and JavaScript. Build responsive websites and understand modern web development practices.',
                'instructor_id' => $teacher1, // Jane Doe
                'start_date'    => '2024-08-15',
                'end_date'      => '2024-12-15',
                'academic_year' => '2024-2025',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 1002,
                'title'         => 'Database Management Systems',
                'description'   => 'Introduction to SQL, database design, normalization, and database administration using MySQL and other systems.',
                'instructor_id' => $teacher1, // Jane Doe
                'start_date'    => '2024-08-15',
                'end_date'      => '2024-12-15',
                'academic_year' => '2024-2025',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 1003,
                'title'         => 'Software Engineering Principles',
                'description'   => 'Software development lifecycle, methodologies, project management, and best practices in software engineering.',
                'instructor_id' => $teacher2, // Bob Johnson
                'start_date'    => '2025-01-10',
                'end_date'      => '2025-05-10',
                'academic_year' => '2024-2025',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 1004,
                'title'         => 'Computer Networks and Security',
                'description'   => 'Network protocols, security fundamentals, ethical hacking, and cybersecurity best practices.',
                'instructor_id' => $teacher2, // Bob Johnson
                'start_date'    => '2025-01-10',
                'end_date'      => '2025-05-10',
                'academic_year' => '2024-2025',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 1005,
                'title'         => 'Data Structures and Algorithms',
                'description'   => 'Fundamental data structures, algorithmic thinking, complexity analysis, and problem-solving techniques.',
                'instructor_id' => $teacher3, // Sarah Martinez
                'start_date'    => '2025-08-15',
                'end_date'      => '2025-12-15',
                'academic_year' => '2024-2025',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 1006,
                'title'         => 'Mobile App Development',
                'description'   => 'Build native and cross-platform mobile applications using modern frameworks and development tools.',
                'instructor_id' => $teacher4, // Michael Chen
                'start_date'    => '2025-08-15',
                'end_date'      => '2025-12-15',
                'academic_year' => '2024-2025',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 1007,
                'title'         => 'Inactive Course Example',
                'description'   => 'This course is inactive and should not appear in available courses for enrollment.',
                'instructor_id' => $teacher1, // Jane Doe
                'start_date'    => '2023-08-15',
                'end_date'      => '2023-12-15',
                'academic_year' => '2023-2024',
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