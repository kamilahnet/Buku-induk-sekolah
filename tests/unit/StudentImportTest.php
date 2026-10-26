<?php

use App\Controllers\StudentImport;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;

/**
 * @internal
 */
final class StudentImportTest extends CIUnitTestCase
{
    public function testParsesTabSeparatedExcelData(): void
    {
        $data = $this->parseRows(
            "nis\tnama_lengkap\tjenis_kelamin\tkelas\ttahun_ajaran\n"
            . "00123\tSiti Aminah\tP\t7A\t2026/2027\n",
        );

        $this->assertSame(['student_number', 'full_name', 'gender', 'class_name', 'academic_year'], $data['headers']);
        $this->assertSame('00123', $data['rows'][0]['student_number']);
        $this->assertSame('Siti Aminah', $data['rows'][0]['full_name']);
        $this->assertSame('2026/2027', $data['rows'][0]['academic_year']);
    }

    public function testParsesCsvWithQuotedCommaInAddress(): void
    {
        $data = $this->parseRows(
            "nis,nama_lengkap,jenis_kelamin,alamat\n"
            . "1001,Budi Santoso,L,\"Jalan Melati, No. 5\"\n",
        );

        $this->assertSame('Jalan Melati, No. 5', $data['rows'][0]['address']);
    }

    public function testParsesStudentProfileTemplateLabels(): void
    {
        $data = $this->parseRows(
            "Nama\tNIPD\tJK\tNISN\tNama Ayah\tNama Ibu\tNama Wali\tRombel Saat Ini\tTahun Ajaran\tPenerima KIP\n"
            . "Siti Aminah\t00123\tP\t0098765432\tAhmad\tSari\tBudi\t7A\t2026/2027\tYa\n",
        );

        $this->assertSame('00123', $data['rows'][0]['student_number']);
        $this->assertSame('Ahmad', $data['rows'][0]['father_name']);
        $this->assertSame('Sari', $data['rows'][0]['mother_name']);
        $this->assertSame('Budi', $data['rows'][0]['guardian_name']);
        $this->assertSame('7A', $data['rows'][0]['class_name']);
        $this->assertSame('Ya', $data['rows'][0]['receives_kip']);
    }

    public function testParsesExpandedProfileHeadersFromCsv(): void
    {
        $data = $this->parseRows(
            "Nama,NIPD,JK,NIK Ayah,NIK Ibu,NIK Wali,Rombel Saat Ini,Tahun Ajaran,Jarak Rumah ke Sekolah (KM)\n"
            . "Siti Aminah,00123,P,001,002,003,7A,2026/2027,1.5\n",
        );

        $this->assertSame('001', $data['rows'][0]['father_nik']);
        $this->assertSame('002', $data['rows'][0]['mother_nik']);
        $this->assertSame('003', $data['rows'][0]['guardian_nik']);
        $this->assertSame('1.5', $data['rows'][0]['distance_to_school_km']);
    }

    public function testParsesEveryColumnInTheDownloadableTemplate(): void
    {
        $columns = (new ReflectionClass(StudentImport::class))->getConstant('TEMPLATE_COLUMNS');
        $values  = array_fill(0, count($columns), '');
        $values[array_search('Nama', array_keys($columns), true)] = 'Siti Aminah';
        $values[array_search('NIPD', array_keys($columns), true)] = '00123';
        $values[array_search('JK', array_keys($columns), true)] = 'P';
        $values[array_search('NIK Ayah', array_keys($columns), true)] = '001';

        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, array_keys($columns), ',', '"', '\\', "\r\n");
        fputcsv($stream, $values, ',', '"', '\\', "\r\n");
        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        $data = $this->parseRows($contents);

