<?php

use App\Controllers\Registry;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;

/**
 * @internal
 */
final class RegistryTest extends CIUnitTestCase
{
    public function testMainDataWarningUsesOnlyFieldsInTheMainDataGroup(): void
    {
        $controller = new Registry();
        $config = [
            'fields' => [
                'full_name' => ['label' => 'Nama', 'group' => 'Data Utama'],
                'student_number' => ['label' => 'NIPD', 'group' => 'Data Utama'],
                'address' => ['label' => 'Alamat', 'group' => 'Alamat dan Kontak'],
            ],
        ];

        $fields = $this->invoke($controller, 'mainDataFields', $config);
        $missing = $this->invoke($controller, 'missingMainDataFields', [
            'full_name' => 'Siti Aminah',
            'student_number' => '   ',
            'address' => '',
        ], $fields);

        $this->assertSame(['full_name' => 'Nama', 'student_number' => 'NIPD'], $fields);
        $this->assertSame(['NIPD'], $missing);
    }

    public function testMainDataWarningIsEmptyWhenAllMainFieldsArePresent(): void
    {
        $controller = new Registry();
        $fields = [
            'full_name' => 'Nama',
            'student_number' => 'NIPD',
        ];

        $missing = $this->invoke($controller, 'missingMainDataFields', [
            'full_name' => 'Siti Aminah',
            'student_number' => '00123',
        ], $fields);

        $this->assertSame([], $missing);
    }

    public function testIncompleteStudentCountHandlesNullEmptyAndWhitespaceValues(): void
    {
        $db = db_connect('tests');
        $forge = Database::forge($db);
        $forge->dropTable('students', true);
        $forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'full_name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        ]);
        $forge->addPrimaryKey('id');
        $forge->createTable('students');
        $db->table('students')->insertBatch([
            ['full_name' => null],
            ['full_name' => ''],
            ['full_name' => '   '],
            ['full_name' => 'Siti Aminah'],
        ]);

        $controller = new Registry();
        $missingCount = $this->invoke($controller, 'countIncompleteStudents', $db, ['full_name' => 'Nama']);

        $this->assertSame(3, $missingCount);
        $forge->dropTable('students', true);
    }

    private function invoke(Registry $controller, string $methodName, mixed ...$arguments): mixed
    {
        return (new ReflectionMethod($controller, $methodName))->invoke($controller, ...$arguments);
    }
}
