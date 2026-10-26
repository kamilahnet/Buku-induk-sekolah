<?php

namespace App\Controllers;

use App\Database\Migrations\CreateReportCards;
use CodeIgniter\Database\MigrationRunner;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Config\Migrations as MigrationConfig;

class ReportCards extends BaseController
{
    private const MAX_FILE_SIZE = 2_097_152;
    private const MAX_ROWS      = 1000;
    private const SUBJECT_COUNT = 11;

    public function index(): string
    {
        $db       = db_connect();
        if (! $db->tableExists('report_card_subjects')
            || ! $db->tableExists('report_card_grades')
            || $db->table('report_card_subjects')->countAllResults() !== self::SUBJECT_COUNT) {
            return view('report_cards/setup', [
                'title' => 'Menyiapkan Nilai Rapor',
            ]);
        }

        $subjects = $this->subjects();
        $periods  = $db->table('report_card_grades')
            ->select('academic_year, semester')
            ->distinct()
            ->orderBy('academic_year', 'DESC')
            ->orderBy('semester')
            ->get()
            ->getResultArray();

        $year          = $this->request->getGet('year');
        $semesterValue = $this->request->getGet('semester');
        $semester      = is_string($semesterValue) ? $this->normalizeSemester($semesterValue) : '';
        $validPeriods = array_map(
            static fn (array $period): string => $period['academic_year'] . '|' . $period['semester'],
            $periods,
        );
        if (! is_string($year) || ! in_array($year . '|' . $semester, $validPeriods, true)) {
            $year     = $periods[0]['academic_year'] ?? '';
            $semester = $periods[0]['semester'] ?? 'Ganjil';
        }

        $records = [];
        if ($year !== '') {
            $records = $db->table('report_card_grades grades')
                ->select('grades.*, students.student_number, students.full_name')
                ->join('students', 'students.id = grades.student_id')
                ->where('grades.academic_year', $year)
                ->where('grades.semester', $semester)
                ->orderBy('students.full_name')
                ->get()
                ->getResultArray();
        }

        return view('report_cards/index', [
            'title'    => 'Nilai Rapor',
            'subjects' => $subjects,
            'periods'  => $periods,
            'year'     => $year,
            'semester' => $semester,
            'records'  => $records,
        ]);
    }

