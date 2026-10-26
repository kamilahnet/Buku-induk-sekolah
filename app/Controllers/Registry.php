<?php

namespace App\Controllers;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\RawSql;
use CodeIgniter\HTTP\RedirectResponse;

class Registry extends BaseController
{
    private const RESOURCES = [
        'students' => [
            'title' => 'Data Siswa',
            'table' => 'students',
            'fields' => [
                'full_name'                => ['label' => 'Nama', 'type' => 'text', 'group' => 'Data Utama', 'rules' => 'required|min_length[2]|max_length[150]'],
                'student_number'           => ['label' => 'NIPD', 'type' => 'text', 'group' => 'Data Utama', 'rules' => 'permit_empty|max_length[30]|is_unique[students.student_number]'],
                'gender'                   => ['label' => 'JK', 'type' => 'select', 'group' => 'Data Utama', 'options' => ['L' => 'Laki-laki', 'P' => 'Perempuan'], 'rules' => 'required|in_list[L,P]'],
                'nisn'                     => ['label' => 'NISN', 'type' => 'text', 'group' => 'Data Utama', 'rules' => 'permit_empty|max_length[20]'],
                'birth_place'              => ['label' => 'Tempat Lahir', 'type' => 'text', 'group' => 'Data Utama', 'rules' => 'permit_empty|max_length[100]'],
                'birth_date'               => ['label' => 'Tanggal Lahir', 'type' => 'date', 'group' => 'Data Utama', 'rules' => 'permit_empty|valid_date[Y-m-d]'],
                'nik'                      => ['label' => 'NIK', 'type' => 'text', 'group' => 'Data Utama', 'rules' => 'permit_empty|max_length[20]'],
                'religion'                 => ['label' => 'Agama', 'type' => 'text', 'group' => 'Data Utama', 'rules' => 'permit_empty|max_length[30]'],
                'address'                  => ['label' => 'Alamat', 'type' => 'textarea', 'group' => 'Alamat dan Kontak', 'rules' => 'permit_empty|max_length[1000]'],
                'rt'                       => ['label' => 'RT', 'type' => 'text', 'group' => 'Alamat dan Kontak', 'rules' => 'permit_empty|max_length[5]'],
                'rw'                       => ['label' => 'RW', 'type' => 'text', 'group' => 'Alamat dan Kontak', 'rules' => 'permit_empty|max_length[5]'],
                'hamlet'                   => ['label' => 'Dusun', 'type' => 'text', 'group' => 'Alamat dan Kontak', 'rules' => 'permit_empty|max_length[100]'],
                'village'                  => ['label' => 'Kelurahan/Desa', 'type' => 'text', 'group' => 'Alamat dan Kontak', 'rules' => 'permit_empty|max_length[100]'],
                'district'                 => ['label' => 'Kecamatan', 'type' => 'text', 'group' => 'Alamat dan Kontak', 'rules' => 'permit_empty|max_length[100]'],
                'postal_code'              => ['label' => 'Kode Pos', 'type' => 'text', 'group' => 'Alamat dan Kontak', 'rules' => 'permit_empty|max_length[10]'],
                'residence_type'           => ['label' => 'Jenis Tinggal', 'type' => 'text', 'group' => 'Alamat dan Kontak', 'rules' => 'permit_empty|max_length[50]'],
                'transportation'           => ['label' => 'Alat Transportasi', 'type' => 'text', 'group' => 'Alamat dan Kontak', 'rules' => 'permit_empty|max_length[50]'],
                'telephone'                => ['label' => 'Telepon', 'type' => 'text', 'group' => 'Alamat dan Kontak', 'rules' => 'permit_empty|max_length[30]'],
                'phone'                    => ['label' => 'HP', 'type' => 'text', 'group' => 'Alamat dan Kontak', 'rules' => 'permit_empty|max_length[30]'],
                'email'                    => ['label' => 'E-Mail', 'type' => 'email', 'group' => 'Alamat dan Kontak', 'rules' => 'permit_empty|valid_email|max_length[150]'],
                'skhun_number'             => ['label' => 'SKHUN', 'type' => 'text', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|max_length[50]'],
                'receives_kps'             => ['label' => 'Penerima KPS', 'type' => 'yes_no', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|in_list[Ya,Tidak]'],
                'kps_number'               => ['label' => 'No. KPS', 'type' => 'text', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|max_length[50]'],
                'father_name'              => ['label' => 'Nama Ayah', 'type' => 'text', 'group' => 'Data Ayah', 'rules' => 'permit_empty|max_length[150]'],
                'father_birth_year'        => ['label' => 'Tahun Lahir', 'type' => 'number', 'group' => 'Data Ayah', 'rules' => 'permit_empty|integer|greater_than_equal_to[1900]|less_than_equal_to[2100]'],
                'father_education'         => ['label' => 'Jenjang Pendidikan', 'type' => 'text', 'group' => 'Data Ayah', 'rules' => 'permit_empty|max_length[100]'],
                'father_occupation'        => ['label' => 'Pekerjaan', 'type' => 'text', 'group' => 'Data Ayah', 'rules' => 'permit_empty|max_length[100]'],
                'father_income'            => ['label' => 'Penghasilan', 'type' => 'text', 'group' => 'Data Ayah', 'rules' => 'permit_empty|max_length[50]'],
                'father_nik'               => ['label' => 'NIK', 'type' => 'text', 'group' => 'Data Ayah', 'rules' => 'permit_empty|max_length[20]'],
                'mother_name'              => ['label' => 'Nama Ibu', 'type' => 'text', 'group' => 'Data Ibu', 'rules' => 'permit_empty|max_length[150]'],
                'mother_birth_year'        => ['label' => 'Tahun Lahir', 'type' => 'number', 'group' => 'Data Ibu', 'rules' => 'permit_empty|integer|greater_than_equal_to[1900]|less_than_equal_to[2100]'],
                'mother_education'         => ['label' => 'Jenjang Pendidikan', 'type' => 'text', 'group' => 'Data Ibu', 'rules' => 'permit_empty|max_length[100]'],
                'mother_occupation'        => ['label' => 'Pekerjaan', 'type' => 'text', 'group' => 'Data Ibu', 'rules' => 'permit_empty|max_length[100]'],
                'mother_income'            => ['label' => 'Penghasilan', 'type' => 'text', 'group' => 'Data Ibu', 'rules' => 'permit_empty|max_length[50]'],
                'mother_nik'               => ['label' => 'NIK', 'type' => 'text', 'group' => 'Data Ibu', 'rules' => 'permit_empty|max_length[20]'],
                'guardian_name'            => ['label' => 'Nama Wali', 'type' => 'text', 'group' => 'Data Wali', 'rules' => 'permit_empty|max_length[150]'],
                'guardian_birth_year'      => ['label' => 'Tahun Lahir', 'type' => 'number', 'group' => 'Data Wali', 'rules' => 'permit_empty|integer|greater_than_equal_to[1900]|less_than_equal_to[2100]'],
                'guardian_education'       => ['label' => 'Jenjang Pendidikan', 'type' => 'text', 'group' => 'Data Wali', 'rules' => 'permit_empty|max_length[100]'],
                'guardian_occupation'      => ['label' => 'Pekerjaan', 'type' => 'text', 'group' => 'Data Wali', 'rules' => 'permit_empty|max_length[100]'],
                'guardian_income'          => ['label' => 'Penghasilan', 'type' => 'text', 'group' => 'Data Wali', 'rules' => 'permit_empty|max_length[50]'],
                'guardian_nik'             => ['label' => 'NIK', 'type' => 'text', 'group' => 'Data Wali', 'rules' => 'permit_empty|max_length[20]'],
                'class_id'                 => ['label' => 'Rombel Saat Ini', 'type' => 'classes', 'group' => 'Data Sekolah', 'rules' => 'permit_empty|is_natural_no_zero|is_not_unique[school_classes.id]'],
                'national_exam_number'     => ['label' => 'No Peserta Ujian Nasional', 'type' => 'text', 'group' => 'Data Sekolah', 'rules' => 'permit_empty|max_length[50]'],
                'diploma_serial_number'    => ['label' => 'No Seri Ijazah', 'type' => 'text', 'group' => 'Data Sekolah', 'rules' => 'permit_empty|max_length[50]'],
                'receives_kip'             => ['label' => 'Penerima KIP', 'type' => 'yes_no', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|in_list[Ya,Tidak]'],
                'kip_number'               => ['label' => 'Nomor KIP', 'type' => 'text', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|max_length[50]'],
                'kip_name'                 => ['label' => 'Nama di KIP', 'type' => 'text', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|max_length[150]'],
                'kks_number'               => ['label' => 'Nomor KKS', 'type' => 'text', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|max_length[50]'],
                'birth_certificate_number' => ['label' => 'No Registrasi Akta Lahir', 'type' => 'text', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|max_length[50]'],
                'bank_name'                => ['label' => 'Bank', 'type' => 'text', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|max_length[100]'],
                'bank_account_number'      => ['label' => 'Nomor Rekening Bank', 'type' => 'text', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|max_length[50]'],
                'bank_account_holder'      => ['label' => 'Rekening Atas Nama', 'type' => 'text', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|max_length[150]'],
                'eligible_for_pip'         => ['label' => 'Layak PIP (usulan dari sekolah)', 'type' => 'text', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|max_length[100]'],
                'pip_reason'               => ['label' => 'Alasan Layak PIP', 'type' => 'textarea', 'group' => 'Dokumen dan Bantuan', 'rules' => 'permit_empty|max_length[1000]'],
                'special_needs'            => ['label' => 'Kebutuhan Khusus', 'type' => 'text', 'group' => 'Data Tambahan', 'rules' => 'permit_empty|max_length[150]'],
                'previous_school'          => ['label' => 'Sekolah Asal', 'type' => 'text', 'group' => 'Data Tambahan', 'rules' => 'permit_empty|max_length[150]'],
                'child_order'              => ['label' => 'Anak ke-berapa', 'type' => 'number', 'group' => 'Data Tambahan', 'rules' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[50]'],
                'latitude'                 => ['label' => 'Lintang', 'type' => 'number', 'step' => 'any', 'group' => 'Data Tambahan', 'rules' => 'permit_empty|decimal|greater_than_equal_to[-90]|less_than_equal_to[90]'],
                'longitude'                => ['label' => 'Bujur', 'type' => 'number', 'step' => 'any', 'group' => 'Data Tambahan', 'rules' => 'permit_empty|decimal|greater_than_equal_to[-180]|less_than_equal_to[180]'],
                'family_card_number'       => ['label' => 'No KK', 'type' => 'text', 'group' => 'Data Tambahan', 'rules' => 'permit_empty|max_length[20]'],
                'weight_kg'                => ['label' => 'Berat Badan (kg)', 'type' => 'number', 'step' => '0.01', 'group' => 'Data Tambahan', 'rules' => 'permit_empty|decimal|greater_than[0]|less_than[500]'],
                'height_cm'                => ['label' => 'Tinggi Badan (cm)', 'type' => 'number', 'step' => '0.01', 'group' => 'Data Tambahan', 'rules' => 'permit_empty|decimal|greater_than[0]|less_than[300]'],
                'head_circumference_cm'    => ['label' => 'Lingkar Kepala (cm)', 'type' => 'number', 'step' => '0.01', 'group' => 'Data Tambahan', 'rules' => 'permit_empty|decimal|greater_than[0]|less_than[200]'],
                'sibling_count'            => ['label' => 'Jml. Saudara Kandung', 'type' => 'number', 'group' => 'Data Tambahan', 'rules' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[50]'],
                'distance_to_school_km'    => ['label' => 'Jarak Rumah ke Sekolah (KM)', 'type' => 'number', 'step' => '0.01', 'group' => 'Data Tambahan', 'rules' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than[1000]'],
                'admission_date'           => ['label' => 'Tanggal Masuk', 'type' => 'date', 'group' => 'Data Sekolah', 'rules' => 'permit_empty|valid_date[Y-m-d]'],
                'status'                   => ['label' => 'Status Siswa', 'type' => 'select', 'group' => 'Data Sekolah', 'options' => ['active' => 'Aktif', 'graduated' => 'Lulus', 'transferred' => 'Pindah'], 'rules' => 'required|in_list[active,graduated,transferred]'],
            ],
            'columns' => ['full_name' => 'Nama', 'student_number' => 'NIPD', 'gender' => 'JK', 'nisn' => 'NISN', 'birth_place' => 'Tempat Lahir', 'birth_date' => 'Tanggal Lahir', 'nik' => 'NIK', 'class_name' => 'Rombel Saat Ini', 'status' => 'Status'],
        ],
        'teachers' => [
            'title' => 'Data Guru',
            'table' => 'teachers',
            'fields' => [
                'employee_number' => ['label' => 'NIP', 'type' => 'text', 'rules' => 'required|max_length[30]|is_unique[teachers.employee_number]'],
                'full_name'       => ['label' => 'Nama lengkap', 'type' => 'text', 'rules' => 'required|min_length[2]|max_length[150]'],
                'subject'         => ['label' => 'Mata pelajaran', 'type' => 'text', 'rules' => 'permit_empty|max_length[100]'],
                'phone'           => ['label' => 'Nomor telepon', 'type' => 'text', 'rules' => 'permit_empty|max_length[30]'],
                'email'           => ['label' => 'Email', 'type' => 'email', 'rules' => 'permit_empty|valid_email|max_length[150]'],
                'status'          => ['label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Aktif', 'inactive' => 'Tidak aktif'], 'rules' => 'required|in_list[active,inactive]'],
            ],
            'columns' => ['employee_number' => 'NIP', 'full_name' => 'Nama', 'subject' => 'Mata pelajaran', 'status' => 'Status'],
        ],
        'classes' => [
            'title' => 'Data Kelas',
            'table' => 'school_classes',
            'fields' => [
                'name'               => ['label' => 'Nama kelas', 'type' => 'text', 'rules' => 'required|max_length[50]'],
                'grade_level'        => ['label' => 'Tingkat', 'type' => 'number', 'rules' => 'required|integer|greater_than[0]|less_than[13]'],
                'academic_year'      => ['label' => 'Tahun ajaran', 'type' => 'text', 'rules' => 'required|regex_match[/^\d{4}\/\d{4}$/]'],
                'homeroom_teacher_id' => ['label' => 'Wali kelas', 'type' => 'teachers', 'rules' => 'permit_empty|is_natural_no_zero|is_not_unique[teachers.id]'],
            ],
            'columns' => ['name' => 'Nama kelas', 'grade_level' => 'Tingkat', 'academic_year' => 'Tahun ajaran', 'teacher_name' => 'Wali kelas'],
        ],
        'users' => [
            'title' => 'Pengguna',
            'table' => 'users',
            'fields' => [
                'name'     => ['label' => 'Nama lengkap', 'type' => 'text', 'rules' => 'required|min_length[2]|max_length[100]'],
                'username' => ['label' => 'Nama pengguna', 'type' => 'text', 'rules' => 'required|min_length[3]|max_length[50]|alpha_numeric_punct|is_unique[users.username]'],
                'password' => ['label' => 'Kata sandi', 'type' => 'password', 'rules' => 'required|min_length[12]|max_length[255]'],
                'role'     => ['label' => 'Hak akses', 'type' => 'select', 'options' => ['admin' => 'Administrator', 'operator' => 'Operator'], 'rules' => 'required|in_list[admin,operator]'],
            ],
            'columns' => ['name' => 'Nama', 'username' => 'Nama pengguna', 'role' => 'Hak akses'],
        ],
    ];

    public function index(string $resource): string|RedirectResponse
    {
        $config = $this->resource($resource);
        if ($config instanceof RedirectResponse) {
            return $config;
        }

        $db      = db_connect();
        $builder = $db->table($config['table']);
        if ($resource === 'students') {
            $builder->select('students.*, school_classes.name AS class_name')
                ->join('school_classes', 'school_classes.id = students.class_id', 'left');
        } elseif ($resource === 'classes') {
            $builder->select('school_classes.*, teachers.full_name AS teacher_name')
                ->join('teachers', 'teachers.id = school_classes.homeroom_teacher_id', 'left');
        } elseif ($resource === 'users') {
            $builder->select('id, name, username, role, created_at');
        } else {
            $builder->select('*');
        }

        $pagination = [];
        $incompleteStudents = 0;
        if ($resource === 'students') {
            $mainFields = $this->mainDataFields($config);
            $allowedPerPage = range(10, 100, 10);
            $requestedLimit = filter_var($this->request->getGet('per_page'), FILTER_VALIDATE_INT);
            $perPage        = in_array($requestedLimit, $allowedPerPage, true) ? $requestedLimit : 10;
            $requestedSearch = $this->request->getGet('search');
            $search          = is_string($requestedSearch) ? trim($requestedSearch) : '';
            $page           = filter_var($this->request->getGet('page_students'), FILTER_VALIDATE_INT);
            $page           = $page !== false && $page !== null ? max(1, $page) : 1;
            $countBuilder = $db->table('students')
                ->join('school_classes', 'school_classes.id = students.class_id', 'left');
            if ($search !== '') {
                $countBuilder->groupStart()
                    ->like('students.full_name', $search)
                    ->orLike('students.student_number', $search)
                    ->orLike('students.nisn', $search)
                    ->orLike('school_classes.name', $search)
                    ->groupEnd();
                $builder->groupStart()
                    ->like('students.full_name', $search)
                    ->orLike('students.student_number', $search)
                    ->orLike('students.nisn', $search)
                    ->orLike('school_classes.name', $search)
                    ->groupEnd();
            }
            $total          = $countBuilder->countAllResults();
            $incompleteStudents = $this->countIncompleteStudents($db, $mainFields);
            $pageCount      = (int) ceil($total / $perPage);
            $page           = $pageCount > 0 ? min($page, $pageCount) : 1;
            $records        = $builder
                ->orderBy('students.id', 'DESC')
                ->limit($perPage, ($page - 1) * $perPage)
                ->get()
                ->getResultArray();
            foreach ($records as &$record) {
                $record['missing_main_fields'] = $this->missingMainDataFields($record, $mainFields);
            }
            unset($record);
            $pager = service('pager')->only(['per_page', 'search']);

            $pagination = [
                'perPage'       => $perPage,
                'total'         => $total,
                'page'          => $page,
                'firstItem'     => $total === 0 ? 0 : (($page - 1) * $perPage) + 1,
                'lastItem'      => min($page * $perPage, $total),
                'search'        => $search,
                'links'         => $pager->makeLinks($page, $perPage, $total, 'default_full', 0, 'students'),
            ];
        } else {
            $records = $builder->orderBy('id', 'DESC')->get()->getResultArray();
        }

        return view('registry/index', [
            'title'   => $config['title'],
            'resource'=> $resource,
            'config'  => $config,
            'records' => $records,
            'pagination' => $pagination,
            'incompleteStudents' => $incompleteStudents,
        ]);
    }

    public function new(string $resource): string|RedirectResponse
    {
        return $this->form($resource);
    }

    public function showStudent(int $id): string|RedirectResponse
    {
        $config = $this->resource('students');
        if ($config instanceof RedirectResponse) {
            return $config;
        }

        $record = db_connect()->table('students')
            ->select('students.*, school_classes.name AS class_name, school_classes.academic_year AS class_academic_year')
            ->join('school_classes', 'school_classes.id = students.class_id', 'left')
            ->where('students.id', $id)
            ->get()
            ->getRowArray();
        if ($record === null) {
            return redirect()->to('/data/students')->with('error', 'Data siswa tidak ditemukan.');
        }
        $missingMainFields = $this->missingMainDataFields($record, $this->mainDataFields($config));

        return view('registry/student_detail', [
            'title'  => 'Detail Siswa',
            'config' => $config,
            'record' => $record,
            'missingMainFields' => $missingMainFields,
        ]);
    }

    public function create(string $resource): RedirectResponse
    {
        return $this->save($resource);
    }

    public function edit(string $resource, int $id): string|RedirectResponse
    {
        return $this->form($resource, $id);
    }

    public function update(string $resource, int $id): RedirectResponse
    {
        return $this->save($resource, $id);
    }

    public function delete(string $resource, int $id): RedirectResponse
    {
        $config = $this->resource($resource);
        if ($config instanceof RedirectResponse) {
            return $config;
        }

        $db = db_connect();
        $record = $db->table($config['table'])->where('id', $id)->get()->getRowArray();
        if ($record === null) {
            return redirect()->to('/data/' . $resource)->with('error', 'Data tidak ditemukan.');
        }
        if ($resource === 'users' && (int) $record['id'] === (int) session()->get('user_id')) {
            return redirect()->to('/data/users')->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }
        if ($resource === 'users' && $record['role'] === 'admin') {
            $adminCount = $db->table('users')->where('role', 'admin')->countAllResults();
            if ($adminCount <= 1) {
                return redirect()->to('/data/users')->with('error', 'Administrator terakhir tidak dapat dihapus.');
            }
        }

        $db->table($config['table'])->where('id', $id)->delete();

        return redirect()->to('/data/' . $resource)->with('message', 'Data berhasil dihapus.');
    }

    public function deleteAllStudents(): RedirectResponse
    {
        if (session()->get('user_role') !== 'admin') {
            return redirect()->to('/data/students')->with('error', 'Hanya administrator yang dapat menghapus seluruh data siswa.');
        }

        $confirmation = $this->request->getPost('confirmation');
        if (! is_string($confirmation) || trim($confirmation) !== 'HAPUS SEMUA SISWA') {
            return redirect()->to('/data/students')->with('error', 'Konfirmasi tidak sesuai. Tidak ada data siswa yang dihapus.');
        }

        $db = db_connect();
        $db->transBegin();
        $db->table('students')->where('id >', 0)->delete();

        if ($db->transStatus() === false) {
            $db->transRollback();

            return redirect()->to('/data/students')->with('error', 'Data siswa gagal dihapus. Perubahan dibatalkan.');
        }

        $deletedCount = $db->affectedRows();
        $db->transCommit();

        return redirect()->to('/data/students')->with('message', $deletedCount . ' data siswa berhasil dihapus.');
    }

    private function form(string $resource, ?int $id = null): string|RedirectResponse
    {
        $config = $this->resource($resource);
        if ($config instanceof RedirectResponse) {
            return $config;
        }

        $record = $id === null
            ? []
            : db_connect()->table($config['table'])->where('id', $id)->get()->getRowArray();
        if ($id !== null && $record === null) {
            return redirect()->to('/data/' . $resource)->with('error', 'Data tidak ditemukan.');
        }

        $options = [];
        foreach ($config['fields'] as $field) {
            if ($field['type'] === 'classes') {
                $options['class_id'] = db_connect()->table('school_classes')->orderBy('name')->get()->getResultArray();
            } elseif ($field['type'] === 'teachers') {
                $options['homeroom_teacher_id'] = db_connect()->table('teachers')->where('status', 'active')->orderBy('full_name')->get()->getResultArray();
            }
        }
        if ($resource === 'users' && $id !== null) {
            unset($config['fields']['password']);
        }

        return view('registry/form', [
            'title'    => ($id === null ? 'Tambah ' : 'Ubah ') . $config['title'],
            'resource' => $resource,
            'config'   => $config,
            'record'   => $record ?? [],
            'options'  => $options,
            'id'       => $id,
        ]);
    }

    private function save(string $resource, ?int $id = null): RedirectResponse
    {
        $config = $this->resource($resource);
        if ($config instanceof RedirectResponse) {
            return $config;
        }
        if ($id !== null && db_connect()->table($config['table'])->where('id', $id)->countAllResults() === 0) {
            return redirect()->to('/data/' . $resource)->with('error', 'Data tidak ditemukan.');
        }

        if ($resource === 'users' && $id !== null) {
            $currentUser = db_connect()->table('users')->where('id', $id)->get()->getRowArray();
            if ((int) $id === (int) session()->get('user_id') && $this->request->getPost('role') === 'operator') {
                return redirect()->back()->withInput()->with('error', 'Hak akses akun yang sedang digunakan tidak dapat diturunkan.');
            }
            if ($currentUser !== null && $currentUser['role'] === 'admin' && $this->request->getPost('role') === 'operator') {
                $adminCount = db_connect()->table('users')->where('role', 'admin')->countAllResults();
                if ($adminCount <= 1) {
                    return redirect()->back()->withInput()->with('error', 'Administrator terakhir tidak dapat diubah menjadi operator.');
                }
            }
        }

        foreach ($config['fields'] as $name => $field) {
            $value = $this->request->getPost($name);
            if ($value !== null && ! is_string($value)) {
                return redirect()->back()->with('error', 'Data pada kolom "' . $field['label'] . '" tidak valid.');
            }
        }
        if ($resource === 'classes') {
            $duplicateClass = db_connect()->table('school_classes')
                ->where('name', trim((string) $this->request->getPost('name')))
                ->where('academic_year', trim((string) $this->request->getPost('academic_year')));
            if ($id !== null) {
                $duplicateClass->where('id !=', $id);
            }
            if ($duplicateClass->countAllResults() > 0) {
                return redirect()->back()->withInput()->with('errors', ['name' => 'Nama kelas sudah terdaftar pada tahun ajaran tersebut.']);
            }
        }

        $rules = [];
        foreach ($config['fields'] as $name => $field) {
            $rule = $field['rules'];
            if ($id !== null && str_contains($rule, '|is_unique[')) {
                $rule = preg_replace('/\|is_unique\[[^\]]+\]/', '|is_unique[' . $config['table'] . '.' . $name . ',id,' . $id . ']', $rule);
            }
            if ($resource === 'users' && $name === 'password' && $id !== null) {
                $rule = 'permit_empty|min_length[12]|max_length[255]';
            }
            $rules[$name] = $rule;
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [];
        foreach ($config['fields'] as $name => $field) {
            $value = $this->request->getPost($name);
            if ($resource === 'users' && $name === 'password') {
                if ($value === '' && $id !== null) {
                    continue;
                }
                $value = password_hash((string) $value, PASSWORD_DEFAULT);
            }
            $data[$name] = $value === '' ? null : $value;
        }
        $data['updated_at'] = date('Y-m-d H:i:s');

        $builder = db_connect()->table($config['table']);
        if ($id === null) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $builder->insert($data);
            $message = 'Data berhasil ditambahkan.';
        } else {
            $builder->where('id', $id)->update($data);
            $message = 'Data berhasil diperbarui.';
        }

        return redirect()->to('/data/' . $resource)->with('message', $message);
    }

    private function resource(string $resource): array|RedirectResponse
    {
        if (! isset(self::RESOURCES[$resource])) {
            return redirect()->to('/dashboard')->with('error', 'Modul tidak ditemukan.');
        }
        if ($resource === 'users' && session()->get('user_role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Hanya administrator yang dapat mengelola pengguna.');
        }

        return self::RESOURCES[$resource];
    }

    private function mainDataFields(array $config): array
    {
        $fields = [];
        foreach ($config['fields'] as $name => $field) {
            if (($field['group'] ?? '') === 'Data Utama') {
                $fields[$name] = $field['label'];
            }
        }

        return $fields;
    }

    private function missingMainDataFields(array $record, array $fields): array
    {
        $missing = [];
        foreach ($fields as $name => $label) {
            $value = $record[$name] ?? null;
            if (! is_string($value) || trim($value) === '') {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    private function countIncompleteStudents(BaseConnection $db, array $fields): int
    {
        $query = $db->table('students')->groupStart();
        foreach (array_keys($fields) as $fieldName) {
            $column = $db->escapeIdentifiers($fieldName);
            $query->orWhere($fieldName, null)
                ->orWhere(new RawSql('TRIM(' . $column . ") = ''"));
        }

        return $query->groupEnd()->countAllResults();
    }
}
