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

        // Sample courses with multiple sections to demonstrate the feature
        $data = [
            // Web System - Multiple sections with same teacher but different schedules
            [
                'course_code'   => '4040',
                'title'         => 'Web System',
                'description'   => 'Advanced web development using modern frameworks and technologies.',
                'instructor_id' => $teacher1, // waa uy
                'section'       => 'A',
                'schedule_time' => 'MWF 8:00-9:00 AM',
                'room'          => 'Room 101',
                'units'         => 3,
                'term'          => 'Term 1',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'course_code'   => '4040',
                'title'         => 'Web System',
                'description'   => 'Advanced web development using modern frameworks and technologies.',
                'instructor_id' => $teacher1, // Same teacher (waa uy)
                'section'       => 'B',
                'schedule_time' => 'TTH 10:00-11:30 AM',
                'room'          => 'Room 102',
                'units'         => 3,
                'term'          => 'Term 1',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            
            // Web Development Fundamentals
            [
                'course_code'   => '1001',
                'title'         => 'Web Development Fundamentals',
                'description'   => 'Learn the basics of HTML, CSS, and JavaScript. Build responsive websites and understand modern web development practices.',
                'instructor_id' => $teacher2,
                'section'       => 'A',
                'schedule_time' => 'MWF 9:00-10:00 AM',
                'room'          => 'Lab A',
                'units'         => 3,
                'term'          => 'Term 1',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            
            // Database Management Systems
            [
                'course_code'   => '2001',
                'title'         => 'Database Management Systems',
                'description'   => 'Introduction to SQL, database design, normalization, and database administration using MySQL and other systems.',
                'instructor_id' => $teacher2,
                'section'       => 'A',
                'schedule_time' => 'TTH 1:00-2:30 PM',
                'room'          => 'Lab B',
                'units'         => 3,
                'term'          => 'Term 1',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            
            // Software Engineering Principles
            [
                'course_code'   => '3001',
                'title'         => 'Software Engineering Principles',
                'description'   => 'Software development lifecycle, methodologies, project management, and best practices in software engineering.',
                'instructor_id' => $teacher3,
                'section'       => 'A',
                'schedule_time' => 'MWF 2:00-3:00 PM',
                'room'          => 'Room 201',
                'units'         => 3,
                'term'          => 'Term 2',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            
            // Computer Networks and Security
            [
                'course_code'   => '3002',
                'title'         => 'Computer Networks and Security',
                'description'   => 'Network protocols, security fundamentals, ethical hacking, and cybersecurity best practices.',
                'instructor_id' => $teacher3,
                'section'       => 'A',
                'schedule_time' => 'TTH 3:00-4:30 PM',
                'room'          => 'Lab C',
                'units'         => 3,
                'term'          => 'Term 2',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            
            // Data Structures and Algorithms
            [
                'course_code'   => '2002',
                'title'         => 'Data Structures and Algorithms',
                'description'   => 'Fundamental data structures, algorithmic thinking, complexity analysis, and problem-solving techniques.',
                'instructor_id' => $teacher4,
                'section'       => 'A',
                'schedule_time' => 'MWF 10:00-11:00 AM',
                'room'          => 'Room 301',
                'units'         => 3,
                'term'          => 'Term 1',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            
            // Mobile App Development
            [
                'course_code'   => '4001',
                'title'         => 'Mobile App Development',
                'description'   => 'Build native and cross-platform mobile applications using modern frameworks and development tools.',
                'instructor_id' => $teacher4,
                'section'       => 'A',
                'schedule_time' => 'TTH 8:00-9:30 AM',
                'room'          => 'Lab D',
                'units'         => 3,
                'term'          => 'Term 2',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'title'         => 'Inactive Course Example',
                'description'   => 'This course is inactive and should not appear in available courses for enrollment.',
                'instructor_id' => $teacher1, // Jane Doe
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]
        ];

        // Insert data into courses table
        $this->db->table('courses')->insertBatch($data);
        
        echo "Courses seeded successfully! " . count($data) . " courses added.\n";
    }
}