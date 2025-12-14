<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use App\Models\ProgramModel;
use App\Models\StudentProgramModel;

/**
 * Course Controller - Handles course-related operations including enrollment
 */
class Course extends BaseController
{
    protected $session;
    protected $courseModel;
    protected $enrollmentModel;
    protected $notificationModel;
    protected $programModel;
    protected $studentProgramModel;

    public function __construct()
    {
        // Initialize required services
        $this->session = \Config\Services::session();
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->notificationModel = new NotificationModel();
        $this->programModel = new ProgramModel();
        $this->studentProgramModel = new StudentProgramModel();
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
        // Check if this is an AJAX request
        $isAjax = $this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
        
        // Set response content type to JSON for AJAX requests
        if ($isAjax) {
            $this->response->setHeader('Content-Type', 'application/json');
        }

        try {
            // Step 1: Check if user is logged in
            if (!$this->isLoggedIn()) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Unauthorized: Please login to enroll in courses.',
                        'code' => 'UNAUTHORIZED'
                    ])->setStatusCode(401);
                } else {
                    $this->session->setFlashdata('error', 'Please login to enroll in courses.');
                    return redirect()->to('login');
                }
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
            // Temporarily disable CSRF validation to test enrollment
            /*
            if (!$this->validate(['csrf_token' => 'required'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'CSRF token validation failed.',
                    'code' => 'CSRF_ERROR'
                ])->setStatusCode(403);
            }
            */

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
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Course not found or is inactive.',
                        'code' => 'COURSE_NOT_FOUND'
                    ])->setStatusCode(404);
                } else {
                    $this->session->setFlashdata('error', 'Course not found or is inactive.');
                    return redirect()->to('courses');
                }
            }

            // Step 5.5: Check if course has an instructor assigned
            $courseDetails = $this->getCourseDetails((int)$courseId);
            if (empty($courseDetails['instructor_id']) || $courseDetails['instructor_id'] == null) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'This course does not have an instructor assigned yet. Enrollment is not available.',
                        'code' => 'NO_INSTRUCTOR'
                    ])->setStatusCode(400);
                } else {
                    $this->session->setFlashdata('error', 'This course does not have an instructor assigned yet. Enrollment is not available.');
                    return redirect()->to('courses/view/' . $courseId);
                }
            }

            // Step 6: Check if user is already enrolled to prevent duplicates
            if ($this->enrollmentModel->isAlreadyEnrolled((int)$userId, (int)$courseId)) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'You are already enrolled in this course.',
                        'code' => 'ALREADY_ENROLLED'
                    ])->setStatusCode(409); // Conflict status
                } else {
                    $this->session->setFlashdata('error', 'You are already enrolled in this course.');
                    return redirect()->to('courses/view/' . $courseId);
                }
            }

            // Step 6.5: Check if user has exceeded maximum enrollment attempts
            if ($this->enrollmentModel->hasExceededAttempts((int)$userId, (int)$courseId)) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'You have reached the maximum number of enrollment attempts (3) for this course.',
                        'code' => 'MAX_ATTEMPTS_EXCEEDED'
                    ])->setStatusCode(403); // Forbidden status
                } else {
                    $this->session->setFlashdata('error', 'You have reached the maximum number of enrollment attempts (3) for this course.');
                    return redirect()->to('courses/view/' . $courseId);
                }
            }

            // Step 7: Insert new enrollment request with 'pending' status (requires teacher approval)
            $enrollmentData = [
                'user_id' => (int)$userId,
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

                if ($isAjax) {
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
                    // For form submissions, redirect back with success message
                    $this->session->setFlashdata('success', 'Enrollment request submitted! Waiting for teacher approval.');
                    return redirect()->to('courses/view/' . $courseId);
                }

            } else {
                // Enrollment failed
                log_message('error', "Enrollment failed for user {$userId} in course {$courseId}");
                
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Enrollment failed. Please try again later.',
                        'code' => 'ENROLLMENT_FAILED'
                    ])->setStatusCode(500);
                } else {
                    // For form submissions, redirect back with error message
                    $this->session->setFlashdata('error', 'Enrollment failed. Please try again later.');
                    return redirect()->to('courses/view/' . $courseId);
                }
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
     * Only shows courses from the student's enrolled program
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
        $userRole = $this->session->get('role');

        // For students: Only show courses from their enrolled program
        if ($userRole === 'student') {
            $studentProgram = $this->studentProgramModel->getStudentProgram($userId);
            
            if (!$studentProgram) {
                // Student is not enrolled in any program
                return $this->response->setJSON([
                    'success' => true,
                    'courses' => [],
                    'message' => 'You are not enrolled in any program yet.'
                ]);
            }

            // Get courses from the student's program that they haven't enrolled in
            $programCourses = $this->programModel->getProgramCourses($studentProgram['program_id']);
            
            if (empty($programCourses)) {
                return $this->response->setJSON([
                    'success' => true,
                    'courses' => [],
                    'message' => 'No courses available in your program yet.'
                ]);
            }

            // Get available courses from the student's program
            $courses = $this->courseModel
                ->select('courses.id, courses.course_code, courses.title, courses.description, courses.units,
                         users.name as instructor_name, program_courses.year_level, program_courses.semester, 
                         program_courses.is_required')
                ->join('users', 'users.id = courses.instructor_id', 'left')
                ->join('program_courses', 'program_courses.course_id = courses.id', 'inner')
                ->where('courses.status', 'active')
                ->where('program_courses.program_id', $studentProgram['program_id'])
                ->whereNotIn('courses.id', function($builder) use ($userId) {
                    return $builder->select('course_id')
                                  ->from('enrollments')
                                  ->where('user_id', $userId)
                                  ->whereIn('status', ['pending', 'approved', 'confirmed', 'completed']);
                })
                ->orderBy('program_courses.year_level', 'ASC')
                ->orderBy('program_courses.semester', 'ASC')
                ->orderBy('courses.title', 'ASC')
                ->findAll();
        } else {
            // For non-students: Show all courses they haven't enrolled in
            $courses = $this->courseModel
                ->select('courses.id, courses.title, courses.description, users.name as instructor_name')
                ->join('users', 'users.id = courses.instructor_id', 'left')
                ->where('courses.status', 'active')
                ->whereNotIn('courses.id', function($builder) use ($userId) {
                    return $builder->select('course_id')
                                  ->from('enrollments')
                                  ->where('user_id', $userId);
                })
                ->orderBy('courses.title', 'ASC')
                ->findAll();
        }

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
     * For students: Only shows courses from their enrolled program
     * For admins/teachers: Shows all courses
     */
    public function index()
    {
        $courses = [];
        $studentProgram = null;
        $userRole = $this->session->get('role');
        
        if ($this->isLoggedIn() && $userRole === 'student') {
            // For students: Only show courses from their enrolled program
            $studentId = $this->session->get('userID');
            $studentProgram = $this->studentProgramModel->getStudentProgram($studentId);
            
            if ($studentProgram) {
                // Get courses from the student's program
                $programCourses = $this->programModel->getProgramCourses($studentProgram['program_id']);
                
                if (!empty($programCourses)) {
                    // Extract course IDs from program courses
                    $programCourseIds = array_column($programCourses, 'course_id');
                    
                    // Get course details with instructor information
                    $courses = $this->courseModel
                        ->select('courses.*, users.name as instructor_name, program_courses.year_level, 
                                 program_courses.semester, program_courses.is_required')
                        ->join('users', 'users.id = courses.instructor_id', 'left')
                        ->join('program_courses', 'program_courses.course_id = courses.id', 'inner')
                        ->where('courses.status', 'active')
                        ->where('program_courses.program_id', $studentProgram['program_id'])
                        ->orderBy('program_courses.year_level', 'ASC')
                        ->orderBy('program_courses.semester', 'ASC')
                        ->orderBy('courses.title', 'ASC')
                        ->findAll();
                }
            }
            // If student is not enrolled in any program, courses array remains empty
        } else {
            // For admins, teachers, or non-logged in users: Show all active courses
            $courses = $this->courseModel->getActiveCourses();
        }

        $data = [
            'title' => 'Course Search',
            'courses' => $courses,
            'studentProgram' => $studentProgram,
            'userRole' => $userRole,
            'isLoggedIn' => $this->isLoggedIn()
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

    /**
     * View individual course details
     */
    public function view($courseId)
    {
        // Validate course ID
        if (empty($courseId) || !is_numeric($courseId)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Course not found');
        }

        // Get course details with instructor information
        $course = $this->courseModel->getCourseWithInstructor((int)$courseId);
        
        if (!$course) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Course not found');
        }

        // Check if user is logged in and get enrollment status
        $enrollmentStatus = null;
        $attemptInfo = null;
        if ($this->isLoggedIn()) {
            $userId = $this->session->get('userID');
            $enrollment = $this->enrollmentModel->getEnrollmentStatus($userId, (int)$courseId);
            $enrollmentStatus = $enrollment['status'] ?? null;
            
            // Get attempt information for students (only if not already enrolled/approved)
            if ($this->session->get('role') === 'student' && $enrollmentStatus !== 'approved') {
                $totalAttempts = $this->enrollmentModel->getTotalAttempts($userId, (int)$courseId);
                $hasExceededAttempts = $this->enrollmentModel->hasExceededAttempts($userId, (int)$courseId);
                $remainingAttempts = max(0, 3 - $totalAttempts);
                
                $attemptInfo = [
                    'totalAttempts' => $totalAttempts,
                    'remainingAttempts' => $remainingAttempts,
                    'hasExceededAttempts' => $hasExceededAttempts
                ];
            }
        }

        $data = [
            'title' => $course['title'] . ' - Course Details',
            'course' => $course,
            'enrollmentStatus' => $enrollmentStatus,
            'attemptInfo' => $attemptInfo,
            'isLoggedIn' => $this->isLoggedIn(),
            'user' => [
                'userID' => $this->session->get('userID'),
                'name' => $this->session->get('name'),
                'role' => $this->session->get('role')
            ]
        ];

        return view('courses/course_detail', $data);
    }

    /**
     * Cancel enrollment request
     */
    public function cancelEnrollment()
    {
        // Check if this is an AJAX request
        $isAjax = $this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
        
        // Set response content type to JSON for AJAX requests
        if ($isAjax) {
            $this->response->setHeader('Content-Type', 'application/json');
        }

        try {
            // Check if user is logged in
            if (!$this->isLoggedIn()) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Unauthorized: Please login to cancel enrollment.',
                        'code' => 'UNAUTHORIZED'
                    ])->setStatusCode(401);
                } else {
                    $this->session->setFlashdata('error', 'Please login to cancel enrollment.');
                    return redirect()->to('login');
                }
            }

            // Verify this is a POST request
            if ($this->request->getMethod() !== 'POST') {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid request method. Only POST allowed.',
                        'code' => 'INVALID_METHOD'
                    ])->setStatusCode(405);
                } else {
                    $this->session->setFlashdata('error', 'Invalid request method.');
                    return redirect()->to('courses');
                }
            }

            // Get course_id from POST request
            $courseId = $this->request->getPost('course_id');
            $userId = $this->session->get('userID');

            // Input validation
            if (empty($courseId) || !is_numeric($courseId)) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid course ID provided.',
                        'code' => 'INVALID_COURSE_ID'
                    ])->setStatusCode(400);
                } else {
                    $this->session->setFlashdata('error', 'Invalid course ID provided.');
                    return redirect()->to('courses');
                }
            }

            // Check if user has a pending enrollment for this course
            $enrollment = $this->enrollmentModel->getEnrollmentStatus($userId, (int)$courseId);
            if (!$enrollment || $enrollment['status'] !== 'pending') {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'No pending enrollment found for this course.',
                        'code' => 'NO_PENDING_ENROLLMENT'
                    ])->setStatusCode(404);
                } else {
                    $this->session->setFlashdata('error', 'No pending enrollment found for this course.');
                    return redirect()->to('courses/view/' . $courseId);
                }
            }

            // Cancel the enrollment (delete the record)
            $success = $this->enrollmentModel->dropEnrollment($userId, (int)$courseId);

            if ($success) {
                // Log the cancellation
                log_message('info', "User {$userId} cancelled enrollment request for course {$courseId}");

                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Enrollment request cancelled successfully.',
                    ])->setStatusCode(200);
                } else {
                    $this->session->setFlashdata('success', 'Enrollment request cancelled successfully.');
                    return redirect()->to('courses/view/' . $courseId);
                }
            } else {
                // Cancellation failed
                log_message('error', "Failed to cancel enrollment for user {$userId} in course {$courseId}");
                
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to cancel enrollment. Please try again.',
                        'code' => 'CANCELLATION_FAILED'
                    ])->setStatusCode(500);
                } else {
                    $this->session->setFlashdata('error', 'Failed to cancel enrollment. Please try again.');
                    return redirect()->to('courses/view/' . $courseId);
                }
            }

        } catch (\Exception $e) {
            // Handle unexpected errors
            log_message('critical', 'Enrollment cancellation error: ' . $e->getMessage());
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'An unexpected error occurred. Please try again.',
                    'code' => 'SYSTEM_ERROR'
                ])->setStatusCode(500);
            } else {
                $this->session->setFlashdata('error', 'An unexpected error occurred. Please try again.');
                return redirect()->to('courses');
            }
        }
    }
}