<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\NotificationModel;

/**
 * Course Controller - Handles course-related operations including enrollment
 */
class Course extends BaseController
{
    protected $session;
    protected $enrollmentModel;
    protected $notificationModel;
    protected $db;

    public function __construct()
    {
        // Initialize required services
        $this->session = \Config\Services::session();
        $this->enrollmentModel = new EnrollmentModel();
        $this->notificationModel = new NotificationModel();
        $this->db = \Config\Database::connect();
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

            // Step 7: Insert new enrollment record with current timestamp
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
                $notificationMessage = "You have been successfully enrolled in {$courseTitle}";
                $this->notificationModel->createNotification((int)$userId, $notificationMessage);
                
                // Create notification for the teacher
                $teacherId = $courseDetails['instructor_id'] ?? null;
                if ($teacherId) {
                    $teacherNotificationMessage = "{$studentName} has enrolled in your course: {$courseTitle}";
                    $this->notificationModel->createNotification((int)$teacherId, $teacherNotificationMessage);
                }
                
                // Log successful enrollment for security auditing
                log_message('info', "User {$userId} successfully enrolled in course {$courseId}");

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Successfully enrolled in the course!',
                    'data' => [
                        'enrollment_id' => $enrollmentId,
                        'course_id' => (int)$courseId,
                        'course_title' => $courseTitle,
                        'enrollment_date' => date('Y-m-d H:i:s')
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
        $builder = $this->db->table('courses');
        $courses = $builder
            ->select('courses.id, courses.title, courses.description, users.name as instructor_name')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('courses.status', 'active')
            ->whereNotIn('courses.id', function($builder) use ($userId) {
                return $builder->select('course_id')
                              ->from('enrollments')
                              ->where('student_id', $userId);
            })
            ->orderBy('courses.title', 'ASC')
            ->get()
            ->getResultArray();

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
        $builder = $this->db->table('courses');
        $course = $builder->where([
            'id' => $courseId,
            'status' => 'active'
        ])->get()->getRowArray();

        return $course !== null;
    }

    /**
     * Helper method: Get course details
     */
    private function getCourseDetails(int $courseId): array
    {
        $builder = $this->db->table('courses');
        $course = $builder
            ->select('courses.*, users.name as instructor_name')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('courses.id', $courseId)
            ->get()
            ->getRowArray();

        return $course ?? [];
    }
}