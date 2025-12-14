<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPeriodToMaterials extends Migration
{
    public function up()
    {
        $this->forge->addColumn('materials', [
            'period' => [
                'type'       => 'ENUM',
                'constraint' => ['Prelim', 'Midterm', 'Final'],
                'null'       => true,
                'after'      => 'file_path'
            ],
            'material_title' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'period'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('materials', ['period', 'material_title']);
    }
}