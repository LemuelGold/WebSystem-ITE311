<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUnitAndTermToCourses extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'units' => [
                'type'       => 'INT',
                'constraint' => 2,
                'null'       => true,
                'after'      => 'description'
            ],
            'term' => [
                'type'       => 'ENUM',
                'constraint' => ['Term 1', 'Term 2'],
                'null'       => true,
                'after'      => 'units'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', ['units', 'term']);
    }
}