        $this->assertSame('Siti Aminah', $data['rows'][0]['full_name']);
        $this->assertSame('00123', $data['rows'][0]['student_number']);
        $this->assertSame('001', $data['rows'][0]['father_nik']);
        $this->assertCount(count($columns), $data['headers']);
    }

    public function testBackupDownloadsAllStudentFieldsAndClassAcademicYear(): void
    {
        $db      = db_connect('tests');
        $forge   = Database::forge($db);
        $columns = (new ReflectionClass(StudentImport::class))->getConstant('TEMPLATE_COLUMNS');

        $forge->dropTable('students', true);
        $forge->dropTable('school_classes', true);
        $forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 50],
            'academic_year' => ['type' => 'VARCHAR', 'constraint' => 9],
        ]);
        $forge->addPrimaryKey('id');
        $forge->createTable('school_classes');

        $studentFields = [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'class_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
        ];
        foreach (array_unique(array_values($columns)) as $field) {
            if (! in_array($field, ['class_name', 'academic_year'], true)) {
                $studentFields[$field] = ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true];
            }
        }
        $forge->addField($studentFields);
        $forge->addPrimaryKey('id');
        $forge->createTable('students');

        $db->table('school_classes')->insert(['name' => '7A', 'academic_year' => '2026/2027']);
        $classId = $db->insertID();
        $db->table('students')->insert([
            'student_number' => '00123',
            'full_name' => 'Siti Aminah',
            'gender' => 'P',
            'class_id' => $classId,
            'father_name' => 'Ahmad',
            'status' => 'active',
        ]);

        $controller = new StudentImport();
        $controller->initController(service('request'), service('response'), service('logger'));
        $response = $controller->backup();
        $contents = $response->getBody();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $contents);
        $this->assertStringContainsString('attachment; filename="backup-data-siswa-', $response->getHeaderLine('Content-Disposition'));

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, substr($contents, 3));
        rewind($stream);
        $headers = fgetcsv($stream, null, ',', '"', '\\');
        $values  = fgetcsv($stream, null, ',', '"', '\\');
        fclose($stream);

        $this->assertSame(array_keys($columns), $headers);
        $this->assertCount(count($columns), $values);
        $this->assertSame('00123', $values[array_search('NIPD', array_keys($columns), true)]);
        $this->assertSame('Siti Aminah', $values[array_search('Nama', array_keys($columns), true)]);
        $this->assertSame('7A', $values[array_search('Rombel Saat Ini', array_keys($columns), true)]);
        $this->assertSame('2026/2027', $values[array_search('Tahun Ajaran', array_keys($columns), true)]);
        $this->assertSame('Ahmad', $values[array_search('Nama Ayah', array_keys($columns), true)]);

        $forge->dropTable('students', true);
        $forge->dropTable('school_classes', true);
    }

    public function testNormalizesCommonYesNoValuesAndTreatsPlaceholdersAsEmpty(): void
    {
        $controller = new StudentImport();

        $this->assertSame('Tidak', $this->invoke($controller, 'normalizeYesNo', 'Tidak'));
        $this->assertSame('Ya', $this->invoke($controller, 'normalizeYesNo', 'Layak'));
        $this->assertSame('Tidak', $this->invoke($controller, 'normalizeYesNo', 'Tidak Layak'));
        $this->assertNull($this->invoke($controller, 'normalizeYesNo', 'nilai-asing'));
    }

    public function testNormalizesExcelLocaleCoordinatesAndIntegerValues(): void
    {
        $controller = new StudentImport();

        $this->assertSame('-6.2345', $this->invoke($controller, 'normalizeLocaleNumber', '-6,2345'));
        $this->assertSame('106.1234', $this->invoke($controller, 'normalizeLocaleNumber', '106.1234'));
        $this->assertSame(3, $this->invoke($controller, 'normalizeInteger', '3,0'));
        $this->assertNull($this->invoke($controller, 'normalizeInteger', '3,5'));
    }

    public function testAcceptsPipEligibilityCategoriesAsText(): void
    {
        $data = $this->parseRows(
            "Nama\tNIPD\tJK\tLayak PIP (usulan dari sekolah)\n"
            . "Siti Aminah\t00123\tP\tSiswa Miskin/Rentan Miskin\n",
        );

        $this->assertSame('Siswa Miskin/Rentan Miskin', $data['rows'][0]['eligible_for_pip']);
    }

    public function testNormalizesCoordinateDecimalCommaAndCompassSuffix(): void
    {
        $controller = new StudentImport();

        $this->assertEquals(-6.2345, (float) $this->invoke($controller, 'normalizeCoordinate', '6,2345 LS'));
        $this->assertEquals(106.1234, (float) $this->invoke($controller, 'normalizeCoordinate', '106,1234 BT'));
        $this->assertEquals(-7.8652392, (float) $this->invoke($controller, 'normalizeCoordinate', '-7.8652392'));
        $this->assertEquals(110.2602094, (float) $this->invoke($controller, 'normalizeCoordinate', '110.2602094'));
        $this->assertSame(3, $this->invoke($controller, 'normalizeInteger', '3'));
    }

    public function testTreatsExcelPlaceholdersAsEmpty(): void
    {
        $controller = new StudentImport();

        $this->assertTrue($this->invoke($controller, 'isEmptyValue', '-'));
        $this->assertTrue($this->invoke($controller, 'isEmptyValue', '—'));
        $this->assertFalse($this->invoke($controller, 'isEmptyValue', '0'));
    }

    public function testRejectsMissingRequiredHeaders(): void
    {
        $data = $this->parseRows("nis,nama_lengkap\n1001,Budi Santoso\n");

        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('JK', $data['error']);
    }

    public function testRejectsDuplicateHeaders(): void
    {
        $data = $this->parseRows("nis,student_number,nama_lengkap,jenis_kelamin\n1001,1001,Budi Santoso,L\n");

        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('terduplikasi', $data['error']);
    }

    private function parseRows(string $contents): array
    {
        $controller = new StudentImport();
        $method     = new ReflectionMethod($controller, 'parseRows');

        return $method->invoke($controller, $contents);
    }

    private function invoke(StudentImport $controller, string $methodName, string $value): mixed
    {
        $method = new ReflectionMethod($controller, $methodName);

        return $method->invoke($controller, $value);
    }
}
