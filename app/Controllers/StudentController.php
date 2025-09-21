<?php

namespace App\Controllers;

/**
 * StudentController - Handles student-specific functionality and dashboard
 */
class StudentController extends BaseController
{
    protected $session;
    protected $db;

    public function __construct()
    {
        // Initialize services for student operations
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
    }

    /**
     * Student Dashboard - displays student-specific information and progress
     */
    public function dashboard()
    {
        // Authorization check - ensure user is logged in and has student role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'student') {
            $this->session->setFlashdata('error', 'Access denied. Student privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Prepare student-specific dashboard data
        $data = $this->prepareStudentDashboardData();
        
        return view('student/dashboard', $data);
    }

    /**
     * Prepare data for student dashboard display
     */
    private function prepareStudentDashboardData(): array
    {
        $studentId = $this->session->get('userID');
        
        // For demo purposes - simulated enrollment data (would come from enrollments/courses tables)
        $enrolledCourses = [
            ['id' => 1, 'name' => 'Web Development Fundamentals', 'teacher' => 'Prof. Garcia', 'progress' => 75, 'grade' => 'A'],
            ['id' => 2, 'name' => 'Database Management Systems', 'teacher' => 'Prof. Santos', 'progress' => 60, 'grade' => 'B+'],
            ['id' => 3, 'name' => 'Software Engineering', 'teacher' => 'Prof. Reyes', 'progress' => 90, 'grade' => 'A-'],
            ['id' => 4, 'name' => 'Computer Networks', 'teacher' => 'Prof. Cruz', 'progress' => 45, 'grade' => 'B']
        ];

        // Simulated upcoming deadlines
        $upcomingDeadlines = [
            ['course' => 'Web Development', 'assignment' => 'Final Project', 'due_date' => '2025-09-25', 'status' => 'pending'],
            ['course' => 'Database Management', 'assignment' => 'Lab Exercise 4', 'due_date' => '2025-09-23', 'status' => 'pending'],
            ['course' => 'Software Engineering', 'assignment' => 'System Design', 'due_date' => '2025-09-28', 'status' => 'in_progress']
        ];

        // Simulated recent grades
        $recentGrades = [
            ['course' => 'Web Development', 'assignment' => 'Midterm Project', 'grade' => 'A', 'date' => '2025-09-15'],
            ['course' => 'Database Management', 'assignment' => 'Quiz 3', 'grade' => 'B+', 'date' => '2025-09-12'],
            ['course' => 'Software Engineering', 'assignment' => 'Lab 5', 'grade' => 'A-', 'date' => '2025-09-10']
        ];

        return [
            'title' => 'Student Dashboard - ITE311 FUNDAR',
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'email'  => $this->session->get('email'),
                'role'   => $this->session->get('role')
            ],
            'stats' => [
                'enrolledCourses' => count($enrolledCourses),
                'completedCourses' => count(array_filter($enrolledCourses, fn($course) => $course['progress'] >= 100)),
                'averageGrade' => $this->calculateAverageGrade($enrolledCourses),
                'pendingAssignments' => count($upcomingDeadlines)
            ],
            'enrolledCourses' => $enrolledCourses,
            'upcomingDeadlines' => $upcomingDeadlines,
            'recentGrades' => $recentGrades
        ];
    }

    /**
     * Calculate average grade from enrolled courses
     */
    private function calculateAverageGrade(array $courses): string
    {
        if (empty($courses)) return 'N/A';
        
        // Convert letter grades to points for calculation
        $gradePoints = ['A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7, 'C+' => 2.3, 'C' => 2.0];
        $totalPoints = 0;
        $validGrades = 0;
        
        foreach ($courses as $course) {
            if (isset($gradePoints[$course['grade']])) {
                $totalPoints += $gradePoints[$course['grade']];
                $validGrades++;
            }
        }
        
        if ($validGrades === 0) return 'N/A';
        
        $average = $totalPoints / $validGrades;
        return number_format($average, 2);
    }

    /**
     * Check if user is logged in
     */
    private function isLoggedIn(): bool
    {
        return $this->session->get('isLoggedIn') === true;
    }

    /**
     * View enrolled courses
     */
    public function viewCourses()
    {
        // Authorization check for student role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'student') {
            $this->session->setFlashdata('error', 'Access denied. Student privileges required.');
            return redirect()->to(base_url('login'));
        }

        // This would typically fetch enrollments from database where student_id = current user
        $data = [
            'title' => 'My Courses - Student Panel',
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('student/my_courses', $data);
    }
}