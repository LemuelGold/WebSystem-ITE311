<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table = 'programs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'program_code',
        'program_name', 
        'description',
        'duration_years',
        'total_units',
        'status'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'program_code' => 'required|max_length[20]',
        'program_name' => 'required|max_length[255]',
        'duration_years' => 'permit_empty|integer|greater_than[0]|less_than[10]',
        'total_units' => 'permit_empty|integer|greater_than[0]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'program_code' => [
            'required' => 'Program code is required',
            'is_unique' => 'Program code already exists'
        ],
        'program_name' => [
            'required' => 'Program name is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get all active programs
     */
    public function getActivePrograms(): array
    {
        return $this->where('status', 'active')
                   ->orderBy('program_name', 'ASC')
                   ->findAll();
    }

    /**
     * Get program with course count
     */
    public function getProgramsWithCourseCount(): array
    {
        $builder = $this->db->table($this->table);
        
        return $builder
            ->select('programs.*, COALESCE(course_counts.course_count, 0) as course_count')
            ->join('(SELECT program_id, COUNT(*) as course_count FROM program_courses GROUP BY program_id) as course_counts', 
                   'course_counts.program_id = programs.id', 'left')
            ->orderBy('programs.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get program courses by year and semester
     */
    public function getProgramCourses(int $programId): array
    {
        $builder = $this->db->table('program_courses');
        
        return $builder
            ->select('program_courses.*, courses.title, courses.course_code, courses.units, courses.description')
            ->join('courses', 'courses.id = program_courses.course_id', 'inner')
            ->where('program_courses.program_id', $programId)
            ->where('courses.status', 'active')
            ->orderBy('program_courses.year_level', 'ASC')
            ->orderBy('program_courses.semester', 'ASC')
            ->orderBy('courses.title', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get program curriculum organized by year and semester
     */
    public function getProgramCurriculum(int $programId): array
    {
        $courses = $this->getProgramCourses($programId);
        $curriculum = [];

        foreach ($courses as $course) {
            $year = $course['year_level'];
            $semester = $course['semester'];
            
            if (!isset($curriculum[$year])) {
                $curriculum[$year] = [];
            }
            
            if (!isset($curriculum[$year][$semester])) {
                $curriculum[$year][$semester] = [];
            }
            
            $curriculum[$year][$semester][] = $course;
        }

        return $curriculum;
    }
}