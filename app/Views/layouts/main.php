<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Buku Induk Sekolah') ?> | Buku Induk Sekolah</title>
    <style>
        :root{font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;color:#172033;background:#f4f7fb;font-synthesis:none}
        *{box-sizing:border-box}body{margin:0}a{color:inherit;text-decoration:none}
        .shell{min-height:100vh;display:grid;grid-template-columns:248px 1fr}
        aside{background:#14233f;color:#d8e1f2;padding:25px 16px}
        .brand{font-size:17px;font-weight:750;letter-spacing:-.3px;color:#fff;padding:3px 12px 26px}
        .brand span{display:block;font-size:11px;color:#9fb2d3;font-weight:500;margin-top:5px}
        .nav-label{padding:14px 12px 7px;color:#8296b8;font-size:10px;font-weight:700;letter-spacing:1.1px;text-transform:uppercase}
        .nav-link{display:block;padding:11px 12px;border-radius:8px;font-size:13px;margin:3px 0;color:#cbd6e8}
        .nav-link:hover,.nav-link.active{background:#243959;color:#fff}
        .workspace{min-width:0}.topbar{height:70px;background:#fff;border-bottom:1px solid #e6ebf2;display:flex;align-items:center;justify-content:space-between;padding:0 34px}
        .topbar strong{font-size:14px}.topbar span{font-size:12px;color:#77849a}
        main{max-width:1240px;margin:0 auto;padding:34px}
        .page-heading{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px}
        h1{font-size:24px;letter-spacing:-.6px;margin:0 0 5px}.subheading{font-size:13px;color:#738098;margin:0}
        .button{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:8px;background:#2364d2;color:#fff;padding:10px 15px;font-size:13px;font-weight:650;cursor:pointer}
        .button:hover{background:#174eae}.button.secondary{background:#e9eef6;color:#36445d}.button.danger{background:#fff0ed;color:#bb402f}
        .cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-bottom:22px}
        .card,.panel{background:#fff;border:1px solid #e7ecf3;border-radius:12px;padding:21px}
        .metric-label{font-size:13px;color:#718098}.metric{font-size:30px;font-weight:750;margin-top:9px;letter-spacing:-1px}
        .panel-title{font-size:15px;font-weight:700;margin:0 0 17px}
        .table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;min-width:600px}
        th{text-align:left;color:#758198;font-size:11px;font-weight:650;text-transform:uppercase;letter-spacing:.45px;padding:11px 12px;border-bottom:1px solid #e9edf3}
        td{font-size:13px;padding:14px 12px;border-bottom:1px solid #f0f2f6}
        .list-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:14px}
        .student-search-form{display:flex;align-items:center;gap:9px;flex-wrap:wrap}
        .student-search-form input[type=search]{width:min(310px,55vw)}
        .visually-hidden{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
        .page-size-form{display:flex;align-items:center;gap:10px}.page-size-form label{white-space:nowrap}
        .page-size-form select{width:auto;min-width:82px;padding:8px}
        .students-pagination{margin-top:18px}
        .students-pagination ul{display:flex;justify-content:flex-end;gap:5px;list-style:none;padding:0;margin:0;flex-wrap:wrap}
        .students-pagination a,.students-pagination span{display:block;padding:8px 11px;border:1px solid #e0e6ef;border-radius:7px;font-size:12px;color:#3d4a60}
        .students-pagination li.active span{background:#2364d2;border-color:#2364d2;color:#fff}
        .students-pagination li.disabled span{color:#a4adba;background:#f8f9fb}
        .student-name{color:#2460bd;font-weight:650}.student-name:hover{text-decoration:underline}
        .student-profile{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px 22px}
        .student-profile-item{min-width:0;padding-bottom:11px;border-bottom:1px solid #f0f2f6}
        .student-profile-item dt{font-size:11px;color:#758198;margin-bottom:5px}
        .student-profile-item dd{font-size:13px;margin:0;white-space:pre-wrap;overflow-wrap:anywhere}
        tr:last-child td{border-bottom:0}.actions{display:flex;align-items:center;gap:9px}.actions a{color:#2460bd;font-weight:600}.actions form{margin:0}
        .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px}.field{display:flex;flex-direction:column;gap:7px}.field.full{grid-column:1/-1}
        .form-section{min-width:0;border:1px solid #e7ecf3;border-radius:10px;padding:20px;margin:0 0 18px}
        .form-section legend{padding:0 8px;font-size:14px;font-weight:700;color:#263855}
        label{font-size:12px;font-weight:650;color:#3d4a60}input,select,textarea{width:100%;border:1px solid #dce3ed;border-radius:7px;padding:10px 11px;font:inherit;font-size:13px;color:#172033;background:#fff}
        textarea{min-height:90px;resize:vertical}input:focus,select:focus,textarea:focus{outline:3px solid #dce9ff;border-color:#6b97e4}
        .form-actions{display:flex;gap:10px;margin-top:22px}.alert{border-radius:8px;padding:12px 15px;margin:0 0 18px;font-size:13px}
        .alert.success{background:#eaf8f0;color:#17673b}.alert.error{background:#fff0ed;color:#9f3225}
        .error-list{margin:0 0 16px;padding:12px 15px 12px 32px;background:#fff0ed;color:#9f3225;border-radius:8px;font-size:13px}
        .auth-page{min-height:100vh;display:grid;place-items:center;padding:24px;background:linear-gradient(140deg,#eaf1ff,#f7f9fc 58%)}
        .auth-card{width:min(100%,440px);background:#fff;padding:34px;border:1px solid #e5eaf2;border-radius:14px;box-shadow:0 18px 55px #1c376016}
        .auth-card h1{font-size:22px;margin-bottom:8px}.auth-card .subheading{margin-bottom:24px;line-height:1.6}.auth-card .field{margin-bottom:16px}
        .auth-card .button{width:100%;margin-top:5px}.auth-brand{font-size:12px;color:#2364d2;font-weight:750;letter-spacing:.8px;text-transform:uppercase;margin-bottom:12px}
        .muted{color:#718098}.empty{text-align:center;padding:30px;color:#718098;font-size:13px}
        @media(max-width:850px){.shell{grid-template-columns:1fr}aside{padding:14px;display:flex;align-items:center;gap:6px;overflow:auto}.brand{padding:5px 10px;white-space:nowrap}.brand span,.nav-label{display:none}.nav-link{white-space:nowrap;margin:0;padding:9px}.topbar{height:58px;padding:0 18px}main{padding:24px 18px}}
        @media(max-width:600px){.cards{grid-template-columns:1fr}.form-grid,.student-profile{grid-template-columns:1fr}.field.full{grid-column:auto}.page-heading{align-items:flex-start;flex-direction:column}.topbar span{display:none}.auth-card{padding:25px}}
    </style>
</head>
<body>
<?php if (session()->get('user_id')): ?>
    <?php $path = trim(uri_string(), '/'); ?>
    <div class="shell">
        <aside>
            <a class="brand" href="<?= site_url('dashboard') ?>">Buku Induk Sekolah<span>Sistem administrasi sekolah</span></a>
            <div class="nav-label">Menu utama</div>
            <a class="nav-link <?= $path === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">Ringkasan</a>
            <a class="nav-link <?= str_starts_with($path, 'data/students') ? 'active' : '' ?>" href="<?= site_url('data/students') ?>">Data siswa</a>
            <a class="nav-link <?= str_starts_with($path, 'data/teachers') ? 'active' : '' ?>" href="<?= site_url('data/teachers') ?>">Data guru</a>
            <a class="nav-link <?= str_starts_with($path, 'data/classes') ? 'active' : '' ?>" href="<?= site_url('data/classes') ?>">Data kelas</a>
            <a class="nav-link <?= str_starts_with($path, 'report-cards') ? 'active' : '' ?>" href="<?= site_url('report-cards') ?>">Nilai rapor</a>
            <a class="nav-link <?= $path === 'reports' ? 'active' : '' ?>" href="<?= site_url('reports') ?>">Laporan</a>
            <?php if (session()->get('user_role') === 'admin'): ?>
                <a class="nav-link <?= str_starts_with($path, 'data/users') ? 'active' : '' ?>" href="<?= site_url('data/users') ?>">Pengguna</a>
            <?php endif ?>
        </aside>
        <div class="workspace">
            <header class="topbar">
                <strong><?= esc($title ?? 'Buku Induk Sekolah') ?></strong>
                <span><?= esc(session()->get('user_name')) ?> · <form style="display:inline" method="post" action="<?= site_url('logout') ?>"><?= csrf_field() ?><button type="submit" style="border:0;background:none;color:#2364d2;cursor:pointer;font:inherit">Keluar</button></form></span>
            </header>
            <main>
                <?php if ($message = session()->getFlashdata('message')): ?><div class="alert success"><?= esc($message) ?></div><?php endif ?>
                <?php if ($error = session()->getFlashdata('error')): ?><div class="alert error"><?= esc($error) ?></div><?php endif ?>
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>
<?php else: ?>
    <div class="auth-page">
        <section class="auth-card">
            <div class="auth-brand">Buku Induk Sekolah</div>
            <?= $this->renderSection('content') ?>
        </section>
    </div>
<?php endif ?>
</body>
</html>
