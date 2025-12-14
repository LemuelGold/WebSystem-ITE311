<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSemesterToCourses extends Migration
{
    public function up()
    {
        // Add semester field to courses table
        $this->forge->addColumn('courses', [
            'semester' => [
                'type' => 'ENUM',
                'constraint' => ['1st Semester', '2nd Semester'],
                'null' => true,
                'default' => null,
                'after' => 'academic_year'
            ]
        ]);
    }

    public function down()
    {
        // Remove semester field
        $this->forge->dropColumn('courses', 'semester');
    }
}