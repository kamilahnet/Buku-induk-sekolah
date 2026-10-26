<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div><h1>Nilai Rapor</h1><p class="subheading">Import dan lihat nilai 11 mata pelajaran siswa per tahun ajaran dan semester.</p></div>
    <div class="actions">
        <?php if (session()->get('user_role') === 'admin'): ?><a class="button secondary" href="<?= site_url('report-cards/subjects') ?>">Atur nama mapel</a><?php endif ?>
        <a class="button secondary" href="<?= site_url('report-cards/template') ?>">Unduh template CSV</a>
    </div>
</div>

<?php if ($errors = session()->getFlashdata('import_errors')): ?>
    <ul class="error-list"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
    <?php if (count($errors) === 30): ?><div class="alert error">Hanya 30 kesalahan pertama yang ditampilkan.</div><?php endif ?>
<?php endif ?>

<section class="panel" style="margin-bottom:18px">
    <h2 class="panel-title">Import nilai rapor</h2>
    <p class="subheading" style="line-height:1.7;margin-bottom:14px">
        Gunakan template terbaru agar header mengikuti nama mata pelajaran saat ini. Isi NIPD atau Nama Siswa, tahun ajaran (contoh 2025/2026),
        semester Ganjil/Genap, dan nilai 0–100 untuk seluruh mapel. Jika NIPD kosong atau tidak ditemukan, Nama Siswa digunakan sebagai pencarian.
        Nama yang cocok dengan beberapa siswa harus dilengkapi dengan NIPD. Jika nilai siswa pada periode yang sama sudah ada, nilai akan diperbarui.
        Jika ada baris tidak valid, seluruh import dibatalkan. Maksimal 1.000 siswa per proses.
    </p>
    <form method="post" action="<?= site_url('report-cards/import') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="field full" style="margin-bottom:18px">
            <label for="pasted_data">Tempel tabel dari Excel</label>
            <textarea id="pasted_data" name="pasted_data" rows="6" placeholder="Tempel seluruh tabel Excel di sini, termasuk baris header."></textarea>
        </div>
        <div class="field full">
            <label for="csv_file">Atau unggah file CSV UTF-8</label>
            <input id="csv_file" type="file" name="csv_file" accept=".csv,text/csv">
        </div>
        <div class="form-actions">
            <button class="button" type="submit" onclick="return confirm('Import atau perbarui seluruh nilai rapor pada file?')">Import nilai rapor</button>
        </div>
    </form>
</section>

<section class="panel">
    <div class="list-toolbar">
        <h2 class="panel-title" style="margin:0">Data nilai tersimpan</h2>
        <form method="get" action="<?= site_url('report-cards') ?>" class="student-search-form">
            <label for="report_year">Tahun ajaran</label>
            <select id="report_year" name="year">
                <?php foreach (array_unique(array_column($periods, 'academic_year')) as $periodYear): ?>
                    <option value="<?= esc($periodYear) ?>" <?= $year === $periodYear ? 'selected' : '' ?>><?= esc($periodYear) ?></option>
                <?php endforeach ?>
            </select>
            <label for="report_semester">Semester</label>
            <select id="report_semester" name="semester">
                <option value="Ganjil" <?= $semester === 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                <option value="Genap" <?= $semester === 'Genap' ? 'selected' : '' ?>>Genap</option>
            </select>
            <button class="button" type="submit">Tampilkan</button>
        </form>
    </div>
    <?php if ($records === []): ?>
        <div class="empty">Belum ada nilai untuk periode ini. Import nilai menggunakan template di atas.</div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>NIPD</th><th>Nama siswa</th><?php foreach ($subjects as $subject): ?><th><?= esc($subject['name']) ?></th><?php endforeach ?></tr></thead>
            <tbody><?php foreach ($records as $record): ?>
                <tr>
                    <td><?= esc($record['student_number']) ?></td>
                    <td><?= esc($record['full_name']) ?></td>
                    <?php foreach ($subjects as $subject): ?><td><?= esc(number_format((float) $record['subject_' . $subject['slot']], 2, ',', '.')) ?></td><?php endforeach ?>
                </tr>
            <?php endforeach ?></tbody>
        </table></div>
    <?php endif ?>
</section>
<?= $this->endSection() ?>
