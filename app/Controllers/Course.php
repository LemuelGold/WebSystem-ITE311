<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;

/**
 * Course Controller - Handles course-related operations including enrollment
 */
class Course extends BaseController
{
    protected $session;
    protected $courseModel;
    protected $enrollmentModel;
    protected $notificationModel;

    public function __construct()
    {
        // Initialize required services
        $this->session = \Config\Services::session();
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Handle course enrollment via AJAX
     * Requirements from lab:
     * - Check if user is logged in
     * - Receive course_id from POST request
     * - Check if user is already enrolled
     * - If not, insert new enrollment record with current timestamp
     * - Return JSON response indicating success or failure
     */
    public function enroll()
    {
        // Set response content type to JSON
        $this->response->setHeader('Content-Type', 'application/json');

        try {
            // Step 1: Check if user is logged in
            if (!$this->isLoggedIn()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized: Please login to enroll in courses.',
                    'code' => 'UNAUTHORIZED'
                ])->setStatusCode(401);
            }

            // Step 2: Verify this is a POST request
            if ($this->request->getMethod() !== 'POST') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid request method. Only POST allowed.',
                    'code' => 'INVALID_METHOD'
                ])->setStatusCode(405);
            }

            // Step 3: Check CSRF protection (security requirement)
            if (!$this->validate(['csrf_token' => 'required'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'CSRF token validation failed.',
                    'code' => 'CSRF_ERROR'
                ])->setStatusCode(403);
            }

            // Step 4: Receive and validate course_id from POST request
            $courseId = $this->request->getPost('course_id');
            $userId = $this->session->get('userID');

            // Input validation - ensure course_id is provided and is numeric
            if (empty($courseId) || !is_numeric($courseId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid course ID provided.',
                    'code' => 'INVALID_COURSE_ID'
                ])->setStatusCode(400);
            }

            // Additional security: Ensure user can only enroll themselves (prevent data tampering)
            if (empty($userId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User session invalid. Please login again.',
                    'code' => 'INVALID_SESSION'
                ])->setStatusCode(401);
            }

            // Step 5: Verify course exists and is active
            if (!$this->courseExists((int)$courseId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Course not found or is inactive.',
                    'code' => 'COURSE_NOT_FOUND'
                ])->setStatusCode(404);
            }

            // Step 6: Check if user is already enrolled to prevent duplicates
            if ($this->enrollmentModel->isAlreadyEnrolled((int)$userId, (int)$courseId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You are already enrolled in this course.',
                    'code' => 'ALREADY_ENROLLED'
                ])->setStatusCode(409); // Conflict status
            }

            // Step 7: Insert new enrollment request with 'pending' status (requires teacher approval)
            $enrollmentData = [
                'student_id' => (int)$userId,
                'course_id' => (int)$courseId
            ];

            $enrollmentId = $this->enrollmentModel->enrollUser($enrollmentData);

            if ($enrollmentId) {
                // Success: Get course details for response
                $courseDetails = $this->getCourseDetails((int)$courseId);
                
                // Get student name for teacher notification
                $studentName = $this->session->get('name');
                
                // Create notification for the student
                $courseTitle = $courseDetails['title'] ?? 'Course';
                $notificationMessage = "Your enrollment request for {$courseTitle} has been submitted and is pending teacher approval.";
                $this->notificationModel->createNotification((int)$userId, $notificationMessage);
                
                // Create notification for the teacher to review the request
                $teacherId = $courseDetails['instructor_id'] ?? null;
                if ($teacherId) {
                    $teacherNotificationMessage = "{$studentName} has requested to enroll in your course: {$courseTitle}. Please review and approve.";
                    $this->notificationModel->createNotification((int)$teacherId, $teacherNotificationMessage);
                }
                
                // Log enrollment request for security auditing
                log_message('info', "User {$userId} submitted enrollment request for course {$courseId}");

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Enrollment request submitted! Waiting for teacher approval.',
                    'data' => [
                        'enrollment_id' => $enrollmentId,
                        'course_id' => (int)$courseId,
                        'course_title' => $courseTitle,
                        'status' => 'pending',
                        'submitted_date' => date('Y-m-d H:i:s')
                    ]
                ])->setStatusCode(200);

            } else {
                // Enrollment failed
                log_message('error', "Enrollment failed for user {$userId} in course {$courseId}");
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Enrollment failed. Please try again later.',
                    'code' => 'ENROLLMENT_FAILED'
                ])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            // Handle unexpected errors
            log_message('critical', 'Enrollment error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.',
                'code' => 'SYSTEM_ERROR'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get available courses for enrollment (for student dashboard)
     */
    public function getAvailableCourses()
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access.'
            ])->setStatusCode(401);
        }

        $userId = $this->session->get('userID');

        // Get courses that user is NOT enrolled in
        $courses = $this->courseModel
            ->select('courses.id, courses.title, courses.description, users.name as instructor_name')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('courses.status', 'active')
            ->whereNotIn('courses.id', function($builder) use ($userId) {
                return $builder->select('course_id')
                              ->from('enrollments')
                              ->where('student_id', $userId);
            })
            ->orderBy('courses.title', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'courses' => $courses
        ]);
    }

    /**
     * Helper method: Check if user is logged in
     */
    private function isLoggedIn(): bool
    {
        return $this->session->get('isLoggedIn') === true;
    }

    /**
     * Helper method: Verify course exists and is active
     */
    private function courseExists(int $courseId): bool
    {
        $course = $this->courseModel->where([
            'id' => $courseId,
            'status' => 'active'
        ])->first();

        return $course !== null;
    }

    /**
     * Helper method: Get course details
     */
    private function getCourseDetails(int $courseId): array
    {
        $course = $this->courseModel->getCourseWithInstructor($courseId);
        return $course ?? [];
    }

    /**
     * Display courses listing page with search functionality
     */
    public function index()
    {
        // Get all active courses with instructor information
        $courses = $this->courseModel->getActiveCourses();

        $data = [
            'title' => 'Course Search',
            'courses' => $courses
        ];

        return view('courses/courses', $data);
    }

    /**
     * Search courses functionality
     * Accepts GET or POST requests with a search term parameter
     * Returns JSON for AJAX requests or renders a view for regular requests
     */
    public function search()
    {
        // Get search term from GET or POST request
        $searchTerm = $this->request->getGet('search_term') ?? $this->request->getPost('search_term');

        // Use CourseModel to search courses
        if (!empty($searchTerm)) {
            $courses = $this->courseModel->searchCourses($searchTerm);
        } else {
            $courses = $this->courseModel->getActiveCourses();
        }

        // Check if this is an AJAX request
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['courses' => $courses, 'searchTerm' => $searchTerm]);
        }

        // For regular requests, render the search results view
        return view('courses/search_results', ['courses' => $courses, 'searchTerm' => $searchTerm]);
    }
}