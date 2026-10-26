<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdjustStudentImportFields extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('students', [
            'student_number'  => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'eligible_for_pip'=> ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        ]);
    }

    public function down()
    {
        throw new \RuntimeException('This migration cannot be reversed safely because imported records may have no NIPD or longer PIP descriptions.');
    }
}
