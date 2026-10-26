<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class StudentImport extends BaseController
{
    private const MAX_FILE_SIZE = 2_097_152;
    private const MAX_ROWS      = 1000;

    private const TEMPLATE_COLUMNS = [
        'Nama'                         => 'full_name',
        'NIPD'                         => 'student_number',
        'JK'                           => 'gender',
        'NISN'                         => 'nisn',
        'Tempat Lahir'                 => 'birth_place',
        'Tanggal Lahir'                => 'birth_date',
        'NIK'                          => 'nik',
        'Agama'                        => 'religion',
        'Alamat'                       => 'address',
        'RT'                           => 'rt',
        'RW'                           => 'rw',
        'Dusun'                        => 'hamlet',
        'Kelurahan'                    => 'village',
        'Kecamatan'                    => 'district',
        'Kode Pos'                     => 'postal_code',
        'Jenis Tinggal'                => 'residence_type',
        'Alat Transportasi'            => 'transportation',
        'Telepon'                      => 'telephone',
        'HP'                           => 'phone',
        'E-Mail'                       => 'email',
        'SKHUN'                        => 'skhun_number',
        'Penerima KPS'                 => 'receives_kps',
        'No. KPS'                      => 'kps_number',
        'Nama Ayah'                    => 'father_name',
        'Tahun Lahir Ayah'             => 'father_birth_year',
        'Jenjang Pendidikan Ayah'      => 'father_education',
        'Pekerjaan Ayah'               => 'father_occupation',
        'Penghasilan Ayah'             => 'father_income',
        'NIK Ayah'                     => 'father_nik',
        'Nama Ibu'                     => 'mother_name',
        'Tahun Lahir Ibu'              => 'mother_birth_year',
        'Jenjang Pendidikan Ibu'       => 'mother_education',
        'Pekerjaan Ibu'                => 'mother_occupation',
        'Penghasilan Ibu'              => 'mother_income',
        'NIK Ibu'                      => 'mother_nik',
        'Nama Wali'                    => 'guardian_name',
        'Tahun Lahir Wali'             => 'guardian_birth_year',
        'Jenjang Pendidikan Wali'      => 'guardian_education',
        'Pekerjaan Wali'               => 'guardian_occupation',
        'Penghasilan Wali'             => 'guardian_income',
        'NIK Wali'                     => 'guardian_nik',
        'Rombel Saat Ini'              => 'class_name',
        'Tahun Ajaran'                 => 'academic_year',
        'No Peserta Ujian Nasional'    => 'national_exam_number',
        'No Seri Ijazah'               => 'diploma_serial_number',
        'Penerima KIP'                 => 'receives_kip',
        'Nomor KIP'                    => 'kip_number',
        'Nama di KIP'                  => 'kip_name',
        'Nomor KKS'                    => 'kks_number',
        'No Registrasi Akta Lahir'     => 'birth_certificate_number',
        'Bank'                         => 'bank_name',
        'Nomor Rekening Bank'          => 'bank_account_number',
        'Rekening Atas Nama'           => 'bank_account_holder',
        'Layak PIP (usulan dari sekolah)' => 'eligible_for_pip',
        'Alasan Layak PIP'             => 'pip_reason',
        'Kebutuhan Khusus'             => 'special_needs',
        'Sekolah Asal'                 => 'previous_school',
        'Anak ke-berapa'               => 'child_order',
        'Lintang'                      => 'latitude',
        'Bujur'                        => 'longitude',
        'No KK'                        => 'family_card_number',
        'Berat Badan'                  => 'weight_kg',
        'Tinggi Badan'                 => 'height_cm',
        'Lingkar Kepala'               => 'head_circumference_cm',
        'Jml. Saudara Kandung'         => 'sibling_count',
        'Jarak Rumah ke Sekolah (KM)'  => 'distance_to_school_km',
        'Tanggal Masuk'                => 'admission_date',
        'Status Siswa'                 => 'status',
    ];

    private const HEADER_ALIASES = [
        'nis'            => 'student_number',
        'student_number' => 'student_number',
        'nama_lengkap'   => 'full_name',
        'nama_siswa'     => 'full_name',
        'nama'           => 'full_name',
        'full_name'      => 'full_name',
        'jenis_kelamin'  => 'gender',
        'jk'             => 'gender',
        'gender'         => 'gender',
        'tempat_lahir'   => 'birth_place',
        'birth_place'    => 'birth_place',
        'tanggal_lahir'  => 'birth_date',
        'birth_date'     => 'birth_date',
        'alamat'         => 'address',
        'address'        => 'address',
        'nama_ayah'      => 'father_name',
        'father_name'    => 'father_name',
        'nama_ibu'       => 'mother_name',
        'mother_name'    => 'mother_name',
        'no_telepon'     => 'phone',
        'telepon'        => 'phone',
        'phone'          => 'phone',
        'kelas'          => 'class_name',
        'class_name'     => 'class_name',
        'rombel'         => 'class_name',
        'rombel_saat_ini'=> 'class_name',
        'tahun_ajaran'   => 'academic_year',
        'academic_year'  => 'academic_year',
        'tanggal_masuk'  => 'admission_date',
        'admission_date' => 'admission_date',
        'status'         => 'status',
        'nipd'           => 'student_number',
        'nisn'           => 'nisn',
        'nik'            => 'nik',
        'jk'             => 'gender',
        'agama'          => 'religion',
        'rt'             => 'rt',
        'rw'             => 'rw',
        'dusun'          => 'hamlet',
        'kelurahan'      => 'village',
        'desa'           => 'village',
        'kecamatan'      => 'district',
        'kode_pos'       => 'postal_code',
        'jenis_tinggal'  => 'residence_type',
        'alat_transportasi' => 'transportation',
        'telepon'        => 'telephone',
        'hp'             => 'phone',
        'e_mail'         => 'email',
        'email'          => 'email',
        'skhun'          => 'skhun_number',
        'penerima_kps'   => 'receives_kps',
        'no_kps'         => 'kps_number',
        'nama_ayah'      => 'father_name',
        'tahun_lahir_ayah' => 'father_birth_year',
        'jenjang_pendidikan_ayah' => 'father_education',
        'pekerjaan_ayah' => 'father_occupation',
        'penghasilan_ayah' => 'father_income',
        'nik_ayah'       => 'father_nik',
        'nama_ibu'       => 'mother_name',
        'tahun_lahir_ibu' => 'mother_birth_year',
        'jenjang_pendidikan_ibu' => 'mother_education',
        'pekerjaan_ibu'  => 'mother_occupation',
        'penghasilan_ibu' => 'mother_income',
        'nik_ibu'        => 'mother_nik',
        'nama_wali'      => 'guardian_name',
        'tahun_lahir_wali' => 'guardian_birth_year',
        'jenjang_pendidikan_wali' => 'guardian_education',
        'pekerjaan_wali' => 'guardian_occupation',
        'penghasilan_wali' => 'guardian_income',
        'nik_wali'       => 'guardian_nik',
        'no_peserta_ujian_nasional' => 'national_exam_number',
        'no_seri_ijazah' => 'diploma_serial_number',
        'penerima_kip'   => 'receives_kip',
        'nomor_kip'      => 'kip_number',
        'nama_di_kip'    => 'kip_name',
        'nomor_kks'      => 'kks_number',
        'no_registrasi_akta_lahir' => 'birth_certificate_number',
        'bank'           => 'bank_name',
        'nomor_rekening_bank' => 'bank_account_number',
        'rekening_atas_nama' => 'bank_account_holder',
        'layak_pip_usulan_dari_sekolah' => 'eligible_for_pip',
        'alasan_layak_pip' => 'pip_reason',
        'kebutuhan_khusus' => 'special_needs',
        'sekolah_asal'   => 'previous_school',
        'anak_ke_berapa' => 'child_order',
        'lintang'        => 'latitude',
        'bujur'          => 'longitude',
        'no_kk'          => 'family_card_number',
        'berat_badan'    => 'weight_kg',
        'tinggi_badan'   => 'height_cm',
        'lingkar_kepala' => 'head_circumference_cm',
        'jml_saudara_kandung' => 'sibling_count',
        'jarak_rumah_ke_sekolah_km' => 'distance_to_school_km',
    ];

    public function index(): string
    {
        return view('registry/import_students', [
            'title'   => 'Import Data Siswa',
            'headers' => array_keys(self::TEMPLATE_COLUMNS),
        ]);
    }

    public function template(): ResponseInterface
    {
        $stream = fopen('php://temp', 'r+');
        if ($stream === false) {
            throw new \RuntimeException('Template CSV tidak dapat dibuat.');
        }
        fputcsv($stream, array_keys(self::TEMPLATE_COLUMNS), ',', '"', '\\', "\r\n");
        rewind($stream);
        $csv = "\xEF\xBB\xBF" . stream_get_contents($stream);
        fclose($stream);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="template-import-siswa.csv"')
            ->setBody($csv);
    }

    public function backup(): ResponseInterface
    {
        $stream = fopen('php://temp', 'r+');
        if ($stream === false) {
            throw new \RuntimeException('Backup data siswa tidak dapat dibuat.');
        }

        fputcsv($stream, array_keys(self::TEMPLATE_COLUMNS), ',', '"', '\\', "\r\n");
        $students = db_connect()->table('students')
            ->select('students.*, school_classes.name AS class_name, school_classes.academic_year')
            ->join('school_classes', 'school_classes.id = students.class_id', 'left')
            ->orderBy('students.id', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($students as $student) {
            $values = [];
            foreach (self::TEMPLATE_COLUMNS as $field) {
                $values[] = $student[$field] ?? '';
            }
            fputcsv($stream, $values, ',', '"', '\\', "\r\n");
        }

        rewind($stream);
        $csv = "\xEF\xBB\xBF" . stream_get_contents($stream);
        fclose($stream);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="backup-data-siswa-' . date('Y-m-d-His') . '.csv"')
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

        $rows = $parsed['rows'];
        if ($rows === []) {
            return redirect()->back()->with('error', 'Tidak ada baris data siswa untuk diimport.');
        }
        if (count($rows) > self::MAX_ROWS) {
            return redirect()->back()->with('error', 'Maksimal ' . self::MAX_ROWS . ' siswa dapat diimport dalam satu proses.');
        }

        $result = $this->validateRows($rows, $parsed['headers']);
        if ($result['errors'] !== []) {
            return redirect()->back()
                ->with('import_errors', array_slice($result['errors'], 0, 30))
                ->with('error', 'Import dibatalkan. Perbaiki kesalahan pada data lalu coba kembali.');
        }

        $db = db_connect();
        $db->transBegin();
        $inserted = $db->table('students')->insertBatch($result['students']);

        if ($inserted === false || $db->transStatus() === false) {
            $db->transRollback();

            return redirect()->back()->with('error', 'Data gagal disimpan. Tidak ada siswa yang diimport; periksa database lalu coba kembali.');
        }

        $db->transCommit();

        return redirect()->to('/data/students')->with('message', count($result['students']) . ' siswa berhasil diimport.');
    }

    private function parseRows(string $contents): array
    {
        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents) ?? $contents;
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

        $knownHeaders = [];
        foreach (self::TEMPLATE_COLUMNS as $label => $field) {
            $knownHeaders[$this->normalizeHeader($label)] = $field;
        }
        $headers = [];
        foreach ($headerRow as $index => $header) {
            $key = $this->normalizeHeader((string) $header);
            $mapped = $knownHeaders[$key] ?? self::HEADER_ALIASES[$key] ?? null;
            if ($mapped !== null) {
                if (isset($headers[$mapped])) {
                    fclose($stream);

                    return ['error' => 'Header "' . $header . '" terduplikasi. Pastikan setiap kolom hanya satu kali.'];
                }
                $headers[$mapped] = $index;
            }
        }

        foreach (['student_number', 'full_name', 'gender'] as $requiredHeader) {
            if (! array_key_exists($requiredHeader, $headers)) {
                fclose($stream);

                return ['error' => 'Kolom wajib NIPD, Nama, dan JK harus ada. Unduh template agar format kolom sesuai.'];
            }
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
            if (count($rows) > self::MAX_ROWS) {
                fclose($stream);

                return ['error' => 'Maksimal ' . self::MAX_ROWS . ' siswa dapat diimport dalam satu proses.'];
            }
        }

        fclose($stream);

        return ['headers' => array_keys($headers), 'rows' => $rows];
    }

    private function validateRows(array $rows, array $headers): array
    {
        $db        = db_connect();
        $classRows = $db->table('school_classes')->select('id, name, academic_year')->get()->getResultArray();
        $classes   = [];
        foreach ($classRows as $class) {
            $classes[$this->classKey($class['name'])][] = [
                'id'            => (int) $class['id'],
                'academic_year' => mb_strtolower(trim($class['academic_year'])),
            ];
        }

        $studentNumbers = array_values(array_filter(array_map(
            static fn (array $row): string => trim((string) ($row['student_number'] ?? '')),
            $rows,
        ), static fn (string $number): bool => $number !== ''));
        $existing = [];
        if ($studentNumbers !== []) {
            foreach (array_chunk(array_unique($studentNumbers), 500) as $numbers) {
                $found = $db->table('students')
                    ->select('student_number')
                    ->whereIn('student_number', $numbers)
                    ->get()
                    ->getResultArray();
                foreach ($found as $student) {
                    $existing[$student['student_number']] = true;
                }
            }
        }

        $errors       = [];
        $seenNumbers  = [];
        $students     = [];
        $now          = date('Y-m-d H:i:s');
        $hasClass     = in_array('class_name', $headers, true);
        $hasAcademic  = in_array('academic_year', $headers, true);

        foreach ($rows as $row) {
            $line   = $row['line'];
            $number = trim((string) ($row['student_number'] ?? ''));
            $name   = trim((string) ($row['full_name'] ?? ''));
            $gender = $this->normalizeGender((string) ($row['gender'] ?? ''));
            $status = $this->normalizeStatus((string) ($row['status'] ?? ''));
            $issues = [];

            if ($number !== '' && mb_strlen($number) > 30) {
                $issues[] = 'NIPD maksimal 30 karakter';
            } elseif ($number !== '' && isset($existing[$number])) {
                $issues[] = 'NIPD sudah terdaftar';
            } elseif ($number !== '' && isset($seenNumbers[$number])) {
                $issues[] = 'NIPD duplikat di dalam file';
            }
            if ($number !== '') {
                $seenNumbers[$number] = true;
            }
            if ($name === '' || mb_strlen($name) < 2 || mb_strlen($name) > 150) {
                $issues[] = 'nama lengkap wajib diisi (2–150 karakter)';
            }
            if ($gender === null) {
                $issues[] = 'jenis kelamin harus L atau P';
            }

            $birthDate     = $this->normalizeDate((string) ($row['birth_date'] ?? ''));
            $admissionDate = $this->normalizeDate((string) ($row['admission_date'] ?? ''));
            if (($row['birth_date'] ?? '') !== '' && $birthDate === null) {
                $issues[] = 'tanggal lahir harus berformat YYYY-MM-DD atau DD/MM/YYYY';
            }
            if (($row['admission_date'] ?? '') !== '' && $admissionDate === null) {
                $issues[] = 'tanggal masuk harus berformat YYYY-MM-DD atau DD/MM/YYYY';
            }
            if (! in_array($status, ['active', 'graduated', 'transferred'], true)) {
                $issues[] = 'status harus Aktif, Lulus, atau Pindah';
            }

            $classId    = null;
            $className  = trim((string) ($row['class_name'] ?? ''));
            $academicYear = trim((string) ($row['academic_year'] ?? ''));
            if ($className !== '' || $academicYear !== '') {
                if (! $hasClass || $className === '') {
                    $issues[] = 'Rombel Saat Ini harus diisi jika tahun ajaran dicantumkan';
                } else {
                    $matches = $classes[$this->classKey($className)] ?? [];
                    if ($hasAcademic && $academicYear !== '') {
                        $matches = array_values(array_filter(
                            $matches,
                            static fn (array $class): bool => $class['academic_year'] === mb_strtolower($academicYear),
                        ));
                    }
                    if (count($matches) === 1) {
                        $classId = $matches[0]['id'];
                    } elseif ($matches === []) {
                        $issues[] = 'kelas "' . $className . '" tahun ajaran "' . $academicYear . '" tidak ditemukan';
                    } else {
                        $issues[] = 'nama rombel "' . $className . '" ada di beberapa tahun ajaran; isi kolom Tahun Ajaran';
                    }
                }
            }

            $extended = [];
            foreach (self::TEMPLATE_COLUMNS as $field) {
                if (in_array($field, ['full_name', 'student_number', 'gender', 'birth_place', 'birth_date', 'address', 'class_name', 'academic_year', 'admission_date', 'status'], true)) {
                    continue;
                }
                $value = trim((string) ($row[$field] ?? ''));
                $extended[$field] = $this->isEmptyValue($value) ? null : $value;
            }
            foreach (['receives_kps', 'receives_kip'] as $field) {
                if ($extended[$field] !== null) {
                    $normalized = $this->normalizeYesNo($extended[$field]);
                    if ($normalized === null) {
                        $issues[] = $field . ' berisi "' . $extended[$field] . '"; gunakan Ya/Tidak atau kosongkan';
                    } else {
                        $extended[$field] = $normalized;
                    }
                }
            }
            foreach (['father_birth_year', 'mother_birth_year', 'guardian_birth_year'] as $field) {
                if ($extended[$field] !== null && (! ctype_digit($extended[$field]) || (int) $extended[$field] < 1900 || (int) $extended[$field] > 2100)) {
                    $issues[] = $field . ' harus berupa tahun 1900–2100';
                }
            }
            $lengths = [
                'nisn' => 20, 'nik' => 20, 'religion' => 30, 'rt' => 5, 'rw' => 5,
                'hamlet' => 100, 'village' => 100, 'district' => 100, 'postal_code' => 10,
                'residence_type' => 50, 'transportation' => 50, 'telephone' => 30, 'email' => 150,
                'skhun_number' => 50, 'receives_kps' => 5, 'kps_number' => 50,
                'father_name' => 150, 'father_education' => 100, 'father_occupation' => 100, 'father_income' => 50, 'father_nik' => 20,
                'mother_name' => 150, 'mother_education' => 100, 'mother_occupation' => 100, 'mother_income' => 50, 'mother_nik' => 20,
                'guardian_name' => 150, 'guardian_education' => 100, 'guardian_occupation' => 100, 'guardian_income' => 50, 'guardian_nik' => 20,
                'national_exam_number' => 50, 'diploma_serial_number' => 50, 'receives_kip' => 5, 'kip_number' => 50, 'kip_name' => 150,
                'kks_number' => 50, 'birth_certificate_number' => 50, 'bank_name' => 100, 'bank_account_number' => 50,
                'bank_account_holder' => 150, 'eligible_for_pip' => 100, 'special_needs' => 150, 'previous_school' => 150, 'family_card_number' => 20,
            ];
            foreach ($lengths as $field => $limit) {
                if ($extended[$field] !== null && mb_strlen($extended[$field]) > $limit) {
                    $issues[] = $field . ' maksimal ' . $limit . ' karakter';
                }
            }
            if ($extended['email'] !== null && ! filter_var($extended['email'], FILTER_VALIDATE_EMAIL)) {
                $issues[] = 'E-Mail tidak valid';
            }
            if ($extended['pip_reason'] !== null && mb_strlen($extended['pip_reason']) > 1000) {
                $issues[] = 'Alasan Layak PIP maksimal 1000 karakter';
            }
            foreach (['child_order' => [0, 50], 'sibling_count' => [0, 50]] as $field => [$min, $max]) {
                $integer = $extended[$field] === null ? null : $this->normalizeInteger($extended[$field]);
                if ($extended[$field] !== null && ($integer === null || $integer < $min || $integer > $max)) {
                    $issues[] = $field . ' harus bilangan bulat ' . $min . '–' . $max;
                } elseif ($integer !== null) {
                    $extended[$field] = (string) $integer;
                }
            }
            foreach ([
                'latitude' => [-90, 90], 'longitude' => [-180, 180], 'weight_kg' => [0, 500],
                'height_cm' => [0, 300], 'head_circumference_cm' => [0, 200], 'distance_to_school_km' => [0, 1000],
            ] as $field => [$min, $max]) {
                if ($extended[$field] !== null) {
                    $numberValue = in_array($field, ['latitude', 'longitude'], true)
                        ? $this->normalizeCoordinate($extended[$field])
                        : $this->normalizeLocaleNumber($extended[$field]);
                    if (! is_numeric($numberValue) || (float) $numberValue < $min || (float) $numberValue > $max) {
                        $issues[] = $field . ' harus berupa angka antara ' . $min . ' dan ' . $max;
                    } else {
                        $extended[$field] = $numberValue;
                    }
                }
            }

            foreach (['birth_place' => 100, 'father_name' => 150, 'mother_name' => 150, 'phone' => 30] as $field => $maxLength) {
                if (mb_strlen((string) ($row[$field] ?? '')) > $maxLength) {
                    $issues[] = $field . ' maksimal ' . $maxLength . ' karakter';
                }
            }
            if (mb_strlen((string) ($row['address'] ?? '')) > 1000) {
                $issues[] = 'alamat maksimal 1000 karakter';
            }

            if ($issues !== []) {
                $errors[] = 'Baris ' . $line . ': ' . implode('; ', $issues) . '.';
                continue;
            }

            $students[] = array_merge([
                'student_number' => $number === '' ? null : $number,
                'full_name'      => $name,
                'gender'         => $gender,
                'birth_place'    => $this->nullableValue($row['birth_place'] ?? null),
                'birth_date'     => $birthDate,
                'address'        => $this->nullableValue($row['address'] ?? null),
                'father_name'    => $this->nullableValue($row['father_name'] ?? null),
                'mother_name'    => $this->nullableValue($row['mother_name'] ?? null),
                'phone'          => $this->nullableValue($row['phone'] ?? null),
                'class_id'       => $classId,
                'admission_date' => $admissionDate,
                'status'         => $status,
                'created_at'     => $now,
                'updated_at'     => $now,
            ], $extended);
        }

        return ['students' => $students, 'errors' => $errors];
    }

    private function normalizeGender(string $value): ?string
    {
        return match (strtolower(trim($value))) {
            'l', 'laki-laki', 'laki laki', 'putra' => 'L',
            'p', 'perempuan', 'putri' => 'P',
            default => null,
        };
    }

    private function normalizeStatus(string $value): ?string
    {
        return match (strtolower(trim($value))) {
            '', 'aktif', 'active' => 'active',
            'lulus', 'graduated' => 'graduated',
            'pindah', 'transferred' => 'transferred',
            default => null,
        };
    }

    private function normalizeYesNo(string $value): ?string
    {
        return match (strtolower(trim($value))) {
            'ya', 'yes', 'y', '1', 'true', 'layak', 'menerima', 'layak pip' => 'Ya',
            'tidak', 'no', 'n', '0', 'false', 'tidak layak', 'tidak menerima', 'tidak layak pip' => 'Tidak',
            default => null,
        };
    }

    private function isEmptyValue(string $value): bool
    {
        return in_array(mb_strtolower(trim($value)), ['', '-', '--', '—', '–', 'n/a', 'na', 'null', 'n.a.'], true);
    }

    private function normalizeLocaleNumber(string $value): string
    {
        $value = str_replace(["\u{2212}", ' '], ['-', ''], trim($value));
        if (str_contains($value, ',') && ! str_contains($value, '.')) {
            return str_replace(',', '.', $value);
        }

        return $value;
    }

    private function normalizeInteger(string $value): ?int
    {
        $value = $this->normalizeLocaleNumber($value);
        if ($value === '' || ! is_numeric($value) || floor((float) $value) !== (float) $value) {
            return null;
        }

        return (int) $value;
    }

    private function normalizeCoordinate(string $value): string
    {
        $value = mb_strtoupper(trim($value));
        $value = str_replace(["\u{2212}", ' '], ['-', ''], $value);
        $direction = null;
        if (preg_match('/(LU|LS|BT|[NSEW])$/u', $value, $matches) === 1) {
            $direction = $matches[1];
            $value = substr($value, 0, -strlen($direction));
        }

        $value = str_replace('°', '', $value);
        $value = $this->normalizeLocaleNumber($value);
        if (is_numeric($value) && $direction !== null && in_array($direction, ['S', 'W', 'LS'], true)) {
            return (string) -abs((float) $value);
        }

        return $value;
    }

    private function normalizeDate(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        foreach (['!Y-m-d' => 'Y-m-d', '!d/m/Y' => 'd/m/Y'] as $format => $displayFormat) {
            $date   = \DateTimeImmutable::createFromFormat($format, $value);
            $errors = \DateTimeImmutable::getLastErrors();
            if ($date !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0)) && $date->format($displayFormat) === $value) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    private function classKey(string $name): string
    {
        return mb_strtolower(trim($name));
    }

    private function normalizeHeader(string $header): string
    {
        $key = strtolower(trim($header));

        return trim((string) preg_replace('/[^a-z0-9]+/', '_', $key), '_');
    }
    private function nullableValue(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
