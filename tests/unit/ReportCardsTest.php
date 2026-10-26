<?php

use App\Controllers\ReportCards;
use Config\Database;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ReportCardsTest extends CIUnitTestCase
{
    private $testDb;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testDb = db_connect('tests');
        $forge       = Database::forge($this->testDb);
        $forge->dropTable('report_card_grades', true);
        $forge->dropTable('report_card_subjects', true);
        $forge->dropTable('students', true);

        $forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'student_number' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'full_name'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        ]);
        $forge->addPrimaryKey('id');
        $forge->createTable('students');

        require_once APPPATH . 'Database/Migrations/2026-10-23-000006_CreateReportCards.php';
        (new \App\Database\Migrations\CreateReportCards(Database::forge($this->testDb)))->up();
    }

    protected function tearDown(): void
    {
        $forge = Database::forge($this->testDb);
        $forge->dropTable('report_card_grades', true);
        $forge->dropTable('report_card_subjects', true);
        $forge->dropTable('students', true);

        parent::tearDown();
    }

    public function testParsesTemplateAndCustomSubjectHeaders(): void
    {
        $this->testDb->table('report_card_subjects')->where('slot', 1)->update(['name' => 'Bahasa Indonesia']);
        $headers = ['NIPD', 'Tahun Ajaran', 'Semester', 'Bahasa Indonesia'];
        for ($slot = 2; $slot <= 11; $slot++) {
            $headers[] = 'Mapel ' . $slot;
        }
        $values = ['00123', '2025/2026', 'Ganjil'];
        for ($slot = 1; $slot <= 11; $slot++) {
            $values[] = (string) ($slot * 10);
        }

        $data = $this->parseRows(implode("\t", $headers) . "\n" . implode("\t", $values) . "\n");

        $this->assertContains('subject_1', $data['headers']);
        $this->assertSame('00123', $data['rows'][0]['student_number']);
        $this->assertSame('10', $data['rows'][0]['subject_1']);
        $this->assertSame('110', $data['rows'][0]['subject_11']);
    }

    public function testValidatesScoresAndMapsStudentByNipd(): void
    {
        $this->testDb->table('students')->insert(['student_number' => '00123', 'full_name' => 'Siti Aminah']);
        $row = [
            'line'          => 2,
            'student_number' => '00123',
            'student_name'  => 'Siti Aminah',
            'academic_year' => '2025/2026',
            'semester'      => 'Semester Ganjil',
        ];
        for ($slot = 1; $slot <= 11; $slot++) {
            $row['subject_' . $slot] = $slot === 1 ? '89,5' : (string) (80 + $slot);
        }

        $result = $this->invoke('validateRows', [$row], array_keys($row));

        $this->assertSame([], $result['errors']);
        $this->assertCount(1, $result['grades']);
        $this->assertSame('Ganjil', $result['grades'][0]['semester']);
        $this->assertSame('89.5', $result['grades'][0]['subject_1']);
        $this->assertSame(1, $result['grades'][0]['student_id']);
    }

    public function testRejectsScoresOutsideTheAcceptedRange(): void
    {
        $this->testDb->table('students')->insert(['student_number' => '00123']);
        $row = [
            'line'          => 2,
            'student_number' => '00123',
            'academic_year' => '2025/2026',
            'semester'      => 'Ganjil',
        ];
        for ($slot = 1; $slot <= 11; $slot++) {
            $row['subject_' . $slot] = '80';
        }
        $row['subject_11'] = '100.01';

        $result = $this->invoke('validateRows', [$row], array_keys($row));

        $this->assertSame([], $result['grades']);
        $this->assertStringContainsString('nilai Mapel 11', $result['errors'][0]);
    }

    public function testMigrationCanRepairMissingGradeTableAndPreserveSubjectNames(): void
    {
        $this->testDb->table('report_card_subjects')->where('slot', 1)->update(['name' => 'Bahasa Indonesia']);
        Database::forge($this->testDb)->dropTable('report_card_grades', true);

        (new \App\Database\Migrations\CreateReportCards(Database::forge($this->testDb)))->up();

        $this->assertTrue($this->testDb->tableExists('report_card_grades'));
        $this->assertSame(
            'Bahasa Indonesia',
            $this->testDb->table('report_card_subjects')->where('slot', 1)->get()->getRowArray()['name'],
        );
        $this->assertSame(11, $this->testDb->table('report_card_subjects')->countAllResults());
    }

    public function testImportTemplateRecognizesNameAsFallbackIdentifier(): void
    {
        $headers = ['Nama Siswa', 'Tahun Ajaran', 'Semester'];
        for ($slot = 1; $slot <= 11; $slot++) {
            $headers[] = 'Mapel ' . $slot;
        }
        $values = ['Siti Aminah', '2025/2026', 'Ganjil'];
        for ($slot = 1; $slot <= 11; $slot++) {
            $values[] = '80';
        }

        $parsed = $this->parseRows(implode("\t", $headers) . "\n" . implode("\t", $values) . "\n");

        $this->assertContains('student_name', $parsed['headers']);
        $this->assertSame('Siti Aminah', $parsed['rows'][0]['student_name']);
    }

    public function testValidatesStudentUsingNameWhenNipdIsBlank(): void
    {
        $this->testDb->table('students')->insert(['student_number' => null, 'full_name' => 'Siti Aminah']);
        $row = $this->validGradeRow(['student_number' => '', 'student_name' => 'Siti Aminah']);

        $result = $this->invoke('validateRows', [$row], array_keys($row));

        $this->assertSame([], $result['errors']);
        $this->assertSame(1, $result['grades'][0]['student_id']);
    }

    public function testFallsBackToNameWhenNipdDoesNotMatch(): void
    {
        $this->testDb->table('students')->insert(['student_number' => '00123', 'full_name' => 'Siti Aminah']);
        $row = $this->validGradeRow(['student_number' => 'OLD-NIPD', 'student_name' => 'Siti Aminah']);

        $result = $this->invoke('validateRows', [$row], array_keys($row));

        $this->assertSame([], $result['errors']);
        $this->assertSame(1, $result['grades'][0]['student_id']);
    }

    public function testRejectsAmbiguousNameFallbackAndRequestsNipd(): void
    {
        $this->testDb->table('students')->insertBatch([
            ['student_number' => '00123', 'full_name' => 'Siti Aminah'],
            ['student_number' => '00456', 'full_name' => 'Siti Aminah'],
        ]);
        $row = $this->validGradeRow(['student_number' => '', 'student_name' => 'Siti Aminah']);

        $result = $this->invoke('validateRows', [$row], array_keys($row));

        $this->assertSame([], $result['grades']);
        $this->assertStringContainsString('isi NIPD', $result['errors'][0]);
    }

    private function validGradeRow(array $identity): array
    {
        $row = array_merge([
            'line'          => 2,
            'student_number' => '',
            'student_name'  => '',
            'academic_year' => '2025/2026',
            'semester'      => 'Ganjil',
        ], $identity);
        for ($slot = 1; $slot <= 11; $slot++) {
            $row['subject_' . $slot] = '80';
        }

        return $row;
    }

    private function parseRows(string $contents): array
    {
        return $this->invoke('parseRows', $contents);
    }

    private function invoke(string $methodName, mixed ...$arguments): mixed
    {
        $method = new ReflectionMethod(new ReportCards(), $methodName);

        return $method->invoke(new ReportCards(), ...$arguments);
    }
}
