<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixCourseTimestamps extends Migration
{
    public function up()
    {
        // Fix courses with invalid created_at timestamps
        $this->db->query("
            UPDATE courses 
            SET created_at = NOW(), updated_at = NOW() 
            WHERE created_at IS NULL 
               OR created_at = '0000-00-00 00:00:00' 
               OR created_at < '2020-01-01 00:00:00'
        ");
    }

    public function down()
    {
        // No rollback needed for this fix
    }
}