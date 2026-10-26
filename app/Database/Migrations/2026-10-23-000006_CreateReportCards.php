<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReportCards extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('report_card_subjects')) {
            $this->forge->addField([
                'slot' => ['type' => 'TINYINT', 'constraint' => 2, 'unsigned' => true],
                'name' => ['type' => 'VARCHAR', 'constraint' => 60],
            ]);
            $this->forge->addPrimaryKey('slot');
            $this->forge->createTable('report_card_subjects');
        }

        if (! $this->db->tableExists('report_card_grades')) {
            $gradeFields = [
                'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'student_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'academic_year' => ['type' => 'VARCHAR', 'constraint' => 9],
                'semester'      => ['type' => 'VARCHAR', 'constraint' => 6],
            ];
            for ($slot = 1; $slot <= 11; $slot++) {
                $gradeFields['subject_' . $slot] = ['type' => 'DECIMAL', 'constraint' => '5,2'];
            }
            $gradeFields['created_at'] = ['type' => 'DATETIME', 'null' => true];
            $gradeFields['updated_at'] = ['type' => 'DATETIME', 'null' => true];

            $this->forge->addField($gradeFields);
            $this->forge->addPrimaryKey('id');
            $this->forge->addKey(['student_id', 'academic_year', 'semester'], false, true);
            $this->forge->addKey(['academic_year', 'semester']);
            $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('report_card_grades');
        }

        for ($slot = 1; $slot <= 11; $slot++) {
            if ($this->db->table('report_card_subjects')->where('slot', $slot)->countAllResults() === 0) {
                $this->db->table('report_card_subjects')->insert([
                    'slot' => $slot,
                    'name' => 'Mapel ' . $slot,
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('report_card_grades', true);
        $this->forge->dropTable('report_card_subjects', true);
    }
}
