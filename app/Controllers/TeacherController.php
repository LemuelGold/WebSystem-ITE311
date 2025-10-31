<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\NotificationModel;

/**
 * TeacherController - Handles teacher-specific functionality and dashboard
 */
class TeacherController extends BaseController
{
    protected $session;
    protected $db;
    protected $enrollmentModel;
    protected $notificationModel;

    public function __construct()
    {
        // Initialize services for teacher operations
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->enrollmentModel = new EnrollmentModel();
        $this->notificationModel = new NotificationModel();
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
        
        // Get actual courses that belong to this teacher (only active courses)
        $myCourses = $this->db->table('courses')
            ->select('*')
            ->where('instructor_id', $teacherId)
            ->where('status', 'active')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
        
        // Remove any duplicate courses based on course ID (safety check)
        $uniqueCourses = [];
        $seenIds = [];
        foreach ($myCourses as $course) {
            if (!in_array($course['id'], $seenIds)) {
                $seenIds[] = $course['id'];
                $uniqueCourses[] = $course;
            }
        }
        $myCourses = $uniqueCourses;
        
        // Debug: Log the courses to check for duplicates
        log_message('debug', 'Teacher ' . $teacherId . ' courses count: ' . count($myCourses));
        foreach ($myCourses as $debugCourse) {
            log_message('debug', 'Course ID: ' . $debugCourse['id'] . ' - ' . $debugCourse['title']);
        }
        
        // Get enrollment count for each course
        foreach ($myCourses as &$course) {
            $enrollmentCount = $this->db->table('enrollments')
                ->where('course_id', $course['id'])
                ->countAllResults();
            $course['students'] = $enrollmentCount;
            $course['name'] = $course['title']; // Add name field for compatibility
        }
        unset($course); // Break the reference
        
        // Count active courses
        $activeCourses = 0;
        foreach ($myCourses as $course) {
            if ($course['status'] === 'active') {
                $activeCourses++;
            }
        }
        
        // For now, keep these as empty arrays - can be implemented later
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
                'activeCourses' => $activeCourses,
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

        // Get teacher's courses
        $teacherId = $this->session->get('userID');
        $coursesBuilder = $this->db->table('courses');
        $myCourses = $coursesBuilder
            ->where('instructor_id', $teacherId)
            ->where('status', 'active')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'My Courses - Teacher Panel',
            'courses' => $myCourses,
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('teacher/manage_courses', $data);
    }

    /**
     * View students in a specific course
     */
    public function viewCourseStudents($courseId)
    {
        // Authorization check for teacher role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'teacher') {
            $this->session->setFlashdata('error', 'Access denied. Teacher privileges required.');
            return redirect()->to(base_url('login'));
        }

        $teacherId = $this->session->get('userID');

        // Verify this course belongs to the teacher
        $coursesBuilder = $this->db->table('courses');
        $courseCheck = $coursesBuilder->where('id', $courseId)
            ->where('instructor_id', $teacherId)
            ->get()
            ->getRowArray();
        
        if (!$courseCheck) {
            $this->session->setFlashdata('error', 'Access denied. You can only manage students in your own courses.');
            return redirect()->to(base_url('teacher/courses'));
        }

        // Get course details with instructor info
        $coursesBuilder = $this->db->table('courses');
        $course = $coursesBuilder
            ->select('courses.*, users.name as instructor_name, users.email as instructor_email')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('courses.id', $courseId)
            ->get()
            ->getRowArray();

        if (!$course) {
            $this->session->setFlashdata('error', 'Course not found.');
            return redirect()->to(base_url('teacher/courses'));
        }

        // Get enrollment count
        $enrollmentCount = $this->db->table('enrollments')
            ->where('course_id', $courseId)
            ->countAllResults();
        $course['enrollment_count'] = $enrollmentCount;

        // Get enrolled students
        $enrolledStudents = $this->db->table('enrollments')
            ->select('users.id, users.name, users.email, enrollments.enrollment_date, enrollments.id as enrollment_id')
            ->join('users', 'users.id = enrollments.student_id', 'inner')
            ->where('enrollments.course_id', $courseId)
            ->where('users.role', 'student')
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->get()
            ->getResultArray();
        
        // Get available students (not enrolled)
        $enrolledIds = $this->db->table('enrollments')
            ->select('student_id')
            ->where('course_id', $courseId)
            ->get()
            ->getResultArray();
        
        $enrolledStudentIds = array_column($enrolledIds, 'student_id');
        
        // Get all students not in the enrolled list
        $studentsBuilder = $this->db->table('users');
        $studentsBuilder->select('id, name, email')
            ->where('role', 'student');
        
        if (!empty($enrolledStudentIds)) {
            $studentsBuilder->whereNotIn('id', $enrolledStudentIds);
        }
        
