<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div><h1>Import Data Siswa</h1><p class="subheading">Unggah CSV atau tempel beberapa baris dari Excel sekaligus.</p></div>
    <a class="button secondary" href="<?= site_url('data/students') ?>">Kembali ke data siswa</a>
</div>

<?php if ($errors = session()->getFlashdata('import_errors')): ?>
    <ul class="error-list"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
    <?php if (count($errors) === 30): ?><div class="alert error">Hanya 30 kesalahan pertama yang ditampilkan.</div><?php endif ?>
<?php endif ?>

<section class="panel" style="margin-bottom:18px">
    <h2 class="panel-title">1. Siapkan data dari Excel</h2>
    <p class="subheading" style="line-height:1.7;margin-bottom:14px">
        Salin baris header di bawah, tempel ke baris pertama Excel, lalu isi data mulai baris berikutnya.
        Saat import, pilih seluruh tabel termasuk header. NIPD wajib diisi dan sebaiknya diformat sebagai teks agar angka nol di depan tidak hilang.
        Rombel harus sudah ada di Data Kelas. Jika nama rombel digunakan di beberapa tahun ajaran, isi juga kolom Tahun Ajaran.
        Kolom yang tidak memiliki data boleh dikosongkan.
    </p>
    <div class="field">
        <label for="template_headers">Header kolom — klik untuk menyalin</label>
        <textarea id="template_headers" readonly rows="2" onclick="this.select()"><?= esc(implode("\t", $headers)) ?></textarea>
    </div>
    <div class="form-actions">
        <button class="button secondary" type="button" id="copy_headers">Salin header</button>
        <a class="button secondary" href="<?= site_url('data/students/import/template') ?>">Unduh template CSV</a>
    </div>
    <p class="subheading" style="margin-top:14px;line-height:1.7">
        Format: JK <strong>L/P</strong>; tanggal <strong>YYYY-MM-DD</strong> atau <strong>DD/MM/YYYY</strong>;
        kolom Penerima KPS/KIP menerima <strong>Ya/Tidak</strong>; Layak PIP dapat berisi keterangan seperti <strong>Siswa Miskin/Rentan Miskin</strong>.
        Status Siswa <strong>Aktif</strong>, <strong>Lulus</strong>, atau <strong>Pindah</strong>.
        Status kosong dianggap Aktif.
        Maksimal 1.000 siswa per import.
    </p>
</section>

<section class="panel">
    <h2 class="panel-title">2. Import data</h2>
    <?php if ($error = session()->getFlashdata('error')): ?><div class="alert error"><?= esc($error) ?></div><?php endif ?>
    <form method="post" action="<?= site_url('data/students/import') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="field full" style="margin-bottom:18px">
            <label for="pasted_data">Tempel tabel dari Excel</label>
            <textarea id="pasted_data" name="pasted_data" rows="8" placeholder="Tempel seluruh tabel Excel di sini, termasuk baris header."></textarea>
        </div>
        <div class="field full">
            <label for="csv_file">Atau unggah file CSV UTF-8</label>
            <input id="csv_file" type="file" name="csv_file" accept=".csv,text/csv">
        </div>
        <div class="form-actions">
            <button class="button" type="submit" onclick="return confirm('Import semua data siswa yang valid? Jika ada kesalahan, seluruh import dibatalkan.')">Import siswa</button>
            <a class="button secondary" href="<?= site_url('data/students') ?>">Batal</a>
        </div>
    </form>
</section>

<script>
    document.getElementById('copy_headers').addEventListener('click', async () => {
        const headers = document.getElementById('template_headers').value;
        await navigator.clipboard.writeText(headers);
        document.getElementById('copy_headers').textContent = 'Header tersalin';
    });
</script>
<?= $this->endSection() ?>