    public function runMigration(): RedirectResponse
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('/report-cards')->with('error', 'Hanya administrator yang dapat menyiapkan tabel nilai rapor.');
        }

        $db = db_connect();
        if (! $db->tableExists('students')) {
            return redirect()->to('/report-cards')
                ->with('error', 'Tabel siswa belum tersedia. Pastikan database aplikasi sudah diinstal terlebih dahulu.');
        }

        $migrationPath  = APPPATH . 'Database/Migrations/2026-10-23-000006_CreateReportCards.php';
        $migrationClass = 'App\\Database\\Migrations\\CreateReportCards';

        try {
            $migrationConfig = config(MigrationConfig::class);
            $migrationConfig->lock = true;
            $runner = service('migrations', $migrationConfig, $db, false);
            if (! $runner instanceof MigrationRunner) {
                throw new \RuntimeException('Migration runner tidak tersedia.');
            }
            $history = $runner->getHistory('default');
            $alreadyApplied = false;
            foreach ($history as $entry) {
                if ($entry->version === '2026-10-23-000006' && $entry->class === $migrationClass) {
                    $alreadyApplied = true;
                    break;
                }
            }

            if ($alreadyApplied) {
                require_once $migrationPath;
                (new CreateReportCards(Database::forge($db)))->up();
            } else {
                if (! $runner->force($migrationPath, 'App\\Database\\Migrations', 'default')) {
                    return redirect()->to('/report-cards')
                        ->with('error', 'Migration tidak berhasil dijalankan. Periksa log aplikasi lalu coba kembali.');
                }
            }

            if (! $db->tableExists('report_card_subjects')
                || ! $db->tableExists('report_card_grades')
                || $db->table('report_card_subjects')->countAllResults() !== self::SUBJECT_COUNT) {
                return redirect()->to('/report-cards')
                    ->with('error', 'Migration belum selesai. Muat ulang halaman sebentar lagi atau periksa log aplikasi.');
            }
        } catch (\RuntimeException $exception) {
            log_message('error', 'Report-card migration failed: {message}', ['message' => $exception->getMessage()]);

            return redirect()->to('/report-cards')
                ->with('error', 'Tabel nilai rapor gagal disiapkan. Periksa koneksi database dan log aplikasi, lalu coba kembali.');
        }

        return redirect()->to('/report-cards')->with('message', 'Tabel nilai rapor berhasil disiapkan.');
    }

    public function subjectsPage(): string|RedirectResponse
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('/report-cards')->with('error', 'Hanya administrator yang dapat mengubah nama mata pelajaran.');
        }
        if (! db_connect()->tableExists('report_card_subjects')) {
            return redirect()->to('/report-cards')->with('error', 'Tabel nilai rapor belum disiapkan.');
        }

        return view('report_cards/subjects', [
            'title'    => 'Nama Mata Pelajaran',
            'subjects' => $this->subjects(),
        ]);
    }

    public function updateSubjects(): RedirectResponse
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('/report-cards')->with('error', 'Hanya administrator yang dapat mengubah nama mata pelajaran.');
        }
        if (! db_connect()->tableExists('report_card_subjects')) {
            return redirect()->to('/report-cards')->with('error', 'Tabel nilai rapor belum disiapkan.');
        }

        $names = $this->request->getPost('subjects');
        if (! is_array($names) || count($names) !== self::SUBJECT_COUNT) {
            return redirect()->back()->with('error', 'Semua 11 nama mata pelajaran harus diisi.');
        }

        $reserved = ['nipd', 'nis', 'student_number', 'tahun_ajaran', 'academic_year', 'semester'];
        $normalizedNames = [];
        $updates = [];
        for ($slot = 1; $slot <= self::SUBJECT_COUNT; $slot++) {
            $name = $names[$slot] ?? null;
            if (! is_string($name)) {
                return redirect()->back()->withInput()->with('error', 'Nama mata pelajaran tidak valid.');
            }
            $name = trim($name);
            if ($name === '' || mb_strlen($name) > 60) {
                return redirect()->back()->withInput()->with('error', 'Nama setiap mata pelajaran wajib diisi dan maksimal 60 karakter.');
            }

            $normalized = $this->normalizeHeader($name);
            if (in_array($normalized, $reserved, true) || in_array($normalized, $normalizedNames, true)) {
                return redirect()->back()->withInput()->with('error', 'Nama mata pelajaran tidak boleh sama satu sama lain atau memakai nama kolom identitas/periode.');
            }
            for ($otherSlot = 1; $otherSlot <= self::SUBJECT_COUNT; $otherSlot++) {
                if ($otherSlot !== $slot && $normalized === 'mapel_' . $otherSlot) {
                    return redirect()->back()->withInput()->with('error', 'Nama mapel tidak boleh memakai label Mapel ' . $otherSlot . ' karena label itu digunakan slot mapel tersebut.');
                }
            }
            $normalizedNames[] = $normalized;
            $updates[] = ['slot' => $slot, 'name' => $name];
        }

        $db = db_connect();
        $db->transBegin();
        foreach ($updates as $update) {
            if ($db->table('report_card_subjects')->where('slot', $update['slot'])->update(['name' => $update['name']]) === false) {
                $db->transRollback();

                return redirect()->back()->with('error', 'Nama mata pelajaran gagal disimpan. Tidak ada perubahan yang diterapkan.');
            }
        }
        if ($db->transStatus() === false) {
            $db->transRollback();

            return redirect()->back()->with('error', 'Nama mata pelajaran gagal disimpan. Tidak ada perubahan yang diterapkan.');
        }
        $db->transCommit();

        return redirect()->to('/report-cards')->with('message', 'Nama mata pelajaran berhasil diperbarui.');
    }

    public function template(): ResponseInterface
    {
        $subjects = $this->subjects();
        $headers  = ['NIPD', 'Nama Siswa', 'Tahun Ajaran', 'Semester'];
        foreach ($subjects as $subject) {
            $headers[] = $subject['name'];
        }

        $stream = fopen('php://temp', 'r+');
        if ($stream === false) {
            throw new \RuntimeException('Template CSV tidak dapat dibuat.');
        }
        fputcsv($stream, $headers, ',', '"', '\\', "\r\n");
        rewind($stream);
        $csv = "\xEF\xBB\xBF" . stream_get_contents($stream);
        fclose($stream);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="template-import-nilai-rapor.csv"')
            ->setBody($csv);
    }

    public function importData(): RedirectResponse
    {
        $uploadedFile = $this->request->getFile('csv_file');
        $pastedData   = $this->request->getPost('pasted_data');
        $hasUpload    = $uploadedFile !== null && $uploadedFile->getError() !== UPLOAD_ERR_NO_FILE;
        $hasPaste     = is_string($pastedData) && trim($pastedData) !== '';

        if ($hasUpload === $hasPaste) {
            return redirect()->back()->with('error', 'Pilih satu cara import: unggah file CSV atau tempel data dari Excel.');
        }

        if ($hasUpload) {
            if (! $uploadedFile->isValid()) {
                return redirect()->back()->with('error', 'File tidak berhasil diunggah. Silakan pilih file CSV kembali.');
            }
            if (strtolower($uploadedFile->getExtension()) !== 'csv') {
                return redirect()->back()->with('error', 'Format file harus CSV. Simpan Excel sebagai CSV UTF-8 terlebih dahulu.');
            }
            if ($uploadedFile->getSize() > self::MAX_FILE_SIZE) {
                return redirect()->back()->with('error', 'Ukuran file maksimal 2 MB.');
            }

            $contents = file_get_contents($uploadedFile->getTempName());
            if ($contents === false) {
                return redirect()->back()->with('error', 'File CSV tidak dapat dibaca.');
            }
        } else {
            $contents = $pastedData;
            if (strlen($contents) > self::MAX_FILE_SIZE) {
                return redirect()->back()->with('error', 'Data yang ditempel terlalu besar. Batas maksimal 2 MB.');
            }
        }

        if (! mb_check_encoding($contents, 'UTF-8')) {
            return redirect()->back()->with('error', 'Data harus menggunakan encoding UTF-8. Simpan file Excel sebagai CSV UTF-8.');
        }

        $parsed = $this->parseRows($contents);
        if (isset($parsed['error'])) {
            return redirect()->back()->with('error', $parsed['error']);
        }
        if ($parsed['rows'] === []) {
            return redirect()->back()->with('error', 'Tidak ada baris nilai rapor untuk diimport.');
        }
        if (count($parsed['rows']) > self::MAX_ROWS) {
            return redirect()->back()->with('error', 'Maksimal ' . self::MAX_ROWS . ' nilai siswa dapat diimport dalam satu proses.');
        }

        $validated = $this->validateRows($parsed['rows'], $parsed['headers']);
        if ($validated['errors'] !== []) {
            return redirect()->back()
                ->with('import_errors', array_slice($validated['errors'], 0, 30))
                ->with('error', 'Import dibatalkan. Perbaiki kesalahan pada data lalu coba kembali.');
        }

        $db = db_connect();
        $db->transBegin();
        foreach ($validated['grades'] as $grade) {
            $existing = $db->table('report_card_grades')
                ->select('id')
                ->where('student_id', $grade['student_id'])
                ->where('academic_year', $grade['academic_year'])
                ->where('semester', $grade['semester'])
                ->get()
                ->getRowArray();

            $grade['updated_at'] = date('Y-m-d H:i:s');
            if ($existing === null) {
                $grade['created_at'] = $grade['updated_at'];
                $saved = $db->table('report_card_grades')->insert($grade);
            } else {
                $saved = $db->table('report_card_grades')->where('id', $existing['id'])->update($grade);
            }
            if ($saved === false) {
                $db->transRollback();

                return redirect()->back()->with('error', 'Nilai gagal disimpan. Import dibatalkan; periksa database lalu coba kembali.');
            }
        }
        if ($db->transStatus() === false) {
            $db->transRollback();

            return redirect()->back()->with('error', 'Nilai gagal disimpan. Import dibatalkan; periksa database lalu coba kembali.');
        }
        $db->transCommit();

        $firstGrade = $validated['grades'][0];
        $query      = http_build_query([
            'year'     => $firstGrade['academic_year'],
            'semester' => $firstGrade['semester'],
        ]);

        return redirect()->to('/report-cards?' . $query)
            ->with('message', count($validated['grades']) . ' nilai rapor berhasil diimport atau diperbarui.');
    }

    private function parseRows(string $contents): array
    {
        $contents  = preg_replace('/^\xEF\xBB\xBF/', '', $contents) ?? $contents;
        $firstLine = strtok($contents, "\r\n");
        if ($firstLine === false || trim($firstLine) === '') {
            return ['error' => 'File atau data yang ditempel masih kosong.'];
        }

        $delimiterCounts = [
            "\t" => substr_count($firstLine, "\t"),
            ','  => substr_count($firstLine, ','),
            ';'  => substr_count($firstLine, ';'),
        ];
        arsort($delimiterCounts);
        $delimiter = (string) array_key_first($delimiterCounts);
        if ($delimiterCounts[$delimiter] === 0) {
            return ['error' => 'Kolom data tidak terdeteksi. Gunakan template CSV atau salin tabel dari Excel.'];
        }

        $stream = fopen('php://temp', 'r+');
        if ($stream === false) {
            return ['error' => 'Data import tidak dapat diproses.'];
        }
        fwrite($stream, $contents);
        rewind($stream);
        $headerRow = fgetcsv($stream, null, $delimiter, '"', '\\');
        if (! is_array($headerRow)) {
            fclose($stream);

            return ['error' => 'Baris header tidak ditemukan.'];
        }

        $headerMap = [
            'nipd' => 'student_number',
            'nis' => 'student_number',
            'student_number' => 'student_number',
            'nama' => 'student_name',
            'nama_siswa' => 'student_name',
            'nama_lengkap' => 'student_name',
            'full_name' => 'student_name',
            'tahun_ajaran' => 'academic_year',
            'academic_year' => 'academic_year',
            'semester' => 'semester',
        ];
        $subjects = $this->subjects();
        foreach ($subjects as $subject) {
            $headerMap[$this->normalizeHeader($subject['name'])] = 'subject_' . $subject['slot'];
            $headerMap['mapel_' . $subject['slot']] = 'subject_' . $subject['slot'];
        }

        $headers = [];
        foreach ($headerRow as $index => $header) {
            $key    = $this->normalizeHeader((string) $header);
            $mapped = $headerMap[$key] ?? null;
            if ($mapped !== null) {
                if (isset($headers[$mapped])) {
                    fclose($stream);

                    return ['error' => 'Header "' . $header . '" terduplikasi. Pastikan setiap kolom hanya satu kali.'];
                }
                $headers[$mapped] = $index;
            }
        }

        $required = ['academic_year', 'semester'];
        for ($slot = 1; $slot <= self::SUBJECT_COUNT; $slot++) {
            $required[] = 'subject_' . $slot;
        }
        if (array_diff($required, array_keys($headers)) !== []
            || (! isset($headers['student_number']) && ! isset($headers['student_name']))) {
            fclose($stream);

            return ['error' => 'Header wajib NIPD atau Nama Siswa, Tahun Ajaran, Semester, dan semua 11 mata pelajaran harus ada. Unduh template terbaru.'];
        }

        $rows = [];
        $line = 1;
        while (($values = fgetcsv($stream, null, $delimiter, '"', '\\')) !== false) {
            $line++;
            if ($values === [null] || count(array_filter($values, static fn ($value): bool => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $row = ['line' => $line];
            foreach ($headers as $field => $index) {
                $row[$field] = trim((string) ($values[$index] ?? ''));
            }
            $rows[] = $row;
        }

        fclose($stream);

        return ['headers' => array_keys($headers), 'rows' => $rows];
    }

    private function validateRows(array $rows, array $headers): array
    {
        $numbers = [];
        $names   = [];
        foreach ($rows as $row) {
            $number = trim((string) ($row['student_number'] ?? ''));
            if ($number !== '') {
                $numbers[] = $number;
            }
            $name = trim((string) ($row['student_name'] ?? ''));
            if ($name !== '') {
                $names[] = $name;
            }
        }

        $db              = db_connect();
        $studentsByNumber = [];
        foreach (array_chunk(array_unique($numbers), 500) as $chunk) {
            $found = $db->table('students')
                ->select('id, student_number')
                ->whereIn('student_number', $chunk)
                ->get()
                ->getResultArray();
            foreach ($found as $student) {
                $studentsByNumber[$student['student_number']] = (int) $student['id'];
            }
        }

        $studentsByName = [];
        foreach (array_chunk(array_unique($names), 500) as $chunk) {
            $found = $db->table('students')
                ->select('id, full_name')
                ->whereIn('full_name', $chunk)
                ->get()
                ->getResultArray();
            foreach ($found as $student) {
                $studentsByName[$this->normalizeStudentName($student['full_name'])][] = (int) $student['id'];
            }
        }

        $errors       = [];
        $grades       = [];
        $seenPeriods  = [];
        $headerFields = array_fill_keys($headers, true);
        foreach ($rows as $row) {
            $line   = (int) $row['line'];
            $issues = [];
            $number = trim((string) ($row['student_number'] ?? ''));
            $name   = trim((string) ($row['student_name'] ?? ''));
            $year   = trim((string) ($row['academic_year'] ?? ''));
            $semester = $this->normalizeSemester((string) ($row['semester'] ?? ''));

            $studentId = null;
            if ($number !== '' && mb_strlen($number) > 30) {
                $issues[] = 'NIPD maksimal 30 karakter';
            } elseif ($number !== '' && isset($studentsByNumber[$number])) {
                $studentId = $studentsByNumber[$number];
            } elseif ($name !== '' && mb_strlen($name) > 150) {
                $issues[] = 'Nama Siswa maksimal 150 karakter';
            } elseif ($name !== '') {
                $nameMatches = $studentsByName[$this->normalizeStudentName($name)] ?? [];
                if (count($nameMatches) === 1) {
                    $studentId = $nameMatches[0];
                } elseif (count($nameMatches) > 1) {
                    $issues[] = 'nama "' . $name . '" cocok dengan lebih dari satu siswa; isi NIPD yang benar';
                } elseif ($number !== '') {
                    $issues[] = 'siswa dengan NIPD "' . $number . '" dan nama "' . $name . '" tidak ditemukan';
                } else {
                    $issues[] = 'siswa dengan nama "' . $name . '" tidak ditemukan';
                }
            } elseif ($number !== '') {
                $issues[] = 'siswa dengan NIPD "' . $number . '" tidak ditemukan; isi Nama Siswa untuk pencarian cadangan';
            } else {
                $issues[] = 'NIPD atau Nama Siswa wajib diisi';
            }
            if (! preg_match('/^\d{4}\/\d{4}$/', $year)
                || (int) substr($year, 5, 4) !== (int) substr($year, 0, 4) + 1) {
                $issues[] = 'Tahun Ajaran harus berformat YYYY/YYYY, misalnya 2025/2026';
            }
            if (! in_array($semester, ['Ganjil', 'Genap'], true)) {
                $issues[] = 'Semester harus Ganjil atau Genap';
            }

            $values = [];
            for ($slot = 1; $slot <= self::SUBJECT_COUNT; $slot++) {
                $field = 'subject_' . $slot;
                $value = trim((string) ($row[$field] ?? ''));
                $normalized = str_replace(',', '.', $value);
                if (! isset($headerFields[$field]) || ! preg_match('/^(?:100(?:\.0{1,2})?|[0-9]{1,2}(?:\.[0-9]{1,2})?)$/', $normalized)) {
                    $issues[] = 'nilai Mapel ' . $slot . ' harus berupa angka 0–100 (maksimal 2 desimal)';
                    continue;
                }
                $values[$field] = $normalized;
            }

            $periodKey = ($studentId ?? $number . '|' . $name) . '|' . $year . '|' . $semester;
            if (isset($seenPeriods[$periodKey])) {
                $issues[] = 'siswa dan periode ini muncul lebih dari satu kali dalam file';
            }
            $seenPeriods[$periodKey] = true;

            if ($issues !== []) {
                $errors[] = 'Baris ' . $line . ': ' . implode('; ', $issues) . '.';
                continue;
            }

            $grades[] = array_merge([
                'student_id'    => $studentId,
                'academic_year' => $year,
                'semester'      => $semester,
            ], $values);
        }

        return ['grades' => $grades, 'errors' => $errors];
    }

    private function subjects(): array
    {
        return db_connect()->table('report_card_subjects')
            ->orderBy('slot')
            ->get()
            ->getResultArray();
    }

    private function normalizeHeader(string $header): string
    {
        $header = mb_strtolower(trim($header));

        return trim(preg_replace('/[^a-z0-9]+/u', '_', $header) ?? '', '_');
    }

    private function normalizeSemester(string $semester): string
    {
        $semester = mb_strtolower(trim($semester));

        return match ($semester) {
            '1', 'ganjil', 'semester 1', 'semester ganjil' => 'Ganjil',
            '2', 'genap', 'semester 2', 'semester genap' => 'Genap',
            default => '',
        };
    }

    private function normalizeStudentName(string $name): string
    {
        return mb_strtolower(trim($name));
    }
}
