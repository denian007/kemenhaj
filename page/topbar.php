<?php
$currentTopbarPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$topbarPages = [
    'index.php' => ['Dashboard', 'Ringkasan layanan dan aktivitas administrasi'],
    'pendaftaran.php' => ['Pendaftaran Porsi Haji', 'Kelola data pendaftaran porsi haji jamaah'],
    'layanan_surat.php' => ['Layanan E-Surat', 'Manajemen surat masuk dan surat keluar'],
    'surat_masuk.php' => ['Surat Masuk', 'Arsip dan tindak lanjut surat masuk'],
    'surat_keluar.php' => ['Surat Keluar', 'Arsip dan pengelolaan surat keluar'],
    'jamaah.php' => ['Data Jamaah', 'Informasi dan data jamaah terdaftar'],
    'pengaturan.php' => ['Pengaturan Sistem', 'Kelola preferensi dan konfigurasi sistem']
];
[$page_title, $page_subtitle] = $topbarPages[$currentTopbarPage] ?? ['Panel Administrasi', 'Sistem layanan Kementerian Haji dan Umrah Kota Subulussalam'];
$topbarName = htmlspecialchars((string)($_SESSION['nama_lengkap'] ?? 'Administrator'), ENT_QUOTES, 'UTF-8');
$topbarUsername = htmlspecialchars((string)($_SESSION['username'] ?? 'admin'), ENT_QUOTES, 'UTF-8');
?>
<header class="topbar">
    <div class="topbar-left">
        <button class="btn-toggle-sidebar" id="btnToggleSidebar" type="button" aria-label="Buka atau tutup navigasi">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>
        <div class="topbar-title">
            <h1><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
            <p><?= htmlspecialchars($page_subtitle, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <div class="topbar-actions">
        <div class="digital-clock" aria-label="Waktu saat ini">
            <span class="digital-clock-icon"><i class="fa-regular fa-clock"></i></span>
            <div class="time" id="clockTime">00:00:00</div>
            <div class="date" id="clockDate">Memuat...</div>
        </div>

        <button class="icon-btn" id="btnTheme" type="button" title="Ganti Tema" aria-label="Ganti tema">
            <i class="fa-solid fa-moon"></i>
        </button>

        <button class="icon-btn" id="btnFullscreen" type="button" title="Layar Penuh" aria-label="Layar penuh">
            <i class="fa-solid fa-expand"></i>
        </button>

        <div class="user-profile">
            <div class="user-profile-info d-none d-sm-block">
                <strong><?= $topbarName ?></strong>
                <span>@<?= $topbarUsername ?></span>
            </div>
            <div class="avatar-gold topbar-avatar" aria-hidden="true"><i class="fa-solid fa-user"></i></div>
        </div>
    </div>
</header>
