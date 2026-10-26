<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Selamat datang</h1>
<p class="subheading">Masuk untuk mengelola buku induk sekolah.</p>
<?php if ($message = session()->getFlashdata('message')): ?><div class="alert success"><?= esc($message) ?></div><?php endif ?>
<?php if ($error = session()->getFlashdata('error')): ?><div class="alert error"><?= esc($error) ?></div><?php endif ?>
<form method="post" action="<?= site_url('login') ?>">
    <?= csrf_field() ?>
    <div class="field"><label for="username">Nama pengguna</label><input id="username" name="username" value="<?= old('username') ?>" required autocomplete="username"></div>
    <div class="field"><label for="password">Kata sandi</label><input id="password" type="password" name="password" required autocomplete="current-password"></div>
    <button class="button" type="submit">Masuk</button>
</form>
<?= $this->endSection() ?>
