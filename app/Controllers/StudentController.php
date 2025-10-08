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
        
        return view('auth/dashboard', $data);
    }

    /**
     * Prepare data for student dashboard display
     */
    private function prepareStudentDashboardData(): array
    {
        $studentId = $this->session->get('userID');
        
        // Empty arrays - ready for real data implementation
        $enrolledCourses = [];
        $upcomingDeadlines = [];
        $recentGrades = [];

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
                'completedCourses' => 0,
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