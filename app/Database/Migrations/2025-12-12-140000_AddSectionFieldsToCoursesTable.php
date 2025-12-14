<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSectionFieldsToCoursesTable extends Migration
{
    public function up()
    {
        $fields = [
            'section' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'comment'    => 'Course section (e.g., A, B, 1, 2)'
            ],
            'schedule_time' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Class schedule time (e.g., MWF 9:00-10:00 AM)'
            ],
            'room' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Classroom or room assignment'
            ]
        ];

        $this->forge->addColumn('courses', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', ['section', 'schedule_time', 'room']);
    }
}