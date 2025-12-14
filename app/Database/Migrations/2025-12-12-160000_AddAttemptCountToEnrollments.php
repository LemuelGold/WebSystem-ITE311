<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAttemptCountToEnrollments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('enrollments', [
            'attempt_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
                'null'       => false,
                'comment'    => 'Number of enrollment attempts for this course'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', 'attempt_count');
    }
}