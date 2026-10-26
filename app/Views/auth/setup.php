<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Buat administrator</h1>
<p class="subheading">Buat akun administrator untuk mulai mengelola data sekolah. Langkah ini hanya tersedia sebelum akun pertama dibuat.</p>
<?php if ($errors = session()->getFlashdata('errors')): ?>
    <ul class="error-list"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
<?php endif ?>
<form method="post" action="<?= site_url('setup') ?>">
    <?= csrf_field() ?>
    <div class="field"><label for="name">Nama lengkap</label><input id="name" name="name" value="<?= old('name') ?>" required maxlength="100" autocomplete="name"></div>
    <div class="field"><label for="username">Nama pengguna</label><input id="username" name="username" value="<?= old('username') ?>" required minlength="3" maxlength="50" autocomplete="username"></div>
    <div class="field"><label for="password">Kata sandi (minimal 12 karakter)</label><input id="password" type="password" name="password" required minlength="12" autocomplete="new-password"></div>
    <div class="field"><label for="password_confirmation">Ulangi kata sandi</label><input id="password_confirmation" type="password" name="password_confirmation" required minlength="12" autocomplete="new-password"></div>
    <button class="button" type="submit">Buat akun dan lanjutkan</button>
</form>
<?= $this->endSection() ?>
