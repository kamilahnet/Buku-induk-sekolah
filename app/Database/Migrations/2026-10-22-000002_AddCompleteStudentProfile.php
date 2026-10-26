<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompleteStudentProfile extends Migration
{
    private array $fields = [
        'nisn'                    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        'nik'                     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        'religion'                => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
        'rt'                      => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
        'rw'                      => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
        'hamlet'                  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'village'                 => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'district'                => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'postal_code'             => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
        'residence_type'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'transportation'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'telephone'               => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
        'email'                   => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        'skhun_number'            => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'receives_kps'            => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => true],
        'kps_number'              => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'father_birth_year'       => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => true],
        'father_education'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'father_occupation'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'father_income'           => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'father_nik'              => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        'mother_birth_year'       => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => true],
        'mother_education'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'mother_occupation'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'mother_income'           => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'mother_nik'              => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        'guardian_name'           => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        'guardian_birth_year'     => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => true],
        'guardian_education'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'guardian_occupation'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'guardian_income'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'guardian_nik'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        'national_exam_number'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'diploma_serial_number'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'receives_kip'            => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => true],
        'kip_number'              => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'kip_name'                => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        'kks_number'              => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'birth_certificate_number'=> ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'bank_name'               => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'bank_account_number'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'bank_account_holder'     => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        'eligible_for_pip'        => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => true],
        'pip_reason'              => ['type' => 'TEXT', 'null' => true],
        'special_needs'           => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        'previous_school'         => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        'child_order'             => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true, 'null' => true],
        'latitude'                => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
        'longitude'               => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
        'family_card_number'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        'weight_kg'               => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
        'height_cm'               => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
        'head_circumference_cm'   => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
        'sibling_count'           => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true, 'null' => true],
        'distance_to_school_km'   => ['type' => 'DECIMAL', 'constraint' => '6,2', 'null' => true],
    ];

    public function up()
    {
        $this->forge->addColumn('students', $this->fields);
    }

    public function down()
    {
        $this->forge->dropColumn('students', array_keys($this->fields));
    }
}
