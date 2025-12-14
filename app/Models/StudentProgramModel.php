<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentProgramModel extends Model
{
    protected $table = 'student_programs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'student_id',
        'program_id',
        'student_number',
        'enrollment_date',
        'current_year_level',
        'current_semester',
        'academic_year',
        'status'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'student_id' => 'required|integer',
        'program_id' => 'required|integer',
        'student_number' => 'required|max_length[20]|is_unique[student_programs.student_number,id,{id}]',
        'enrollment_date' => 'required|valid_date',
        'current_year_level' => 'required|integer|greater_than[0]|less_than[5]',
        'academic_year' => 'required|max_length[20]',
        'status' => 'required|in_list[active,inactive,graduated,dropped]'
    ];

    protected $validationMessages = [
        'student_number' => [
            'required' => 'Student number is required',
            'is_unique' => 'Student number already exists'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get student's program enrollment
     */
    public function getStudentProgram(int $studentId): ?array
    {
        $builder = $this->db->table($this->table);
        
        // Get the latest program enrollment record for the student
        $latestRecord = $builder
            ->select('student_programs.*, programs.program_code, programs.program_name, programs.duration_years')
            ->join('programs', 'programs.id = student_programs.program_id', 'inner')
            ->where('student_programs.student_id', $studentId)
            ->orderBy('student_programs.id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
        
        // Only return if the latest record is active
        if ($latestRecord && $latestRecord['status'] === 'active') {
            return $latestRecord;
        }
        
        return null;
    }

    /**
     * Get all students in a program
     */
    public function getProgramStudents(int $programId): array
    {
        $builder = $this->db->table($this->table);
        
        return $builder
            ->select('student_programs.*, users.name, users.email')
            ->join('users', 'users.id = student_programs.student_id', 'inner')
            ->where('student_programs.program_id', $programId)
            ->where('student_programs.status', 'active')
            ->where('users.deleted_at IS NULL')
            ->orderBy('student_programs.current_year_level', 'ASC')
            ->orderBy('users.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Generate student number
     */
    public function generateStudentNumber(string $programCode, int $year): string
    {
        // Format: PROGRAMCODE-YEAR-XXXX (e.g., BSIT-2025-0001)
        $yearSuffix = substr($year, -2); // Get last 2 digits of year
        $prefix = strtoupper($programCode) . '-' . $year . '-';
        
        // Get the last student number for this program and year
        $lastStudent = $this->like('student_number', $prefix, 'after')
                           ->orderBy('student_number', 'DESC')
                           ->first();
        
        if ($lastStudent) {
            // Extract the sequence number and increment
            $lastNumber = substr($lastStudent['student_number'], -4);
            $nextNumber = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // First student for this program and year
            $nextNumber = '0001';
        }
        
        return $prefix . $nextNumber;
    }

    /**
     * Enroll student in program
     */
    public function enrollStudentInProgram(array $data): bool|int
    {
        // Check if student is already enrolled in a program
        $existingEnrollment = $this->where('student_id', $data['student_id'])
                                  ->where('status', 'active')
                                  ->first();
        
        if ($existingEnrollment) {
            return false; // Student already enrolled in a program
        }

        // Generate student number if not provided
        if (empty($data['student_number'])) {
            $program = model('ProgramModel')->find($data['program_id']);
            $data['student_number'] = $this->generateStudentNumber(
                $program['program_code'], 
                date('Y')
            );
        }

        return $this->insert($data);
    }
}