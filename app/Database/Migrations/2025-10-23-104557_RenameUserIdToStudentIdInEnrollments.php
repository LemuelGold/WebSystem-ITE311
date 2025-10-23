<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameUserIdToStudentIdInEnrollments extends Migration
{
    public function up()
    {
        // Drop the old foreign key first
        $this->forge->dropForeignKey('enrollments', 'enrollments_user_id_foreign');
        
        // Rename the column
        $fields = [
            'user_id' => [
                'name' => 'student_id',
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ];
        $this->forge->modifyColumn('enrollments', $fields);
        
        // Add the foreign key back with the new column name
        $this->forge->addForeignKey('student_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->db->query('ALTER TABLE enrollments ADD CONSTRAINT enrollments_student_id_foreign FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // Reverse the changes
        $this->forge->dropForeignKey('enrollments', 'enrollments_student_id_foreign');
        
        $fields = [
            'student_id' => [
                'name' => 'user_id',
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ];
        $this->forge->modifyColumn('enrollments', $fields);
        
        $this->db->query('ALTER TABLE enrollments ADD CONSTRAINT enrollments_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }
}
