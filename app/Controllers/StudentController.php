<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;

/**
 * StudentController - Handles student-specific functionality and dashboard
 */
class StudentController extends BaseController
{
    protected $session;
    protected $db;
    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        // Initialize services for student operations
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->courseModel = new CourseModel();
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
        
        // Get enrolled courses using EnrollmentModel (approved only)
        $enrolledCourses = $this->enrollmentModel->getUserEnrollments($studentId);
        
        // Get pending enrollment requests
        $pendingEnrollments = $this->enrollmentModel->getStudentPendingEnrollments($studentId);
        
        // Get available courses (not enrolled in and no pending requests)
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
                'pendingAssignments' => count($upcomingDeadlines),
                'pendingEnrollments' => count($pendingEnrollments)
            ],
            'enrolledCourses' => $enrolledCourses,
            'pendingEnrollments' => $pendingEnrollments,
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
     * Get available courses for enrollment (courses student has NOT enrolled in or requested)
     */
    private function getAvailableCourses(int $studentId): array
    {
        $courses = $this->courseModel
            ->select('courses.id, courses.title, courses.description, users.name as instructor_name')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('courses.status', 'active')
            ->whereNotIn('courses.id', function($builder) use ($studentId) {
                // Exclude courses with any enrollment status (pending, enrolled, etc.)
                return $builder->select('course_id')
                              ->from('enrollments')
                              ->where('student_id', $studentId)
                              ->whereIn('status', ['pending', 'enrolled', 'completed']);
            })
            ->orderBy('courses.title', 'ASC')
            ->findAll();

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

        $studentId = $this->session->get('userID');
        $enrolledCourses = $this->enrollmentModel->getUserEnrollments($studentId);

        $data = [
            'title' => 'My Courses - Student Panel',
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ],
            'enrolledCourses' => $enrolledCourses
        ];

        return view('student/my_courses', $data);
    }

    /**
     * View specific course details
     */
    public function viewCourse($courseId)
    {
        // Authorization check for student role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'student') {
            $this->session->setFlashdata('error', 'Access denied. Student privileges required.');
            return redirect()->to(base_url('login'));
        }

        $studentId = $this->session->get('userID');
        $courseId = (int)$courseId; // Cast to integer for safety
        
        // Check if student is enrolled in this course
        if (!$this->enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
            $this->session->setFlashdata('error', 'You are not enrolled in this course.');
            return redirect()->to(base_url('student/dashboard'));
        }

        // Get course details
        $courseDetails = $this->getCourseDetails($courseId);
        if (!$courseDetails) {
            $this->session->setFlashdata('error', 'Course not found.');
            return redirect()->to(base_url('student/dashboard'));
        }

        $data = [
            'title' => 'Course: ' . $courseDetails['title'],
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ],
            'course' => $courseDetails
        ];

        return view('student/course_detail', $data);
    }

    /**
     * View downloadable materials for enrolled courses
     */
    public function viewMaterials()
    {
        // Authorization check
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'student') {
            $this->session->setFlashdata('error', 'Access denied. Student privileges required.');
            return redirect()->to(base_url('login'));
        }

        $studentId = $this->session->get('userID');

        // Get materials for student's enrolled courses
        $materialModel = new \App\Models\MaterialModel();
        $materials = $materialModel->getMaterialsForStudent($studentId);

        $data = [
            'title' => 'Course Materials',
            'materials' => $materials,
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('materials/student_materials', $data);
    }

    // Assignments and grades functionality will be added later
    // For now, focusing on core course enrollment and viewing functionality

    /**
     * Get course details by ID
     */
    private function getCourseDetails(int $courseId): array
    {
        $builder = $this->db->table('courses');
        $course = $builder
            ->select('courses.*, users.name as instructor_name')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('courses.id', $courseId)
            ->where('courses.status', 'active')
            ->get()
            ->getRowArray();

        return $course ?? [];
    }
}