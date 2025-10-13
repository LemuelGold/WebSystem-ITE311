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
     * Get all courses a user is enrolled in
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
            ->where('courses.status', 'active')
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->get()
            ->getResultArray();

        return $enrollments ?? [];
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
}