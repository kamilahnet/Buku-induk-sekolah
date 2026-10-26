<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading"><div><h1>Laporan siswa per kelas</h1><p class="subheading">Jumlah siswa yang tercatat pada setiap kelas dan tahun ajaran.</p></div></div>
<section class="panel">
    <?php if ($classes === []): ?>
        <div class="empty">Belum ada data kelas untuk ditampilkan.</div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Kelas</th><th>Tahun ajaran</th><th>Jumlah siswa</th></tr></thead>
            <tbody><?php foreach ($classes as $class): ?>
                <tr><td><?= esc($class['name']) ?></td><td><?= esc($class['academic_year']) ?></td><td><?= number_format((int) $class['student_count'], 0, ',', '.') ?></td></tr>
            <?php endforeach ?></tbody>
        </table></div>
    <?php endif ?>
</section>
<?= $this->endSection() ?>
