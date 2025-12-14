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
        'user_id', 'course_id', 'enrollment_date', 'status', 'attempt_count'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'user_id'   => 'required|integer',
        'course_id' => 'required|integer'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'integer'  => 'User ID must be a valid integer'
        ],
        'course_id' => [
            'required' => 'Course ID is required',
            'integer'  => 'Course ID must be a valid integer'
        ]
    ];

    /**
     * Enroll a user in a course
     * @param array $data Enrollment data containing user_id and course_id
     * @return bool|int Returns enrollment ID on success, false on failure
     */
    public function enrollUser(array $data): bool|int
    {
        // Validate required fields
        if (!isset($data['user_id']) || !isset($data['course_id'])) {
            return false;
        }

        // Check if already enrolled to prevent duplicates
        if ($this->isAlreadyEnrolled($data['user_id'], $data['course_id'])) {
            return false;
        }

        // Check if user has exceeded maximum attempts
        if ($this->hasExceededAttempts($data['user_id'], $data['course_id'])) {
            return false;
        }

        // Check if there's a previous enrollment (rejected/declined) that can be updated
        $previousEnrollment = $this->where([
            'user_id' => $data['user_id'],
            'course_id' => $data['course_id']
        ])->first();

        if ($previousEnrollment) {
            // Update existing enrollment to pending (re-invitation)
            $updateData = [
                'status' => 'pending',
                'enrollment_date' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $this->update($previousEnrollment['id'], $updateData);
            
            if ($result) {
                log_message('info', "User {$data['user_id']} re-enrolled in course {$data['course_id']} (updated existing enrollment)");
                return $previousEnrollment['id'];
            }
        } else {
            // Prepare enrollment data for new enrollment
            $enrollmentData = [
                'user_id'         => $data['user_id'],
                'course_id'       => $data['course_id'],
                'enrollment_date' => date('Y-m-d H:i:s'),
                'status'          => 'pending',
                'attempt_count'   => 1
            ];

            // Insert enrollment record
            $result = $this->insert($enrollmentData);
            
            if ($result) {
                log_message('info', "User {$data['user_id']} enrolled in course {$data['course_id']}");
                return $this->getInsertID();
            }
        }
        
        log_message('error', "Failed to enroll user {$data['user_id']} in course {$data['course_id']}");
        return false;
    }

    /**
     * Get all courses a user is enrolled in (approved enrollments only)
     * @param int $user_id The user's ID
     * @return array Array of enrolled courses with course details
     */
    public function getUserEnrollments(int $user_id): array
    {
        $builder = $this->db->table($this->table);
        
        $enrollments = $builder
            ->select('enrollments.*, courses.title, courses.description, courses.status as course_status, users.name as instructor_name')
            ->join('courses', 'courses.id = enrollments.course_id', 'inner')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('enrollments.user_id', $user_id)
            ->where('enrollments.status', 'confirmed')
            ->where('courses.status', 'active')
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->get()
            ->getResultArray();

        return $enrollments ?? [];
    }

    /**
     * Get pending enrollment requests for a student
     * @param int $user_id The user's ID
     * @return array Array of pending enrollments
     */
    public function getStudentPendingEnrollments(int $user_id): array
    {
        $builder = $this->db->table($this->table);
        
        $pending = $builder
            ->select('enrollments.*, courses.title, courses.description, users.name as instructor_name')
            ->join('courses', 'courses.id = enrollments.course_id', 'inner')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('enrollments.user_id', $user_id)
            ->where('enrollments.status', 'pending')
            ->where('courses.status', 'active')
            ->orderBy('enrollments.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return $pending ?? [];
    }

    /**
     * Get approved enrollments waiting for student confirmation
     * @param int $user_id The user's ID
     * @return array Array of approved enrollments waiting for confirmation
     */
    public function getStudentApprovedEnrollments(int $user_id): array
    {
        $builder = $this->db->table($this->table);
        
        $approved = $builder
            ->select('enrollments.*, courses.title, courses.description, courses.units, courses.term, courses.academic_year, courses.semester, users.name as instructor_name')
            ->join('courses', 'courses.id = enrollments.course_id', 'inner')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('enrollments.user_id', $user_id)
            ->whereIn('enrollments.status', ['approved', 'pending'])
            ->where('courses.status', 'active')
            ->orderBy('enrollments.updated_at', 'DESC')
            ->get()
            ->getResultArray();

        return $approved ?? [];
    }

    /**
     * Check if a user is already enrolled in a specific course
     * @param int $user_id The user's ID  
     * @param int $course_id The course ID
     * @return bool True if already enrolled, false otherwise
     */
    public function isAlreadyEnrolled(int $user_id, int $course_id): bool
    {
        $enrollment = $this->where([
            'user_id' => $user_id,
            'course_id'  => $course_id
        ])->whereIn('status', ['pending', 'approved', 'confirmed'])->first();

        return $enrollment !== null;
    }

    /**
     * Get enrollment statistics for a user
     * @param int $user_id The user's ID
     * @return array Statistics array with counts
     */
    public function getUserEnrollmentStats(int $user_id): array
    {
        $total = $this->where('user_id', $user_id)->countAllResults();
        
        $completed = $this->where('user_id', $user_id)->countAllResults();

        $active = $this->where('user_id', $user_id)->countAllResults();

        return [
            'total_enrollments' => $total,
            'completed_courses' => $completed,
            'active_enrollments' => $active
        ];
    }

    /**
     * Update enrollment status
     * @param int $enrollment_id The enrollment ID
     * @param string $status New status (pending, approved, rejected)
     * @return bool Success status
     */
    public function updateEnrollmentStatus(int $enrollment_id, string $status): bool
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return false;
        }

        return $this->update($enrollment_id, ['status' => $status]);
    }

    /**
     * Remove enrollment (drop from course)
     * @param int $user_id The user's ID
     * @param int $course_id The course ID
     * @return bool Success status
     */
    public function dropEnrollment(int $user_id, int $course_id): bool
    {
        return $this->where([
            'user_id' => $user_id,
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
            ->join('users', 'users.id = enrollments.user_id', 'inner')
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
            ->join('users', 'users.id = enrollments.user_id', 'inner')
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
            'status' => 'approved',
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
            ->select('enrollments.*, users.name as student_name, users.email as student_email, users.id as user_id, courses.title as course_title, courses.instructor_id')
            ->join('users', 'users.id = enrollments.user_id', 'inner')
            ->join('courses', 'courses.id = enrollments.course_id', 'inner')
            ->where('enrollments.id', $enrollment_id)
            ->get()
            ->getRowArray();

        return $enrollment;
    }

    /**
     * Get enrollment status for a specific user and course
     * @param int $user_id The user's ID
     * @param int $course_id The course ID
     * @return array|null Enrollment record with status or null if not enrolled
     */
    public function getEnrollmentStatus(int $user_id, int $course_id): ?array
    {
        // First check for active enrollments (approved or pending)
        $activeEnrollment = $this->where([
            'user_id' => $user_id,
            'course_id' => $course_id
        ])->whereIn('status', ['approved', 'pending'])
        ->orderBy('created_at', 'DESC')->first();

        if ($activeEnrollment) {
            return $activeEnrollment;
        }

        // If no active enrollment, get the most recent one (likely rejected)
        $enrollment = $this->where([
            'user_id' => $user_id,
            'course_id' => $course_id
        ])->orderBy('created_at', 'DESC')->first();

        return $enrollment;
    }

    /**
     * Get total enrollment attempts for a user and course (including rejected ones)
     * @param int $user_id The user's ID
     * @param int $course_id The course ID
     * @return int Total number of attempts
     */
    public function getTotalAttempts(int $user_id, int $course_id): int
    {
        $builder = $this->db->table($this->table);
        
        // Get the highest attempt count for this user and course
        $result = $builder
            ->selectMax('attempt_count')
            ->where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->get()
            ->getRow();

        return (int)($result->attempt_count ?? 0);
    }

    /**
     * Check if user has exceeded maximum enrollment attempts for a course
     * @param int $user_id The user's ID
     * @param int $course_id The course ID
     * @param int $max_attempts Maximum allowed attempts (default: 3)
     * @return bool True if attempts exceeded, false otherwise
     */
    public function hasExceededAttempts(int $user_id, int $course_id, int $max_attempts = 3): bool
    {
        $totalAttempts = $this->getTotalAttempts($user_id, $course_id);
        return $totalAttempts >= $max_attempts;
    }

    /**
     * Increment attempt count when enrollment is rejected
     * @param int $enrollment_id The enrollment ID
     * @return bool Success status
     */
    public function incrementAttemptCount(int $enrollment_id): bool
    {
        $enrollment = $this->find($enrollment_id);
        if (!$enrollment) {
            return false;
        }

        $newAttemptCount = ($enrollment['attempt_count'] ?? 0) + 1;
        
        return $this->update($enrollment_id, [
            'attempt_count' => $newAttemptCount,
            'status' => 'rejected'
        ]);
    }
}