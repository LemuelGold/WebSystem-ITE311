<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PopulateCourseCodesFromExistingData extends Migration
{
    public function up()
    {
        // Update existing courses to have course codes based on their current ID
        $this->db->query("UPDATE courses SET course_code = id WHERE course_code IS NULL OR course_code = ''");
    }

    public function down()
    {
        // Clear course codes
        $this->db->query("UPDATE courses SET course_code = NULL");
    }
}