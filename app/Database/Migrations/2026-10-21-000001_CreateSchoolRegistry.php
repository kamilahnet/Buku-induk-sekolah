<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchoolRegistry extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'username'   => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'admin'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');

        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'employee_number' => ['type' => 'VARCHAR', 'constraint' => 30, 'unique' => true],
            'full_name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'subject'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'phone'           => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'email'           => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'status'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('teachers');

        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'               => ['type' => 'VARCHAR', 'constraint' => 50],
            'grade_level'        => ['type' => 'TINYINT', 'constraint' => 2, 'unsigned' => true],
            'academic_year'      => ['type' => 'VARCHAR', 'constraint' => 9],
            'homeroom_teacher_id'=> ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['name', 'academic_year'], false, true);
        $this->forge->addForeignKey('homeroom_teacher_id', 'teachers', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('school_classes');

        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'student_number' => ['type' => 'VARCHAR', 'constraint' => 30, 'unique' => true],
            'full_name'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'gender'         => ['type' => 'CHAR', 'constraint' => 1],
            'birth_place'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'birth_date'     => ['type' => 'DATE', 'null' => true],
            'address'        => ['type' => 'TEXT', 'null' => true],
            'father_name'    => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'mother_name'    => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'class_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'admission_date' => ['type' => 'DATE', 'null' => true],
            'status'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('class_id', 'school_classes', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('students');
    }

    public function down()
    {
        $this->forge->dropTable('students', true);
        $this->forge->dropTable('school_classes', true);
        $this->forge->dropTable('teachers', true);
        $this->forge->dropTable('users', true);
    }
}
