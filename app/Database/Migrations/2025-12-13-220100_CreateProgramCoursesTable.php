<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramCoursesTable extends Migration
{
    public function up()
    {
        // Create program_courses table (many-to-many relationship)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'program_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'course_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'year_level' => [
                'type'       => 'INT',
                'constraint' => 1,
                'comment'    => '1=1st Year, 2=2nd Year, 3=3rd Year, 4=4th Year',
            ],
            'semester' => [
                'type'       => 'ENUM',
                'constraint' => ['1st Semester', '2nd Semester', 'Summer'],
                'default'    => '1st Semester',
            ],
            'is_required' => [
                'type'    => 'BOOLEAN',
                'default' => true,
            ],
            'prerequisite_course_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addForeignKey('program_id', 'programs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('prerequisite_course_id', 'courses', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addUniqueKey(['program_id', 'course_id']);
        $this->forge->createTable('program_courses');
    }

    public function down()
    {
        $this->forge->dropTable('program_courses');
    }
}