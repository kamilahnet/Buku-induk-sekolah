<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div>
        <h1>Menyiapkan Nilai Rapor</h1>
        <p class="subheading">Tabel database untuk menyimpan nilai rapor belum tersedia atau belum lengkap.</p>
    </div>
</div>

<section class="panel">
    <h2 class="panel-title">Siapkan tabel nilai rapor</h2>
    <p class="subheading" style="line-height:1.7;margin-bottom:14px">
        Tombol ini hanya menjalankan migration khusus nilai rapor: membuat tabel mapel dan nilai, lalu mengisi nama awal Mapel 1 sampai Mapel 11.
        Nilai siswa dan tabel aplikasi lainnya tidak dihapus atau diubah.
    </p>
    <?php if (session()->get('user_role') === 'admin'): ?>
        <form method="post" action="<?= site_url('report-cards/migrate') ?>" onsubmit="return confirm('Jalankan migration khusus nilai rapor pada database aplikasi? Data yang sudah ada tidak dihapus.')">
            <?= csrf_field() ?>
            <button class="button" type="submit">Jalankan migration nilai rapor</button>
        </form>
    <?php else: ?>
        <div class="alert error" style="margin:0">Hubungi administrator untuk menyiapkan tabel nilai rapor.</div>
    <?php endif ?>
</section>
<?= $this->endSection() ?>
