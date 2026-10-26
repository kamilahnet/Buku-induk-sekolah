<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div>
        <h1><?= esc($record['full_name']) ?></h1>
        <p class="subheading">Detail lengkap data siswa.</p>
    </div>
    <div class="actions">
        <a class="button secondary" href="<?= site_url('data/students') ?>">Kembali</a>
        <a class="button" href="<?= site_url('data/students/' . $record['id'] . '/edit') ?>">Ubah data</a>
    </div>
</div>
<?php if ($missingMainFields !== []): ?>
    <div class="alert error" role="status">
        Peringatan: Data Utama siswa belum lengkap. Kolom yang masih kosong:
        <strong><?= esc(implode(', ', $missingMainFields)) ?></strong>.
        <a href="<?= site_url('data/students/' . $record['id'] . '/edit') ?>" style="text-decoration:underline">Lengkapi sekarang</a>.
    </div>
<?php else: ?>
    <div class="alert success" role="status">Data Utama siswa sudah lengkap.</div>
<?php endif ?>
<?php
    $groups = [];
    foreach ($config['fields'] as $name => $field) {
        $groups[$field['group'] ?? 'Informasi'][$name] = $field;
    }
?>
<?php foreach ($groups as $group => $fields): ?>
    <section class="panel" style="margin-bottom:18px">
        <h2 class="panel-title"><?= esc($group) ?></h2>
        <dl class="student-profile">
            <?php foreach ($fields as $name => $field): ?>
                <?php
                    $value = $record[$name] ?? null;
                    if ($field['type'] === 'classes') {
                        $value = $record['class_name'] ?? null;
                        if ($value !== null && $value !== '') {
                            $academicYear = $record['class_academic_year'] ?? '';
                            $value = $academicYear === '' ? $value : $value . ' · ' . $academicYear;
                        }
                    } elseif ($field['type'] === 'select' && $value !== null && isset($field['options'][$value])) {
                        $value = $field['options'][$value];
                    }
                ?>
                <div class="student-profile-item">
                    <dt><?= esc($field['label']) ?></dt>
                    <dd><?= esc((string) ($value === null || $value === '' ? '—' : $value)) ?></dd>
                </div>
            <?php endforeach ?>
        </dl>
    </section>
<?php endforeach ?>
<?= $this->endSection() ?>
