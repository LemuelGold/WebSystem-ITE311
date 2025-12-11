<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * EnrollmentModel - Handles enrollment data operations
 */
class EnrollmentModel extends Model
{
    protected $table      = 'enrollments';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'student_id', 'course_id', 'enrollment_date'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'student_id'   => 'required|integer',
        'course_id' => 'required|integer'
    ];

    protected $validationMessages = [
        'student_id' => [
            'required' => 'Student ID is required',
            'integer'  => 'Student ID must be a valid integer'
        ],
        'course_id' => [
            'required' => 'Course ID is required',
            'integer'  => 'Course ID must be a valid integer'
        ]
    ];

    /**
     * Enroll a user in a course
     * @param array $data Enrollment data containing student_id and course_id
     * @return bool|int Returns enrollment ID on success, false on failure
     */
    public function enrollUser(array $data): bool|int
    {
        // Validate required fields
        if (!isset($data['student_id']) || !isset($data['course_id'])) {
            return false;
        }

        // Check if already enrolled to prevent duplicates
        if ($this->isAlreadyEnrolled($data['student_id'], $data['course_id'])) {
            return false;
        }

        // Prepare enrollment data
        $enrollmentData = [
            'student_id'      => $data['student_id'],
            'course_id'       => $data['course_id'],
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        // Insert enrollment record
        $result = $this->insert($enrollmentData);
        
        if ($result) {
            log_message('info', "User {$data['student_id']} enrolled in course {$data['course_id']}");
            return $this->getInsertID();
        }
        
        log_message('error', "Failed to enroll user {$data['student_id']} in course {$data['course_id']}");
        return false;
    }

    /**
     * Get all courses a user is enrolled in (approved enrollments only)
     * @param int $student_id The student's ID
     * @return array Array of enrolled courses with course details
     */
    public function getUserEnrollments(int $student_id): array
    {
        $builder = $this->db->table($this->table);
        
        $enrollments = $builder
            ->select('enrollments.*, courses.title, courses.description, courses.status as course_status, users.name as instructor_name')
            ->join('courses', 'courses.id = enrollments.course_id', 'inner')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('enrollments.student_id', $student_id)
            ->where('enrollments.status', 'enrolled')
            ->where('courses.status', 'active')
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->get()
            ->getResultArray();

        return $enrollments ?? [];
    }

    /**
     * Get pending enrollment requests for a student
     * @param int $student_id The student's ID
     * @return array Array of pending enrollments
     */
    public function getStudentPendingEnrollments(int $student_id): array
    {
        $builder = $this->db->table($this->table);
        
        $pending = $builder
            ->select('enrollments.*, courses.title, courses.description, users.name as instructor_name')
            ->join('courses', 'courses.id = enrollments.course_id', 'inner')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('enrollments.student_id', $student_id)
            ->where('enrollments.status', 'pending')
            ->where('courses.status', 'active')
            ->orderBy('enrollments.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return $pending ?? [];
    }

    /**
     * Check if a user is already enrolled in a specific course
     * @param int $student_id The student's ID  
     * @param int $course_id The course ID
     * @return bool True if already enrolled, false otherwise
     */
    public function isAlreadyEnrolled(int $student_id, int $course_id): bool
    {
        $enrollment = $this->where([
            'student_id' => $student_id,
            'course_id'  => $course_id
        ])->first();

        return $enrollment !== null;
    }

    /**
     * Get enrollment statistics for a user
     * @param int $student_id The student's ID
     * @return array Statistics array with counts
     */
    public function getUserEnrollmentStats(int $student_id): array
    {
        $total = $this->where('student_id', $student_id)->countAllResults();
        
        $completed = $this->where('student_id', $student_id)->countAllResults();

        $active = $this->where('student_id', $student_id)->countAllResults();

        return [
            'total_enrollments' => $total,
            'completed_courses' => $completed,
            'active_enrollments' => $active
        ];
    }

    /**
     * Update enrollment status
     * @param int $enrollment_id The enrollment ID
     * @param string $status New status (enrolled, completed, dropped)
     * @return bool Success status
     */
    public function updateEnrollmentStatus(int $enrollment_id, string $status): bool
    {
        if (!in_array($status, ['enrolled', 'completed', 'dropped'])) {
            return false;
        }

        return $this->update($enrollment_id, ['status' => $status]);
    }

    /**
     * Remove enrollment (drop from course)
     * @param int $student_id The student's ID
     * @param int $course_id The course ID
     * @return bool Success status
     */
    public function dropEnrollment(int $student_id, int $course_id): bool
    {
        return $this->where([
            'student_id' => $student_id,
            'course_id'  => $course_id
        ])->delete();
    }

    /**
     * Get pending enrollment requests for a course
     * @param int $course_id The course ID
     * @return array Array of pending enrollments with student details
     */
    public function getPendingEnrollments(int $course_id): array
    {
        $builder = $this->db->table($this->table);
        
        $pending = $builder
            ->select('enrollments.*, users.name as student_name, users.email as student_email')
            ->join('users', 'users.id = enrollments.student_id', 'inner')
            ->where('enrollments.course_id', $course_id)
            ->where('enrollments.status', 'pending')
            ->orderBy('enrollments.created_at', 'ASC')
            ->get()
            ->getResultArray();

        return $pending ?? [];
    }

    /**
     * Get all pending enrollment requests for a teacher's courses
     * @param int $teacher_id The teacher's ID
     * @return array Array of pending enrollments
     */
    public function getPendingEnrollmentsByTeacher(int $teacher_id): array
    {
        $builder = $this->db->table($this->table);
        
        $pending = $builder
            ->select('enrollments.*, users.name as student_name, users.email as student_email, courses.title as course_title, courses.id as course_id')
            ->join('users', 'users.id = enrollments.student_id', 'inner')
            ->join('courses', 'courses.id = enrollments.course_id', 'inner')
            ->where('courses.instructor_id', $teacher_id)
            ->where('enrollments.status', 'pending')
            ->orderBy('enrollments.created_at', 'ASC')
            ->get()
            ->getResultArray();

        return $pending ?? [];
    }

    /**
     * Approve a pending enrollment request
     * @param int $enrollment_id The enrollment ID
     * @return bool Success status
     */
    public function approveEnrollment(int $enrollment_id): bool
    {
        return $this->update($enrollment_id, [
            'status' => 'enrolled',
            'enrollment_date' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reject a pending enrollment request
     * @param int $enrollment_id The enrollment ID
     * @return bool Success status
     */
    public function rejectEnrollment(int $enrollment_id): bool
    {
        return $this->update($enrollment_id, ['status' => 'rejected']);
    }

    /**
     * Get enrollment by ID with course and student details
     * @param int $enrollment_id The enrollment ID
     * @return array|null Enrollment details or null
     */
    public function getEnrollmentDetails(int $enrollment_id): ?array
    {
        $builder = $this->db->table($this->table);
        
        $enrollment = $builder
            ->select('enrollments.*, users.name as student_name, users.email as student_email, users.id as student_id, courses.title as course_title, courses.instructor_id')
            ->join('users', 'users.id = enrollments.student_id', 'inner')
            ->join('courses', 'courses.id = enrollments.course_id', 'inner')
            ->where('enrollments.id', $enrollment_id)
            ->get()
            ->getRowArray();

        return $enrollment;
    }
}