        $availableStudents = $studentsBuilder->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Manage Students - ' . $course['title'],
            'course' => $course,
            'enrolledStudents' => $enrolledStudents,
            'availableStudents' => $availableStudents,
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('teacher/course_students', $data);
    }

    /**
     * Add student to course
     */
    public function addStudentToCourse()
    {
        // Authorization check for teacher role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'teacher') {
            $this->session->setFlashdata('error', 'Access denied. Teacher privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $courseId = $this->request->getPost('course_id');
            $studentId = $this->request->getPost('student_id');
            $teacherId = $this->session->get('userID');

            // Verify this course belongs to the teacher
            $coursesBuilder = $this->db->table('courses');
            $courseCheck = $coursesBuilder->where('id', $courseId)
                ->where('instructor_id', $teacherId)
                ->get()
                ->getRowArray();
            
            if (!$courseCheck) {
                $this->session->setFlashdata('error', 'Access denied. You can only add students to your own courses.');
                return redirect()->to(base_url('teacher/courses'));
            }

            // Verify student exists and has student role
            $usersBuilder = $this->db->table('users');
            $student = $usersBuilder->where('id', $studentId)
                ->where('role', 'student')
                ->get()
                ->getRowArray();

            if (!$student) {
                $this->session->setFlashdata('error', 'Invalid student selected.');
                return redirect()->to(base_url("teacher/course/{$courseId}/students"));
            }

            // Check if already enrolled
            if ($this->enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
                $this->session->setFlashdata('error', 'Student is already enrolled in this course.');
                return redirect()->to(base_url("teacher/course/{$courseId}/students"));
            }

            // Enroll student
            $enrollmentData = [
                'student_id' => $studentId,
                'course_id' => $courseId
            ];

            if ($this->enrollmentModel->enrollUser($enrollmentData)) {
                // Get course title and teacher name for notifications
                $courseTitle = $courseCheck['title'] ?? 'a course';
                $teacherName = $this->session->get('name');
                $studentName = $student['name'];
                
                // Create notification for the STUDENT (added by teacher)
                $studentNotificationMessage = "You have been added to the course: {$courseTitle} by {$teacherName}";
                $this->notificationModel->createNotification((int)$studentId, $studentNotificationMessage);
                
                // Create notification for the TEACHER (confirmation)
                $teacherNotificationMessage = "You successfully added {$studentName} to {$courseTitle}";
                $this->notificationModel->createNotification((int)$teacherId, $teacherNotificationMessage);
                
                $this->session->setFlashdata('success', 'Student added to course successfully!');
            } else {
                $this->session->setFlashdata('error', 'Failed to add student to course. Please try again.');
            }
        }

        return redirect()->to(base_url("teacher/course/{$courseId}/students"));
    }

    /**
     * Remove student from course
     */
    public function removeStudentFromCourse()
    {
        // Authorization check for teacher role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'teacher') {
            $this->session->setFlashdata('error', 'Access denied. Teacher privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $courseId = $this->request->getPost('course_id');
            $studentId = $this->request->getPost('student_id');
            $teacherId = $this->session->get('userID');

            // Verify this course belongs to the teacher
            $coursesBuilder = $this->db->table('courses');
            $courseCheck = $coursesBuilder->where('id', $courseId)
                ->where('instructor_id', $teacherId)
                ->get()
                ->getRowArray();
            
            if (!$courseCheck) {
                $this->session->setFlashdata('error', 'Access denied. You can only remove students from your own courses.');
                return redirect()->to(base_url('teacher/courses'));
            }

            // Remove enrollment
            if ($this->enrollmentModel->dropEnrollment($studentId, $courseId)) {
                // Get course and student details for notification
                $courseTitle = $courseCheck['title'] ?? 'a course';
                
                // Get student name
                $studentData = $this->db->table('users')
                    ->select('name')
                    ->where('id', $studentId)
                    ->get()
                    ->getRowArray();
                $studentName = $studentData['name'] ?? 'Student';
                
                // Create notification for the student
                $studentNotificationMessage = "You have been removed from the course: {$courseTitle}";
                $this->notificationModel->createNotification((int)$studentId, $studentNotificationMessage);
                
                // Create notification for the teacher
                $teacherNotificationMessage = "You removed {$studentName} from {$courseTitle}";
                $this->notificationModel->createNotification((int)$teacherId, $teacherNotificationMessage);
                
                $this->session->setFlashdata('success', 'Student removed from course successfully!');
            } else {
                $this->session->setFlashdata('error', 'Failed to remove student from course. Please try again.');
            }
        }

        return redirect()->to(base_url("teacher/course/{$courseId}/students"));
    }
    
    /**
     * Clean up duplicate courses for a teacher
     */
    public function cleanupDuplicates()
    {
        $teacherId = $this->session->get('userID');
        
        // Get all courses for this teacher
        $courses = $this->db->table('courses')
            ->where('instructor_id', $teacherId)
            ->orderBy('created_at', 'ASC')
            ->get()
            ->getResultArray();
        
        $seenTitles = [];
        $duplicatesToDelete = [];
        
        foreach ($courses as $course) {
            if (in_array($course['title'], $seenTitles)) {
                // This is a duplicate, mark for deletion
                $duplicatesToDelete[] = $course['id'];
            } else {
                // First time seeing this title
                $seenTitles[] = $course['title'];
            }
        }
        
        // Delete duplicates
        foreach ($duplicatesToDelete as $courseId) {
            $this->db->table('courses')->where('id', $courseId)->delete();
        }
        
        $this->session->setFlashdata('success', 'Duplicate courses cleaned up successfully!');
        return redirect()->to(base_url('teacher/dashboard'));
    }
}