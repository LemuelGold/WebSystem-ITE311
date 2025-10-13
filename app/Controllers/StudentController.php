<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;

/**
 * StudentController - Handles student-specific functionality and dashboard
 */
class StudentController extends BaseController
{
    protected $session;
    protected $db;
    protected $enrollmentModel;

    public function __construct()
    {
        // Initialize services for student operations
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->enrollmentModel = new EnrollmentModel();
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
        
        // Get enrolled courses using EnrollmentModel
        $enrolledCourses = $this->enrollmentModel->getUserEnrollments($studentId);
        
        // Get available courses (not enrolled in)
        $availableCourses = $this->getAvailableCourses($studentId);
        
        // For now, empty arrays for these features (can be implemented later)
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
            'availableCourses' => $availableCourses,
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
        
        // Since grades are not implemented yet in the enrollment system,
        // return placeholder for now. This can be expanded later when
        // grades/assignments are added to the system.
        return 'N/A';
    }

    /**
     * Get available courses for enrollment (courses student is NOT enrolled in)
     */
    private function getAvailableCourses(int $studentId): array
    {
        $builder = $this->db->table('courses');
        $courses = $builder
            ->select('courses.id, courses.title, courses.description, users.name as instructor_name')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('courses.status', 'active')
            ->whereNotIn('courses.id', function($builder) use ($studentId) {
                return $builder->select('course_id')
                              ->from('enrollments')
                              ->where('student_id', $studentId);
            })
            ->orderBy('courses.title', 'ASC')
            ->get()
            ->getResultArray();

        return $courses ?? [];
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