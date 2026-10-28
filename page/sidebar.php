<?php
$current_page = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$sidebarName = htmlspecialchars((string)($_SESSION['nama_lengkap'] ?? 'Administrator'), ENT_QUOTES, 'UTF-8');
$sidebarUsername = htmlspecialchars((string)($_SESSION['username'] ?? 'admin'), ENT_QUOTES, 'UTF-8');
?>
<aside class="sidebar" id="sidebar">
    <a href="index.php" class="sidebar-brand" aria-label="Kembali ke dashboard Kemenhaj">
        <div class="sidebar-brand-logo">
            <img src="../logo.png" alt="Logo" onerror="this.parentElement.innerHTML='<i class=\'fa-solid fa-kaaba\' style=\'color:#063f2e;font-size:24px\'></i>';" />
        </div>
        <div class="sidebar-brand-text">
            <strong>KEMENHAJ</strong>
            <span>Kota Subulussalam</span>
        </div>
    </a>

    <div class="sidebar-user-mobile">
        <div class="sidebar-user-avatar"><i class="fa-solid fa-user"></i><span></span></div>
        <div class="user-info">
            <strong><?= $sidebarName ?></strong>
            <span><i class="fa-solid fa-shield-halved"></i> @<?= $sidebarUsername ?></span>
        </div>
    </div>

    <ul class="sidebar-menu" aria-label="Navigasi utama">
        <li class="menu-label">MENU UTAMA</li>
        <li><a href="index.php" title="Dashboard" class="<?= $current_page === 'index.php' ? 'active' : '' ?>" <?= $current_page === 'index.php' ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-chart-line"></i> <span>Dashboard</span></a></li>
        <li><a href="pendaftaran.php" title="Pendaftaran Porsi Haji" class="<?= $current_page === 'pendaftaran.php' ? 'active' : '' ?>" <?= $current_page === 'pendaftaran.php' ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-id-card-clip"></i> <span>Pendaftaran Porsi Haji</span></a></li>
        <li><a href="layanan_surat.php" title="Layanan E-Surat" class="<?= in_array($current_page, ['layanan_surat.php', 'surat_masuk.php', 'surat_keluar.php'], true) ? 'active' : '' ?>" <?= in_array($current_page, ['layanan_surat.php', 'surat_masuk.php', 'surat_keluar.php'], true) ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-envelope-open-text"></i> <span>Layanan E-Surat</span></a></li>
        <li><a href="pengawasan_travel.php" title="Pengawasan PPII / Travel" class="<?= $current_page === 'pengawasan_travel.php' ? 'active' : '' ?>" <?= $current_page === 'pengawasan_travel.php' ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-plane-departure"></i> <span>Pengawasan PPII / Travel</span></a></li>

        <li class="menu-label">SISTEM & PENGATURAN</li>
        <li><a href="manajemen_user.php" title="Manajemen Pengguna" class="<?= $current_page === 'manajemen_user.php' ? 'active' : '' ?>" <?= $current_page === 'manajemen_user.php' ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-users-gear"></i> <span>Manajemen Pengguna</span></a></li>
        <li><a href="pengaturan.php" title="Pengaturan Sistem" class="<?= $current_page === 'pengaturan.php' ? 'active' : '' ?>" <?= $current_page === 'pengaturan.php' ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-sliders"></i> <span>Pengaturan Sistem</span></a></li>
    </ul>

    <div class="sidebar-footer">
        <button class="btn-logout-red" id="btnLogout" type="button" title="Keluar dari sistem">
            <i class="fa-solid fa-right-from-bracket"></i> Keluar Sistem
        </button>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
