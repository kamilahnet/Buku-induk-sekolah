<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class WidenStudentYesNoFields extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('students', [
            'receives_kps'     => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
            'receives_kip'     => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
            'eligible_for_pip' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('students', [
            'receives_kps'     => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => true],
            'receives_kip'     => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => true],
            'eligible_for_pip' => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => true],
        ]);
    }
}
