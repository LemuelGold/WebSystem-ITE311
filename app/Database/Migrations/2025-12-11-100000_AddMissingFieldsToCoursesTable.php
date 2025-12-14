<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingFieldsToCoursesTable extends Migration
{
    public function up()
    {
        // Add missing fields to courses table
        $fields = [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
                'after' => 'instructor_id'
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'status'
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'start_date'
            ],
            'academic_year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'end_date'
            ]
        ];
        
        $this->forge->addColumn('courses', $fields);
    }

    public function down()
    {
        // Remove the added fields
        $this->forge->dropColumn('courses', ['status', 'start_date', 'end_date', 'academic_year']);
    }
}