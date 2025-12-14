<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentProgramsTable extends Migration
{
    public function up()
    {
        // Create student_programs table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'student_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'program_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'student_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'enrollment_date' => [
                'type' => 'DATE',
            ],
            'current_year_level' => [
                'type'       => 'INT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'current_semester' => [
                'type'       => 'ENUM',
                'constraint' => ['1st Semester', '2nd Semester', 'Summer'],
                'default'    => '1st Semester',
            ],
            'academic_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive', 'graduated', 'dropped'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('student_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('program_id', 'programs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey('student_number');
        $this->forge->createTable('student_programs');
    }

    public function down()
    {
        $this->forge->dropTable('student_programs');
    }
}