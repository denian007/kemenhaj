<?php
session_start();
include '../crypt-start.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../logout.php");
    exit;
}
require_once '../config.php';

$dashboardYear = (int)date('Y');
$currentMonth = date('Y-m');
$dashboardStats = [
    'jamaah_total' => 0,
    'jamaah_terverifikasi' => 0,
    'jamaah_pending' => 0,
    'jamaah_ditolak' => 0,
    'surat_masuk' => 0,
    'surat_keluar' => 0,
    'surat_tindak_lanjut' => 0,
    'surat_diproses' => 0,
    'surat_ditolak' => 0,
    'surat_selesai_bulan_ini' => 0
];
$monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$currentMonthLabel = $monthNames[(int)date('n') - 1] . ' ' . date('Y');
$chartRegistrations = array_fill(0, 12, 0);
$chartIncoming = array_fill(0, 12, 0);
$chartOutgoing = array_fill(0, 12, 0);

try {
    $registrationSummary = $conn->query("SELECT COUNT(*) AS total,
        COALESCE(SUM(status = 'terverifikasi'), 0) AS terverifikasi,
        COALESCE(SUM(status = 'pending'), 0) AS pending,
        COALESCE(SUM(status = 'ditolak'), 0) AS ditolak
        FROM ta_pendaftaran");
    if ($registrationSummary && ($summary = $registrationSummary->fetch_assoc())) {
        $dashboardStats['jamaah_total'] = (int)$summary['total'];
        $dashboardStats['jamaah_terverifikasi'] = (int)$summary['terverifikasi'];
        $dashboardStats['jamaah_pending'] = (int)$summary['pending'];
        $dashboardStats['jamaah_ditolak'] = (int)$summary['ditolak'];
        $registrationSummary->free();
    }

    $letterSummary = $conn->query("SELECT
        COALESCE(SUM(jenis_surat = 'masuk'), 0) AS masuk,
        COALESCE(SUM(jenis_surat = 'keluar'), 0) AS keluar,
        COALESCE(SUM(status = 'Perlu Tindak Lanjut'), 0) AS tindak_lanjut,
        COALESCE(SUM(status = 'Diproses'), 0) AS diproses,
        COALESCE(SUM(status = 'Ditolak'), 0) AS ditolak,
        COALESCE(SUM(status = 'Selesai' AND DATE_FORMAT(tanggal_surat, '%Y-%m') = '{$currentMonth}'), 0) AS selesai_bulan_ini
        FROM ta_surat");
    if ($letterSummary && ($summary = $letterSummary->fetch_assoc())) {
        $dashboardStats['surat_masuk'] = (int)$summary['masuk'];
        $dashboardStats['surat_keluar'] = (int)$summary['keluar'];
        $dashboardStats['surat_tindak_lanjut'] = (int)$summary['tindak_lanjut'];
        $dashboardStats['surat_diproses'] = (int)$summary['diproses'];
        $dashboardStats['surat_ditolak'] = (int)$summary['ditolak'];
        $dashboardStats['surat_selesai_bulan_ini'] = (int)$summary['selesai_bulan_ini'];
        $letterSummary->free();
    }

    $registrationTrend = $conn->prepare('SELECT MONTH(tgl_daftar) AS bulan, COUNT(*) AS total FROM ta_pendaftaran WHERE YEAR(tgl_daftar) = ? GROUP BY MONTH(tgl_daftar)');
    if ($registrationTrend) {
        $registrationTrend->bind_param('i', $dashboardYear);
        if ($registrationTrend->execute()) {
            $registrationTrend->bind_result($monthNumber, $monthTotal);
            while ($registrationTrend->fetch()) {
                $monthIndex = (int)$monthNumber - 1;
                if ($monthIndex >= 0 && $monthIndex < 12) {
                    $chartRegistrations[$monthIndex] = (int)$monthTotal;
                }
            }
        }
        $registrationTrend->close();
    }

    $letterTrend = $conn->prepare("SELECT MONTH(tanggal_surat) AS bulan, jenis_surat, COUNT(*) AS total FROM ta_surat WHERE YEAR(tanggal_surat) = ? GROUP BY MONTH(tanggal_surat), jenis_surat");
    if ($letterTrend) {
        $letterTrend->bind_param('i', $dashboardYear);
        if ($letterTrend->execute()) {
            $letterTrend->bind_result($monthNumber, $letterType, $monthTotal);
            while ($letterTrend->fetch()) {
                $monthIndex = (int)$monthNumber - 1;
                if ($monthIndex < 0 || $monthIndex >= 12) continue;
                if ($letterType === 'masuk') $chartIncoming[$monthIndex] = (int)$monthTotal;
                if ($letterType === 'keluar') $chartOutgoing[$monthIndex] = (int)$monthTotal;
            }
        }
        $letterTrend->close();
    }
} catch (Throwable $error) {
    error_log('Dashboard index.php: ' . $error->getMessage());
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard — Kemenhaj</title>
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <link rel="shortcut icon" href="../logo.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Path CSS diperbaiki ke luar folder page -->
    <link rel="stylesheet" href="../assets/style.css?v=<?= filemtime(__DIR__ . '/../assets/style.css') ?>" />
    <style>
        .dashboard-page { padding-top:27px; padding-bottom:30px; background:var(--bg-body); }
        .dashboard-hero { position:relative; isolation:isolate; display:flex; align-items:center; justify-content:space-between; gap:24px; min-height:210px; margin-bottom:27px; padding:30px 34px; overflow:hidden; border:1px solid rgba(255,255,255,.12); border-radius:22px; color:#fff; background:radial-gradient(circle at 82% 15%,rgba(55,190,142,.24),transparent 30%),linear-gradient(125deg,#064c39 0%,#087653 58%,#0b8c68 100%); box-shadow:0 18px 40px rgba(6,76,57,.16); }
        .dashboard-hero-copy,.dashboard-hero-actions { position:relative; z-index:2; }
        .dashboard-hero-copy { max-width:720px; }
        .dashboard-eyebrow { display:inline-flex; align-items:center; gap:8px; margin-bottom:13px; color:#b9f2d9; font-size:10px; font-weight:800; letter-spacing:1.2px; }
        .dashboard-hero h2 { margin:0 0 9px; color:#fff; font-size:clamp(23px,2.4vw,31px); font-weight:800; letter-spacing:-.8px; }
        .dashboard-hero h2 span { color:#f4d46a; }
        .dashboard-hero p { max-width:650px; margin:0; color:rgba(255,255,255,.77); font-size:12px; line-height:1.7; }
        .dashboard-hero-meta { display:flex; flex-wrap:wrap; gap:17px; margin-top:18px; color:rgba(255,255,255,.75); font-size:10px; font-weight:600; }
        .dashboard-hero-meta span { display:inline-flex; align-items:center; gap:7px; }
        .dashboard-hero-meta i { color:#a9edce; }
        .dashboard-hero-actions { display:flex; flex-direction:column; gap:9px; min-width:155px; }
        .dashboard-hero-actions a { display:flex; align-items:center; justify-content:center; gap:8px; min-height:40px; padding:0 15px; border-radius:10px; font-size:11px; font-weight:800; text-decoration:none; transition:.2s ease; }
        .btn-dashboard-primary { color:#075b43; background:#f4d46a; box-shadow:0 8px 18px rgba(0,0,0,.12); }
        .btn-dashboard-primary:hover { color:#064c39; background:#ffe28a; transform:translateY(-2px); }
        .btn-dashboard-secondary { color:#fff; border:1px solid rgba(255,255,255,.35); background:rgba(255,255,255,.08); }
        .btn-dashboard-secondary:hover { color:#fff; background:rgba(255,255,255,.16); }
        .dashboard-hero-decoration { position:absolute; z-index:1; right:20%; bottom:-90px; color:rgba(255,255,255,.045); font-size:250px; transform:rotate(-9deg); pointer-events:none; }
        .dashboard-section-heading { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin:0 0 14px; }
        .dashboard-section-heading > div > span { color:#0b805d; font-size:9px; font-weight:800; letter-spacing:1.1px; }
        .dashboard-section-heading h2 { margin:3px 0 3px; color:var(--text-main); font-size:16px; font-weight:800; }
        .dashboard-section-heading p { margin:0; color:var(--text-muted); font-size:10px; }
        .dashboard-live { display:inline-flex; align-items:center; gap:7px; padding:7px 10px; border:1px solid #cfecdd; border-radius:30px; color:#087653; background:#f0faf5; font-size:9px; font-weight:800; white-space:nowrap; }
        .dashboard-live i { width:7px; height:7px; border-radius:50%; background:#10a772; box-shadow:0 0 0 3px rgba(16,167,114,.12); }
        .dashboard-stat-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:13px; margin-bottom:24px; }
        .dashboard-stat-card { position:relative; min-width:0; min-height:123px; padding:16px 17px 14px; overflow:hidden; border:1px solid var(--border-color); border-radius:15px; background:var(--bg-card); box-shadow:0 5px 18px rgba(20,55,42,.035); transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease; }
        .dashboard-stat-card:hover { transform:translateY(-3px); border-color:#cce5d8; box-shadow:0 12px 25px rgba(20,55,42,.08); }
        .dashboard-stat-card:after { position:absolute; right:-24px; bottom:-39px; width:100px; height:100px; border-radius:50%; background:var(--stat-tint); content:""; opacity:.55; }
        .dashboard-stat-top { position:relative; z-index:1; display:flex; align-items:center; justify-content:space-between; gap:10px; color:var(--text-muted); font-size:10px; font-weight:700; }
        .dashboard-stat-icon { display:grid; place-items:center; flex:0 0 34px; width:34px; height:34px; border-radius:11px; color:var(--stat-color); background:var(--stat-tint); font-size:13px; }
        .dashboard-stat-card strong { position:relative; z-index:1; display:block; margin-top:8px; color:var(--text-main); font-size:25px; font-weight:800; line-height:1.05; letter-spacing:-.6px; }
        .dashboard-stat-card small { position:relative; z-index:1; display:block; margin-top:6px; color:var(--text-muted); font-size:9px; }
        .stat-jamaah { --stat-color:#087653; --stat-tint:#e1f5eb; }
        .stat-verified { --stat-color:#168957; --stat-tint:#e2f7e9; }
        .stat-pending { --stat-color:#b77908; --stat-tint:#fff3d7; }
        .stat-rejected { --stat-color:#cf4545; --stat-tint:#ffebeb; }
        .stat-incoming { --stat-color:#2563b8; --stat-tint:#e8f1ff; }
        .stat-outgoing { --stat-color:#7654bd; --stat-tint:#f0eaff; }
        .stat-followup { --stat-color:#c07811; --stat-tint:#fff2df; }
        .stat-processing { --stat-color:#2c6eb4; --stat-tint:#e8f1ff; }
        .stat-letter-rejected { --stat-color:#cf4545; --stat-tint:#ffebeb; }
        .stat-complete { --stat-color:#087d72; --stat-tint:#e1f6f2; }
        .dashboard-page .panel { border:1px solid var(--border-color); border-radius:16px; background:var(--bg-card); box-shadow:0 8px 25px rgba(20,55,42,.045); }
        .dashboard-page .panel-head { padding:17px 19px; border-bottom:1px solid var(--border-color); }
        .dashboard-page .panel-head h3 { margin:0; color:var(--text-main); font-size:13px; font-weight:800; }
        .dashboard-page .panel-body { padding:16px 18px; }
        .dashboard-page .list-pintar { display:grid; gap:9px; }
        .dashboard-page .item-pintar { min-height:48px; padding:11px 12px; border:1px solid var(--border-color); border-radius:11px; background:var(--bg-card); color:var(--text-main); font-size:10px; font-weight:700; }
        .dashboard-page .item-pintar:hover { border-color:#cce5d8; background:#f5fbf7; transform:translateX(2px); }
        [data-theme="dark"] .dashboard-page .item-pintar:hover { background:rgba(255,255,255,.04); }
        @media(max-width:1200px) { .dashboard-stat-grid { grid-template-columns:repeat(4,minmax(0,1fr)); } }
        @media(max-width:900px) { .dashboard-stat-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } .dashboard-hero { align-items:flex-start; } }
        @media(max-width:600px) { .dashboard-page { padding:16px 14px 24px; } .dashboard-hero { flex-direction:column; gap:18px; min-height:0; padding:23px 20px; border-radius:17px; } .dashboard-hero-actions { width:100%; flex-direction:row; } .dashboard-hero-actions a { flex:1; padding:0 9px; font-size:10px; } .dashboard-hero-decoration { right:-16px; bottom:-50px; font-size:165px; } .dashboard-hero-meta { gap:8px 14px; } .dashboard-stat-grid { gap:9px; } .dashboard-stat-card { min-height:112px; padding:12px; } .dashboard-stat-top { font-size:9px; } .dashboard-stat-card strong { font-size:22px; } .dashboard-section-heading { align-items:center; } }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content" id="mainContent">
        <?php include 'topbar.php'; ?>

        <div class="content-wrapper dashboard-page">
            <section class="dashboard-hero">
                <div class="dashboard-hero-copy">
                    <span class="dashboard-eyebrow"><i class="fa-solid fa-chart-pie"></i> RINGKASAN SISTEM</span>
                    <h2>Selamat datang, <span><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Administrator', ENT_QUOTES, 'UTF-8') ?></span></h2>
                    <p>Pantau pendaftaran jamaah dan tata kelola surat dalam satu dashboard. Angka dan grafik diperbarui langsung dari database.</p>
                    <div class="dashboard-hero-meta"><span><i class="fa-solid fa-calendar-day"></i> Data tahun <?= $dashboardYear ?></span><span><i class="fa-solid fa-rotate"></i> Diperbarui <?= date('d M Y, H:i') ?></span></div>
                </div>
                <div class="dashboard-hero-actions">
                    <a href="layanan_surat.php" class="btn-dashboard-primary"><i class="fa-solid fa-envelope-open-text"></i> Kelola Surat</a>
                    <a href="pendaftaran.php" class="btn-dashboard-secondary"><i class="fa-solid fa-users"></i> Data Jamaah</a>
                </div>
                <div class="dashboard-hero-decoration"><i class="fa-solid fa-mosque"></i></div>
            </section>

            <section class="dashboard-section-heading">
                <div><span>OVERVIEW</span><h2>Indikator Layanan</h2><p>Rekapitulasi sesuai data yang tersimpan.</p></div>
                <span class="dashboard-live"><i></i> Data langsung</span>
            </section>

            <div class="dashboard-stat-grid">
                <article class="dashboard-stat-card stat-jamaah">
                    <div class="dashboard-stat-top"><span>Total Jamaah</span><span class="dashboard-stat-icon"><i class="fa-solid fa-users"></i></span></div>
                    <strong><?= number_format($dashboardStats['jamaah_total'], 0, ',', '.') ?></strong><small>Semua data pendaftaran</small>
                </article>
                <article class="dashboard-stat-card stat-verified">
                    <div class="dashboard-stat-top"><span>Terverifikasi</span><span class="dashboard-stat-icon"><i class="fa-solid fa-circle-check"></i></span></div>
                    <strong><?= number_format($dashboardStats['jamaah_terverifikasi'], 0, ',', '.') ?></strong><small>Status pendaftaran jamaah</small>
                </article>
                <article class="dashboard-stat-card stat-pending">
                    <div class="dashboard-stat-top"><span>Menunggu Verifikasi</span><span class="dashboard-stat-icon"><i class="fa-regular fa-clock"></i></span></div>
                    <strong><?= number_format($dashboardStats['jamaah_pending'], 0, ',', '.') ?></strong><small>Perlu pemeriksaan pendaftaran</small>
                </article>
                <article class="dashboard-stat-card stat-rejected">
                    <div class="dashboard-stat-top"><span>Pendaftaran Ditolak</span><span class="dashboard-stat-icon"><i class="fa-solid fa-circle-xmark"></i></span></div>
                    <strong><?= number_format($dashboardStats['jamaah_ditolak'], 0, ',', '.') ?></strong><small>Status ditolak</small>
                </article>
                <article class="dashboard-stat-card stat-incoming">
                    <div class="dashboard-stat-top"><span>Surat Masuk</span><span class="dashboard-stat-icon"><i class="fa-solid fa-inbox"></i></span></div>
                    <strong><?= number_format($dashboardStats['surat_masuk'], 0, ',', '.') ?></strong><small>Total surat masuk</small>
                </article>
                <article class="dashboard-stat-card stat-outgoing">
                    <div class="dashboard-stat-top"><span>Surat Keluar</span><span class="dashboard-stat-icon"><i class="fa-solid fa-paper-plane"></i></span></div>
                    <strong><?= number_format($dashboardStats['surat_keluar'], 0, ',', '.') ?></strong><small>Total surat keluar</small>
                </article>
                <article class="dashboard-stat-card stat-followup">
                    <div class="dashboard-stat-top"><span>Perlu Tindak Lanjut</span><span class="dashboard-stat-icon"><i class="fa-solid fa-list-check"></i></span></div>
                    <strong><?= number_format($dashboardStats['surat_tindak_lanjut'], 0, ',', '.') ?></strong><small>Surat berstatus perlu tindak lanjut</small>
                </article>
                <article class="dashboard-stat-card stat-processing">
                    <div class="dashboard-stat-top"><span>Surat Diproses</span><span class="dashboard-stat-icon"><i class="fa-solid fa-arrows-rotate"></i></span></div>
                    <strong><?= number_format($dashboardStats['surat_diproses'], 0, ',', '.') ?></strong><small>Surat dalam proses penanganan</small>
                </article>
                <article class="dashboard-stat-card stat-letter-rejected">
                    <div class="dashboard-stat-top"><span>Surat Ditolak</span><span class="dashboard-stat-icon"><i class="fa-solid fa-circle-exclamation"></i></span></div>
                    <strong><?= number_format($dashboardStats['surat_ditolak'], 0, ',', '.') ?></strong><small>Surat dengan status ditolak</small>
                </article>
                <article class="dashboard-stat-card stat-complete">
                    <div class="dashboard-stat-top"><span>Selesai Bulan Ini</span><span class="dashboard-stat-icon"><i class="fa-solid fa-circle-check"></i></span></div>
                    <strong><?= number_format($dashboardStats['surat_selesai_bulan_ini'], 0, ',', '.') ?></strong><small>Surat selesai bulan <?= htmlspecialchars($currentMonthLabel, ENT_QUOTES, 'UTF-8') ?></small>
                </article>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="panel h-100">
                        <div class="panel-head">
                            <div>
                                <h3>Aktivitas Administrasi</h3>
                                <p style="font-size:12px; color:var(--text-muted); margin:4px 0 0 0;">Pendaftaran jamaah dan surat berdasarkan tanggal database, <?= $dashboardYear ?></p>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div id="chartPendaftaran"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="panel h-100">
                        <div class="panel-head">
                            <div>
                                <h3>Akses Pintar Administrasi</h3>
                                <p style="font-size:12px; color:var(--text-muted); margin:4px 0 0 0;">Fitur cepat kelola berkas dan data</p>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="list-pintar">
                                <a href="layanan_surat.php" class="item-pintar">
                                    <span><i class="fa-solid fa-envelope-open-text text-success me-2"></i> Manajemen Surat Masuk & Keluar</span>
                                    <i class="fa-solid fa-chevron-right text-muted"></i>
                                </a>
                                <a href="pendaftaran.php" class="item-pintar">
                                    <span><i class="fa-solid fa-id-card text-primary me-2"></i> Pendaftaran Porsi Haji Jamaah</span>
                                    <i class="fa-solid fa-chevron-right text-muted"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php include 'footer.php'; ?>
    </main>

    <script>
        let chartInstance = null;
        const dashboardChartData = <?= json_encode([
            'months' => $monthLabels,
            'registrations' => $chartRegistrations,
            'incoming' => $chartIncoming,
            'outgoing' => $chartOutgoing
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        function renderCharts(theme) {
            const isDark = theme === 'dark';
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

            if (chartInstance) chartInstance.destroy();

            const options = {
                series: [
                    {name:'Pendaftaran Jamaah', data:dashboardChartData.registrations},
                    {name:'Surat Masuk', data:dashboardChartData.incoming},
                    {name:'Surat Keluar', data:dashboardChartData.outgoing}
                ],
                chart: {
                    type: 'area',
                    height: 320,
                    parentHeightOffset: 0,
                    zoom: {enabled:false},
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Plus Jakarta Sans'
                },
                colors: ['#0b805d', '#3b82c4', '#d2a326'],
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        opacityFrom: 0.22,
                        opacityTo: 0.015,
                        stops:[0,90,100]
                    }
                },
                markers:{size:0,hover:{size:5}},
                xaxis: {
                    categories: dashboardChartData.months,
                    axisBorder:{show:false},
                    axisTicks:{show:false},
                    labels: {
                        style: {
                            colors: textColor
                        }
                    }
                },
                yaxis: {
                    min:0,
                    forceNiceScale:true,
                    labels: {
                        style: {
                            colors: textColor,
                            fontFamily:'Plus Jakarta Sans'
                        }
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4,
                    padding:{left:8,right:12}
                },
                tooltip:{shared:true,intersect:false,theme:isDark?'dark':'light',y:{formatter:function(value){return new Intl.NumberFormat('id-ID').format(value)+' data';}}},
                dataLabels: {
                    enabled: false
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize:'10px',
                    markers:{width:8,height:8,radius:8},
                    labels: {
                        colors: textColor
                    }
                },
                noData:{text:'Belum ada aktivitas pada tahun ini',align:'center',verticalAlign:'middle',style:{color:textColor,fontSize:'12px',fontFamily:'Plus Jakarta Sans'}},
                theme: {
                    mode: isDark ? 'dark' : 'light'
                }
            };
            chartInstance = new ApexCharts(document.querySelector("#chartPendaftaran"), options);
            chartInstance.render();
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderCharts(localStorage.getItem('theme') || 'light');
        });
    </script>
</body>

</html>
<?php include '../crypt-start.php'; ?>
