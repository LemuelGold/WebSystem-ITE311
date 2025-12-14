<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use App\Models\ProgramModel;
use App\Models\StudentProgramModel;

/**
 * StudentController - Handles student-specific functionality and dashboard
 */
class StudentController extends BaseController
{
    protected $session;
    protected $db;
    protected $courseModel;
    protected $enrollmentModel;
    protected $notificationModel;
    protected $programModel;
    protected $studentProgramModel;

    public function __construct()
    {
        // Initialize services for student operations
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->notificationModel = new NotificationModel();
        $this->programModel = new ProgramModel();
        $this->studentProgramModel = new StudentProgramModel();
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
        
        // Get student's program enrollment
        $studentProgram = $this->studentProgramModel->getStudentProgram($studentId);
        
        // Get enrolled courses using EnrollmentModel (confirmed only)
        $enrolledCourses = $this->enrollmentModel->getUserEnrollments($studentId);
        
        // Get pending enrollment requests
        $pendingEnrollments = $this->enrollmentModel->getStudentPendingEnrollments($studentId);
        
        // Get approved enrollments waiting for confirmation
        $approvedEnrollments = $this->enrollmentModel->getStudentApprovedEnrollments($studentId);
        
        // Get available courses (not enrolled in and no pending requests)
        $availableCourses = $this->getAvailableCourses($studentId);
        
        // For now, empty arrays for these features (can be implemented later)
        $upcomingDeadlines = [];
        $recentGrades = [];

        return [
            'title' => 'Student Dashboard - RESTAURO LMS',
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'email'  => $this->session->get('email'),
                'role'   => $this->session->get('role')
            ],
            'studentProgram' => $studentProgram,
            'stats' => [
                'enrolledCourses' => count($enrolledCourses),
                'completedCourses' => 0,
                'averageGrade' => $this->calculateAverageGrade($enrolledCourses),
                'pendingAssignments' => count($upcomingDeadlines),
                'pendingEnrollments' => count($pendingEnrollments)
            ],
            'enrolledCourses' => $enrolledCourses,
            'pendingEnrollments' => $pendingEnrollments,
            'approvedEnrollments' => $approvedEnrollments,
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
     * Only shows courses from the student's enrolled program
     */
    private function getAvailableCourses(int $studentId): array
    {
        // First, get the student's program enrollment
        $studentProgram = $this->studentProgramModel->getStudentProgram($studentId);
        
        if (!$studentProgram) {
            // Student is not enrolled in any program, return empty array
            return [];
        }

        // Get courses that are part of the student's program
        $programCourses = $this->programModel->getProgramCourses($studentProgram['program_id']);
        
        if (empty($programCourses)) {
            // No courses assigned to the program yet
            return [];
        }

        // Extract course IDs from program courses
        $programCourseIds = array_column($programCourses, 'course_id');

        // Get available courses from the student's program that they haven't enrolled in
        $courses = $this->courseModel
            ->select('courses.id, courses.course_code, courses.title, courses.description, courses.units, 
                     users.name as instructor_name, program_courses.year_level, program_courses.semester, 
                     program_courses.is_required, program_courses.prerequisite_course_id')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->join('program_courses', 'program_courses.course_id = courses.id', 'inner')
            ->where('courses.status', 'active')
            ->where('program_courses.program_id', $studentProgram['program_id'])
            ->whereNotIn('courses.id', function($builder) use ($studentId) {
                // Exclude courses with any enrollment status (pending, approved, confirmed, etc.)
                return $builder->select('course_id')
                              ->from('enrollments')
                              ->where('user_id', $studentId)
                              ->whereIn('status', ['pending', 'approved', 'confirmed', 'completed']);
            })
            ->orderBy('program_courses.year_level', 'ASC')
            ->orderBy('program_courses.semester', 'ASC')
            ->orderBy('courses.title', 'ASC')
            ->findAll();

        return $courses ?? [];
    }

    /**
     * Check if user is logged in and session is valid
     */
    private function isLoggedIn(): bool
    {
        if ($this->session->get('isLoggedIn') !== true) {
            return false;
        }
        
        // Check if session token is still valid (for auto-logout on profile changes)
        $userId = $this->session->get('userID');
        $sessionToken = $this->session->get('sessionToken');
        
        if ($userId) {
            $usersBuilder = $this->db->table('users');
            $user = $usersBuilder->where('id', $userId)->get()->getRowArray();
            
            // If user doesn't exist or is soft deleted, invalidate session
            if (!$user || !empty($user['deleted_at'])) {
                $this->session->destroy();
                return false;
            }
            
            // If session token exists in database but doesn't match current session, user was logged out
            if (!empty($user['session_token']) && $user['session_token'] !== $sessionToken) {
                $this->session->destroy();
                return false;
            }
        }
        
        return true;
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
        $userId = $this->session->get('userID');
        
        $builder = $this->db->table('courses');
        $course = $builder
            ->select('courses.*, users.name as instructor_name, enrollments.enrollment_date, enrollments.created_at as enrollment_created_at')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->join('enrollments', 'enrollments.course_id = courses.id AND enrollments.user_id = ' . $userId . ' AND enrollments.status = "approved"', 'left')
            ->where('courses.id', $courseId)
            ->where('courses.status', 'active')
            ->get()
            ->getRowArray();

        return $course ?? [];
    }

    /**
     * Accept an approved enrollment (student confirms they want to take the course)
     */
    public function acceptEnrollment()
    {
        // Authorization check for student role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'student') {
            $this->session->setFlashdata('error', 'Access denied. Student privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $enrollmentId = $this->request->getPost('enrollment_id');
            $studentId = $this->session->get('userID');

            // Validate input
            if (empty($enrollmentId)) {
                $this->session->setFlashdata('error', 'Invalid enrollment ID.');
                return redirect()->back();
            }

            // Get enrollment details
            $enrollmentsBuilder = $this->db->table('enrollments');
            $enrollment = $enrollmentsBuilder
                ->select('enrollments.*, courses.title as course_title, courses.instructor_id, users.name as instructor_name')
                ->join('courses', 'courses.id = enrollments.course_id', 'inner')
                ->join('users', 'users.id = courses.instructor_id', 'left')
                ->where('enrollments.id', $enrollmentId)
                ->where('enrollments.user_id', $studentId)
                ->whereIn('enrollments.status', ['approved', 'pending'])
                ->get()
                ->getRowArray();

            if (!$enrollment) {
                $this->session->setFlashdata('error', 'Enrollment not found or not available for confirmation.');
                return redirect()->back();
            }

            // Accept enrollment (change status to confirmed)
            if ($enrollmentsBuilder->where('id', $enrollmentId)->update(['status' => 'confirmed'])) {
                // Send notification to instructor if assigned
                if (!empty($enrollment['instructor_id'])) {
                    $studentName = $this->session->get('name');
                    $notificationMessage = "{$studentName} has accepted enrollment in your course '{$enrollment['course_title']}'.";
                    $this->notificationModel->createNotification($enrollment['instructor_id'], $notificationMessage);
                }

                $this->session->setFlashdata('success', "You have successfully enrolled in {$enrollment['course_title']}!");
            } else {
                $this->session->setFlashdata('error', 'Failed to confirm enrollment.');
            }
        }

        return redirect()->to(base_url('student/dashboard'));
    }

    /**
     * Decline an approved enrollment (student rejects the course)
     */
    public function declineEnrollment()
    {
        // Authorization check for student role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'student') {
            $this->session->setFlashdata('error', 'Access denied. Student privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $enrollmentId = $this->request->getPost('enrollment_id');
            $studentId = $this->session->get('userID');

            // Validate input
            if (empty($enrollmentId)) {
                $this->session->setFlashdata('error', 'Invalid enrollment ID.');
                return redirect()->back();
            }

            // Get enrollment details
            $enrollmentsBuilder = $this->db->table('enrollments');
            $enrollment = $enrollmentsBuilder
                ->select('enrollments.*, courses.title as course_title, courses.instructor_id, users.name as instructor_name')
                ->join('courses', 'courses.id = enrollments.course_id', 'inner')
                ->join('users', 'users.id = courses.instructor_id', 'left')
                ->where('enrollments.id', $enrollmentId)
                ->where('enrollments.user_id', $studentId)
                ->whereIn('enrollments.status', ['approved', 'pending'])
                ->get()
                ->getRowArray();

            if (!$enrollment) {
                $this->session->setFlashdata('error', 'Enrollment not found or not available for decline.');
                return redirect()->back();
            }

            // Decline enrollment (change status to declined)
            if ($enrollmentsBuilder->where('id', $enrollmentId)->update(['status' => 'declined'])) {
                // Send notification to instructor if assigned
                if (!empty($enrollment['instructor_id'])) {
                    $studentName = $this->session->get('name');
                    $notificationMessage = "{$studentName} has declined enrollment in your course '{$enrollment['course_title']}'.";
                    $this->notificationModel->createNotification($enrollment['instructor_id'], $notificationMessage);
                }

                $this->session->setFlashdata('success', "You have declined enrollment in {$enrollment['course_title']}.");
            } else {
                $this->session->setFlashdata('error', 'Failed to decline enrollment.');
            }
        }

        return redirect()->to(base_url('student/dashboard'));
    }
}