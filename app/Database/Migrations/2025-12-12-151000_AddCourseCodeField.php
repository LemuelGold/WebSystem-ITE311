<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseCodeField extends Migration
{
    public function up()
    {
        // Add course_code field to store the 4-digit course identifier
        $this->forge->addColumn('courses', [
            'course_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => false,
                'after'      => 'id',
                'comment'    => '4-digit course code (e.g., 1001, 2050)'
            ]
        ]);

        // Add index on course_code for better performance
        $this->forge->addKey('course_code');
    }

    public function down()
    {
        $this->forge->dropColumn('courses', 'course_code');
    }
}