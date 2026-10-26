<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div><h1>Nama Mata Pelajaran</h1><p class="subheading">Ubah label 11 mapel. Nilai yang sudah tersimpan tetap terhubung ke mapel pada slotnya.</p></div>
    <a class="button secondary" href="<?= site_url('report-cards') ?>">Kembali ke nilai rapor</a>
</div>

<section class="panel">
    <form method="post" action="<?= site_url('report-cards/subjects') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <?php $oldSubjects = old('subjects'); ?>
            <?php foreach ($subjects as $subject): ?>
                <?php $slot = (int) $subject['slot']; ?>
                <div class="field">
                    <label for="subject_<?= $slot ?>">Mapel <?= $slot ?></label>
                    <input id="subject_<?= $slot ?>" name="subjects[<?= $slot ?>]" maxlength="60" required value="<?= esc(is_array($oldSubjects) ? ($oldSubjects[$slot] ?? $subject['name']) : $subject['name']) ?>">
                </div>
            <?php endforeach ?>
        </div>
        <div class="form-actions">
            <button class="button" type="submit">Simpan nama mapel</button>
            <a class="button secondary" href="<?= site_url('report-cards') ?>">Batal</a>
        </div>
    </form>
</section>
<?= $this->endSection() ?>
