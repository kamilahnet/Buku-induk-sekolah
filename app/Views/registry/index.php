<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div><h1><?= esc($config['title']) ?></h1><p class="subheading">Kelola dan perbarui informasi <?= esc(strtolower($config['title'])) ?>.</p></div>
    <div class="actions">
        <?php if ($resource === 'students'): ?><a class="button secondary" href="<?= site_url('data/students/backup') ?>">Backup data siswa</a><?php endif ?>
        <?php if ($resource === 'students'): ?><a class="button secondary" href="<?= site_url('data/students/import') ?>">Import Excel/CSV</a><?php endif ?>
        <a class="button" href="<?= site_url('data/' . $resource . '/new') ?>">+ Tambah data</a>
    </div>
</div>
<?php if ($resource === 'students' && session()->get('user_role') === 'admin' && $records !== []): ?>
    <section class="panel" style="margin-bottom:18px;border-color:#f2d5d1">
        <h2 class="panel-title">Hapus seluruh data siswa</h2>
        <p class="subheading" style="line-height:1.6;margin-bottom:14px">
            Tindakan ini menghapus semua data siswa secara permanen. Data kelas dan guru tidak ikut terhapus.
            Untuk melanjutkan, ketik <strong>HAPUS SEMUA SISWA</strong>.
        </p>
        <form method="post" action="<?= site_url('data/students/delete-all') ?>" onsubmit="return confirm('PERINGATAN: seluruh data siswa akan dihapus permanen. Lanjutkan?')">
            <?= csrf_field() ?>
            <div class="actions" style="flex-wrap:wrap">
                <input name="confirmation" aria-label="Konfirmasi hapus seluruh siswa" placeholder="Ketik HAPUS SEMUA SISWA" required autocomplete="off" style="max-width:280px">
                <button class="button danger" type="submit">Hapus seluruh data siswa</button>
            </div>
        </form>
    </section>
<?php endif ?>
<?php if ($resource === 'students' && $incompleteStudents > 0): ?>
    <div class="alert error" role="status">
        Peringatan: <?= number_format($incompleteStudents, 0, ',', '.') ?> siswa belum melengkapi Data Utama.
        Periksa tanda <strong>Data belum lengkap</strong> pada daftar, lalu lengkapi melalui menu Ubah.
    </div>
<?php endif ?>
<section class="panel">
    <?php if ($resource === 'students'): ?>
        <div class="list-toolbar">
            <form method="get" action="<?= site_url('data/students') ?>" class="student-search-form">
                <label class="visually-hidden" for="student_search">Cari siswa</label>
                <input id="student_search" type="search" name="search" value="<?= esc($pagination['search']) ?>" placeholder="Cari nama, NIPD, NISN, atau rombel">
                <label for="per_page">Data per halaman</label>
                <select id="per_page" name="per_page">
                    <?php foreach (range(10, 100, 10) as $limit): ?>
                        <option value="<?= $limit ?>" <?= $pagination['perPage'] === $limit ? 'selected' : '' ?>><?= $limit ?></option>
                    <?php endforeach ?>
                </select>
                <button class="button" type="submit">Cari</button>
                <?php if ($pagination['search'] !== ''): ?><a class="button secondary" href="<?= site_url('data/students') ?>">Reset</a><?php endif ?>
            </form>
            <span class="subheading">
                Menampilkan <?= number_format($pagination['firstItem'], 0, ',', '.') ?>–<?= number_format($pagination['lastItem'], 0, ',', '.') ?>
                dari <?= number_format($pagination['total'], 0, ',', '.') ?> siswa
            </span>
        </div>
    <?php endif ?>
    <?php if ($records === []): ?>
        <div class="empty"><?= $resource === 'students' && $pagination['search'] !== '' ? 'Tidak ada siswa yang cocok dengan pencarian.' : 'Belum ada data. Tambahkan data pertama menggunakan tombol di atas.' ?></div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><?php foreach ($config['columns'] as $label): ?><th><?= esc($label) ?></th><?php endforeach ?><th>Aksi</th></tr></thead>
            <tbody><?php foreach ($records as $record): ?>
                <tr>
                    <?php foreach ($config['columns'] as $field => $label): ?>
                        <td><?php
                            $value = $record[$field] ?? '';
                            if ($field === 'status') {
                                $value = ['active' => 'Aktif', 'inactive' => 'Tidak aktif', 'graduated' => 'Lulus', 'transferred' => 'Pindah'][$value] ?? $value;
                            }
                        ?><?php if ($resource === 'students' && $field === 'full_name'): ?>
                            <a class="student-name" href="<?= site_url('data/students/' . $record['id']) ?>"><?= esc((string) ($value ?: '—')) ?></a>
                        <?php else: ?>
                            <?= esc((string) ($value ?: '—')) ?>
                        <?php endif ?></td>
                    <?php endforeach ?>
                    <td><div class="actions">
                        <?php if ($resource === 'students' && $record['missing_main_fields'] !== []): ?>
                            <span class="alert error" style="padding:6px 8px;margin:0">Data belum lengkap</span>
                        <?php endif ?>
                        <a href="<?= site_url('data/' . $resource . '/' . $record['id'] . '/edit') ?>">Ubah</a>
                        <form method="post" action="<?= site_url('data/' . $resource . '/' . $record['id'] . '/delete') ?>" onsubmit="return confirm('Hapus data ini?')">
                            <?= csrf_field() ?><button class="button danger" type="submit">Hapus</button>
                        </form>
                    </div></td>
                </tr>
            <?php endforeach ?></tbody>
        </table></div>
    <?php endif ?>
    <?php if ($resource === 'students' && $pagination['total'] > $pagination['perPage']): ?>
        <nav class="students-pagination" aria-label="Navigasi halaman data siswa">
            <?= $pagination['links'] ?>
        </nav>
    <?php endif ?>
</section>
<?= $this->endSection() ?>
