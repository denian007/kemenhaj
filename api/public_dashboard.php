<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');
header('X-Content-Type-Options: nosniff');

function respondDashboard(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

require_once dirname(__DIR__) . '/config.php';

try {
    $registrationResult = $conn->query("SELECT COUNT(*) AS total,
        COALESCE(SUM(status = 'terverifikasi'), 0) AS terverifikasi,
        COALESCE(SUM(status = 'pending'), 0) AS pending
        FROM ta_pendaftaran");
    if (!$registrationResult) {
        throw new RuntimeException('Ringkasan pendaftaran tidak dapat dibaca.');
    }
    $registration = $registrationResult->fetch_assoc();
    $registrationResult->free();

    $monthStart = date('Y-m-01');
    $nextMonthStart = date('Y-m-01', strtotime('first day of next month'));
    $letterSummary = $conn->prepare("SELECT
        COALESCE(SUM(jenis_surat = 'masuk'), 0) AS masuk,
        COALESCE(SUM(jenis_surat = 'keluar'), 0) AS keluar,
        COALESCE(SUM(status = 'Perlu Tindak Lanjut'), 0) AS tindak_lanjut,
        COALESCE(SUM(status = 'Selesai' AND tanggal_surat >= ? AND tanggal_surat < ?), 0) AS selesai_bulan_ini
        FROM ta_surat");
    if (!$letterSummary) {
        throw new RuntimeException('Ringkasan surat tidak dapat disiapkan.');
    }
    $letterSummary->bind_param('ss', $monthStart, $nextMonthStart);
    if (!$letterSummary->execute()) {
        throw new RuntimeException('Ringkasan surat tidak dapat dibaca.');
    }
    $letterSummary->bind_result($incomingCount, $outgoingCount, $followupCount, $completedThisMonth);
    if (!$letterSummary->fetch()) {
        throw new RuntimeException('Ringkasan surat kosong.');
    }
    $letter = [
        'masuk' => $incomingCount,
        'keluar' => $outgoingCount,
        'tindak_lanjut' => $followupCount,
        'selesai_bulan_ini' => $completedThisMonth
    ];
    $letterSummary->close();

    respondDashboard(200, [
        'status' => true,
        'data' => [
            'jamaah_total' => (int)($registration['total'] ?? 0),
            'jamaah_terverifikasi' => (int)($registration['terverifikasi'] ?? 0),
            'jamaah_pending' => (int)($registration['pending'] ?? 0),
            'surat_masuk' => (int)($letter['masuk'] ?? 0),
            'surat_keluar' => (int)($letter['keluar'] ?? 0),
            'surat_tindak_lanjut' => (int)($letter['tindak_lanjut'] ?? 0),
            'surat_selesai_bulan_ini' => (int)($letter['selesai_bulan_ini'] ?? 0)
        ],
        'updated_at' => date(DATE_ATOM)
    ]);
} catch (Throwable $error) {
    error_log('public_dashboard.php: ' . $error->getMessage());
    respondDashboard(500, ['status' => false, 'message' => 'Ringkasan layanan belum dapat dimuat.']);
} finally {
    if (isset($conn) && $conn instanceof mysqli) $conn->close();
}
