<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div><h1>Ringkasan sekolah</h1><p class="subheading">Ikhtisar data buku induk yang tercatat.</p></div>
    <a class="button" href="<?= site_url('data/students/new') ?>">+ Tambah siswa</a>
</div>
<div class="cards">
    <section class="card"><div class="metric-label">Total siswa</div><div class="metric"><?= number_format($studentCount, 0, ',', '.') ?></div></section>
    <section class="card"><div class="metric-label">Total guru</div><div class="metric"><?= number_format($teacherCount, 0, ',', '.') ?></div></section>
    <section class="card"><div class="metric-label">Total kelas</div><div class="metric"><?= number_format($classCount, 0, ',', '.') ?></div></section>
</div>
<section class="panel">
    <h2 class="panel-title">Siswa terbaru</h2>
    <?php if ($recentStudents === []): ?>
        <div class="empty">Belum ada data siswa. Mulai dengan menambahkan siswa pertama.</div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>NIS</th><th>Nama siswa</th><th>Kelas</th><th>Ditambahkan</th></tr></thead>
            <tbody><?php foreach ($recentStudents as $student): ?>
                <tr><td><?= esc($student['student_number']) ?></td><td><?= esc($student['full_name']) ?></td><td><?= esc($student['class_name'] ?? 'Belum ditempatkan') ?></td><td><?= esc(date('d-m-Y', strtotime($student['created_at']))) ?></td></tr>
            <?php endforeach ?></tbody>
        </table></div>
    <?php endif ?>
</section>
<?= $this->endSection() ?>
