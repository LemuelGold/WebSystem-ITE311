<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * MaterialModel - Handles course materials data operations
 */
class MaterialModel extends Model
{
    protected $table      = 'materials';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'course_id', 'file_name', 'file_path', 'created_at'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'course_id' => 'required|integer',
        'file_name' => 'required|max_length[255]',
        'file_path' => 'required|max_length[255]'
    ];

    protected $validationMessages = [
        'course_id' => [
            'required' => 'Course ID is required',
            'integer'  => 'Course ID must be a valid integer'
        ],
        'file_name' => [
            'required' => 'File name is required'
        ],
        'file_path' => [
            'required' => 'File path is required'
        ]
    ];

    /**
     * Insert a new material record
     * @param array $data Material data (course_id, file_name, file_path)
     * @return bool|int Returns material ID on success, false on failure
     */
    public function insertMaterial(array $data): bool|int
    {
        // Add timestamp
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $result = $this->insert($data);
        
        if ($result) {
            log_message('info', "Material added: {$data['file_name']} for course {$data['course_id']}");
            return $this->getInsertID();
        }
        
        log_message('error', "Failed to add material: {$data['file_name']}");
        return false;
    }

    /**
     * Get all materials for a specific course
     * @param int $course_id The course ID
     * @return array Array of materials
     */
    public function getMaterialsByCourse(int $course_id): array
    {
        return $this->where('course_id', $course_id)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get material by ID with course information
     * @param int $material_id The material ID
     * @return array|null Material data with course info
     */
    public function getMaterialWithCourse(int $material_id): ?array
    {
        return $this->select('materials.*, courses.title as course_title, courses.instructor_id')
                    ->join('courses', 'courses.id = materials.course_id')
                    ->where('materials.id', $material_id)
                    ->first();
    }

    /**
     * Delete a material record
     * @param int $material_id The material ID
     * @return bool Success status
     */
    public function deleteMaterial(int $material_id): bool
    {
        return $this->delete($material_id);
    }

    /**
     * Get materials for courses a student is enrolled in
     * @param int $student_id The student's ID
     * @return array Array of materials with course information
     */
    public function getMaterialsForStudent(int $student_id): array
    {
        $builder = $this->db->table($this->table);
        
        return $builder->select('materials.*, courses.title as course_title, courses.id as course_id')
                      ->join('courses', 'courses.id = materials.course_id')
                      ->join('enrollments', 'enrollments.course_id = courses.id')
                      ->where('enrollments.student_id', $student_id)
                      ->orderBy('materials.created_at', 'DESC')
                      ->get()
                      ->getResultArray();
    }
}
