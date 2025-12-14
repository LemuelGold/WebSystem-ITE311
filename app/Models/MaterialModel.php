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
        'course_id', 'uploaded_by', 'file_name', 'file_path', 'period', 'material_title', 'status', 'approved_by', 'approved_at', 'created_at'
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
        
        // Check if new columns exist before trying to insert them
        $columns = $this->db->getFieldNames($this->table);
        if (!in_array('period', $columns)) {
            unset($data['period']);
        }
        if (!in_array('material_title', $columns)) {
            unset($data['material_title']);
        }
        
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
     * @param string|null $status Filter by status (null = all, 'approved', 'pending', 'rejected')
     * @return array Array of materials
     */
    public function getMaterialsByCourse(int $course_id, ?string $status = null): array
    {
        $builder = $this->where('course_id', $course_id);
        
        if ($status !== null) {
            $builder = $builder->where('status', $status);
        }
        
        $materials = $builder->orderBy('created_at', 'DESC')->findAll();
        
        // Add default values for missing columns
        $columns = $this->db->getFieldNames($this->table);
        foreach ($materials as &$material) {
            if (!in_array('period', $columns)) {
                $material['period'] = null;
            }
            if (!in_array('material_title', $columns)) {
                $material['material_title'] = null;
            }
        }
        
        return $materials;
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
        
        // Check if new columns exist and select accordingly
        $columns = $this->db->getFieldNames($this->table);
        $selectFields = 'materials.*, courses.title as course_title, courses.id as course_id';
        
        // Add new fields if they exist
        if (in_array('period', $columns)) {
            $selectFields .= ', materials.period';
        }
        if (in_array('material_title', $columns)) {
            $selectFields .= ', materials.material_title';
        }
        
        return $builder->select($selectFields)
                      ->join('courses', 'courses.id = materials.course_id')
                      ->join('enrollments', 'enrollments.course_id = courses.id')
                      ->where('enrollments.user_id', $student_id)
                      ->where('enrollments.status', 'confirmed')  // Only confirmed enrollments
                      ->where('materials.status', 'approved')  // Only show approved materials to students
                      ->orderBy('materials.created_at', 'DESC')
                      ->get()
                      ->getResultArray();
    }

    /**
     * Get pending materials for admin approval
     * @return array Array of pending materials with course and uploader information
     */
    public function getPendingMaterials(): array
    {
        $builder = $this->db->table($this->table);
        
        return $builder->select('materials.*, courses.title as course_title, users.name as uploaded_by_name')
                      ->join('courses', 'courses.id = materials.course_id')
                      ->join('users', 'users.id = materials.uploaded_by', 'left')
                      ->where('materials.status', 'pending')
                      ->orderBy('materials.created_at', 'DESC')
                      ->get()
                      ->getResultArray();
    }

    /**
     * Approve a material
     * @param int $material_id Material ID
     * @param int $admin_id Admin user ID
     * @return bool Success status
     */
    public function approveMaterial(int $material_id, int $admin_id): bool
    {
        $data = [
            'status' => 'approved',
            'approved_by' => $admin_id,
            'approved_at' => date('Y-m-d H:i:s')
        ];

        return $this->update($material_id, $data);
    }

    /**
     * Reject a material
     * @param int $material_id Material ID
     * @param int $admin_id Admin user ID
     * @return bool Success status
     */
    public function rejectMaterial(int $material_id, int $admin_id): bool
    {
        $data = [
            'status' => 'rejected',
            'approved_by' => $admin_id,
            'approved_at' => date('Y-m-d H:i:s')
        ];

        return $this->update($material_id, $data);
    }
}
