<?php

namespace App\Controllers;

/**
 * TeacherController - Handles teacher-specific functionality and dashboard
 */
class TeacherController extends BaseController
{
    protected $session;
    protected $db;

    public function __construct()
    {
        // Initialize services for teacher operations
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
    }

    /**
     * Teacher Dashboard - displays teacher-specific information and tools
     */
    public function dashboard()
    {
        // Authorization check - ensure user is logged in and has teacher role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'teacher') {
            $this->session->setFlashdata('error', 'Access denied. Teacher privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Prepare teacher-specific dashboard data
        $data = $this->prepareTeacherDashboardData();
        
        return view('auth/dashboard', $data);
    }

    /**
     * Prepare data for teacher dashboard display
     */
    private function prepareTeacherDashboardData(): array
    {
        $teacherId = $this->session->get('userID');
        
        // For demo purposes - simulated data (would come from courses/assignments tables)
        $myCourses = [
            ['id' => 1, 'name' => 'Web Development Fundamentals', 'students' => 25, 'status' => 'Active'],
            ['id' => 2, 'name' => 'Database Management Systems', 'students' => 18, 'status' => 'Active'],
            ['id' => 3, 'name' => 'Software Engineering', 'students' => 22, 'status' => 'Completed']
        ];

        // Simulated pending assignments for review
        $pendingAssignments = [
            ['student' => 'John Doe', 'course' => 'Web Development', 'assignment' => 'Final Project', 'submitted' => '2025-09-20'],
            ['student' => 'Jane Smith', 'course' => 'Database Management', 'assignment' => 'Lab Exercise 3', 'submitted' => '2025-09-19'],
            ['student' => 'Mike Johnson', 'course' => 'Software Engineering', 'assignment' => 'UML Diagrams', 'submitted' => '2025-09-18']
        ];
        
        // New assignment submissions that need teacher attention
        $newSubmissions = [
            ['student' => 'John Doe', 'assignment' => 'Final Project', 'time' => '2 hours'],
            ['student' => 'Jane Smith', 'assignment' => 'Lab Exercise 3', 'time' => '5 hours'],
            ['student' => 'Mike Johnson', 'assignment' => 'UML Diagrams', 'time' => '1 day']
        ];

        // Get student count from database
        $studentsBuilder = $this->db->table('users');
        $totalStudents = $studentsBuilder->where('role', 'student')->countAllResults();

        return [
            'title' => 'Teacher Dashboard - ITE311 FUNDAR',
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'email'  => $this->session->get('email'),
                'role'   => $this->session->get('role')
            ],
            'stats' => [
                'totalCourses' => count($myCourses),
                'activeCourses' => count(array_filter($myCourses, fn($course) => $course['status'] === 'Active')),
                'totalStudents' => $totalStudents,
                'pendingReviews' => count($pendingAssignments)
            ],
            'myCourses' => $myCourses,
            'pendingAssignments' => $pendingAssignments,
            'newSubmissions' => $newSubmissions
        ];
    }

    /**
     * Check if user is logged in
     */
    private function isLoggedIn(): bool
    {
        return $this->session->get('isLoggedIn') === true;
    }

    /**
     * Course Management - List teacher's courses
     */
    public function manageCourses()
    {
        // Authorization check for teacher role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'teacher') {
            $this->session->setFlashdata('error', 'Access denied. Teacher privileges required.');
            return redirect()->to(base_url('login'));
        }

        // This would typically fetch courses from database where teacher_id = current user
        $data = [
            'title' => 'Course Management - Teacher Panel',
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('teacher/manage_courses', $data);
    }
}