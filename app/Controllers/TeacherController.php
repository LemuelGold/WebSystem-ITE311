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
        
            // Empty arrays - ready for real data implementation
        $myCourses = [];
        $pendingAssignments = [];
        $newSubmissions = [];

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
                'activeCourses' => 0,
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