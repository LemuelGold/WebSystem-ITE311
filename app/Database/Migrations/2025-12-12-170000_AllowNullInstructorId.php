<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AllowNullInstructorId extends Migration
{
    public function up()
    {
        // Modify instructor_id column to allow NULL values
        $this->forge->modifyColumn('courses', [
            'instructor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => null
            ]
        ]);
        
        // Update any courses with instructor_id = 0 to NULL
        $this->db->query("UPDATE courses SET instructor_id = NULL WHERE instructor_id = 0");
        
        // Update any courses with invalid instructor_id (not in users table) to NULL
        $this->db->query("UPDATE courses SET instructor_id = NULL WHERE instructor_id NOT IN (SELECT id FROM users WHERE role = 'teacher' AND deleted_at IS NULL)");
    }

    public function down()
    {
        // Revert back to NOT NULL (but this might fail if there are NULL values)
        $this->forge->modifyColumn('courses', [
            'instructor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ]
        ]);
    }
}