<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * CourseModel - Handles course data operations
 */
class CourseModel extends Model
{
    protected $table      = 'courses';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'title',
        'description',
        'instructor_id',
        'status',
        'start_date',
        'end_date',
        'academic_year',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'title'         => 'required|min_length[3]|max_length[255]',
        'description'   => 'permit_empty|max_length[1000]',
        'instructor_id' => 'permit_empty|integer',
        'status'        => 'required|in_list[active,inactive]',
        'start_date'    => 'permit_empty|valid_date',
        'end_date'      => 'permit_empty|valid_date',
        'academic_year' => 'permit_empty|max_length[20]'
    ];

    protected $validationMessages = [
        'title' => [
            'required'   => 'Course title is required',
            'min_length' => 'Course title must be at least 3 characters',
            'max_length' => 'Course title cannot exceed 255 characters'
        ],
        'status' => [
            'required' => 'Course status is required',
            'in_list'  => 'Status must be either active or inactive'
        ],
        'instructor_id' => [
            'integer' => 'Instructor ID must be a valid integer'
        ]
    ];

    /**
     * Get all active courses with instructor information
     *
     * @return array
     */
    public function getActiveCourses()
    {
        return $this->select('courses.*, users.name as instructor_name, users.email as instructor_email')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->where('courses.status', 'active')
                    ->orderBy('courses.title', 'ASC')
                    ->findAll();
    }

    /**
     * Get all courses with instructor information
     *
     * @return array
     */
    public function getAllCoursesWithInstructor()
    {
        return $this->select('courses.*, users.name as instructor_name, users.email as instructor_email')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->orderBy('courses.title', 'ASC')
                    ->findAll();
    }

    /**
     * Get a single course with instructor information
     *
     * @param int $id Course ID
     * @return array|null
     */
    public function getCourseWithInstructor($id)
    {
        return $this->select('courses.*, users.name as instructor_name, users.email as instructor_email')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->find($id);
    }

    /**
     * Get courses by instructor ID
     *
     * @param int $instructorId Instructor user ID
     * @return array
     */
    public function getCoursesByInstructor($instructorId)
    {
        return $this->where('instructor_id', $instructorId)
                    ->orderBy('title', 'ASC')
                    ->findAll();
    }

    /**
     * Search courses by title or description
     *
     * @param string $searchTerm Search keyword
     * @return array
     */
    public function searchCourses($searchTerm)
    {
        return $this->select('courses.*, users.name as instructor_name')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->where('courses.status', 'active')
                    ->groupStart()
                        ->like('courses.title', $searchTerm)
                        ->orLike('courses.description', $searchTerm)
                    ->groupEnd()
                    ->orderBy('courses.title', 'ASC')
                    ->findAll();
    }

    /**
     * Get course count by status
     *
     * @param string $status Course status (active/inactive)
     * @return int
     */
    public function getCountByStatus($status = 'active')
    {
        return $this->where('status', $status)->countAllResults();
    }

    /**
     * Update course status
     *
     * @param int $id Course ID
     * @param string $status New status (active/inactive)
     * @return bool
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Get courses with enrollment count
     *
     * @return array
     */
    public function getCoursesWithEnrollmentCount()
    {
        return $this->select('courses.*, users.name as instructor_name, COUNT(enrollments.id) as student_count')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->join('enrollments', 'enrollments.course_id = courses.id', 'left')
                    ->groupBy('courses.id')
                    ->orderBy('courses.title', 'ASC')
                    ->findAll();
    }
}
