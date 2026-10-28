<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) session_start();

function sendJson(int $code, array $payload): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (empty($_SESSION['user_id'])) {
    sendJson(401, ['status' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
}

require_once dirname(__DIR__) . '/config.php';
try {
    $conn->query("CREATE TABLE IF NOT EXISTS ta_surat (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        jenis_surat ENUM('masuk','keluar') NOT NULL,
        nomor_surat VARCHAR(150) NOT NULL,
        pengirim_penerima VARCHAR(200) NOT NULL,
        tanggal_surat DATE NOT NULL,
        perihal VARCHAR(1000) NOT NULL,
        status VARCHAR(40) NOT NULL DEFAULT 'Perlu Tindak Lanjut',
        catatan TEXT NULL,
        dibuat_oleh VARCHAR(100) NOT NULL,
        dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        diperbarui_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_ta_surat_jenis_tanggal (jenis_surat, tanggal_surat),
        KEY idx_ta_surat_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $conn->query("CREATE TABLE IF NOT EXISTS ta_surat_lampiran (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        public_token CHAR(64) NOT NULL,
        surat_id BIGINT UNSIGNED NOT NULL,
        nama_asli VARCHAR(255) NOT NULL,
        nama_file VARCHAR(255) NOT NULL,
        mime_type VARCHAR(120) NOT NULL,
        ukuran BIGINT UNSIGNED NOT NULL DEFAULT 0,
        dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_ta_surat_lampiran_token (public_token),
        KEY idx_ta_surat_lampiran_surat (surat_id),
        CONSTRAINT fk_ta_surat_lampiran_ta_surat FOREIGN KEY (surat_id) REFERENCES ta_surat(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $tokenColumn = $conn->query("SHOW COLUMNS FROM ta_surat_lampiran LIKE 'public_token'");
    if (!$tokenColumn) throw new RuntimeException('Struktur token lampiran tidak dapat diperiksa.');
    $tokenNeedsNotNull = false;
    if ($tokenColumn->num_rows === 0) {
        if (!$conn->query('ALTER TABLE ta_surat_lampiran ADD COLUMN public_token CHAR(64) NULL AFTER id')) {
            throw new RuntimeException('Kolom token lampiran tidak dapat dibuat.');
        }
        $tokenNeedsNotNull = true;
    } else {
        $tokenColumnInfo = $tokenColumn->fetch_assoc();
        $tokenNeedsNotNull = ($tokenColumnInfo['Null'] ?? 'NO') === 'YES';
    }
    $tokenColumn->free();
    $missingTokens = $conn->query("SELECT id FROM ta_surat_lampiran WHERE public_token IS NULL OR public_token = ''");
    if (!$missingTokens) throw new RuntimeException('Token lampiran tidak dapat diperiksa.');
    $tokenUpdate = $conn->prepare('UPDATE ta_surat_lampiran SET public_token = ? WHERE id = ?');
    while ($missing = $missingTokens->fetch_assoc()) {
        $token = bin2hex(random_bytes(32));
        $attachmentId = (int)$missing['id'];
        $tokenUpdate->bind_param('si', $token, $attachmentId);
        if (!$tokenUpdate->execute()) throw new RuntimeException('Token lampiran lama gagal diperbarui.');
    }
    $tokenUpdate->close();
    $missingTokens->free();
    $tokenIndex = $conn->query("SHOW INDEX FROM ta_surat_lampiran WHERE Key_name = 'uq_ta_surat_lampiran_token'");
    if (!$tokenIndex || $tokenIndex->num_rows === 0) {
        if (!$conn->query('ALTER TABLE ta_surat_lampiran ADD UNIQUE KEY uq_ta_surat_lampiran_token (public_token)')) {
            throw new RuntimeException('Indeks token lampiran tidak dapat dibuat.');
        }
    }
    if ($tokenIndex) $tokenIndex->free();
    if ($tokenNeedsNotNull && !$conn->query('ALTER TABLE ta_surat_lampiran MODIFY public_token CHAR(64) NOT NULL')) {
        throw new RuntimeException('Kolom token lampiran tidak dapat diperbarui.');
    }

    $records = [];
    $result = $conn->query('SELECT id, jenis_surat, nomor_surat, pengirim_penerima, tanggal_surat, perihal, status, catatan FROM ta_surat ORDER BY tanggal_surat DESC, id DESC');
    if (!$result) throw new RuntimeException('Data surat tidak dapat dibaca.');
    while ($row = $result->fetch_assoc()) {
        $row['id'] = (string)$row['id'];
        $row['kind'] = $row['jenis_surat'];
        $row['number'] = $row['nomor_surat'];
        $row['party'] = $row['pengirim_penerima'];
        $row['date'] = $row['tanggal_surat'];
        $row['subject'] = $row['perihal'];
        $row['note'] = $row['catatan'] ?? '';
        $row['serverId'] = $row['id'];
        $row['attachments'] = [];
        $records[$row['id']] = $row;
    }
    $result->free();

    if ($records) {
        $attachments = $conn->query('SELECT id, surat_id, nama_asli, mime_type, public_token FROM ta_surat_lampiran ORDER BY id');
        if ($attachments) {
            while ($file = $attachments->fetch_assoc()) {
                $key = (string)$file['surat_id'];
                if (isset($records[$key])) {
                    $records[$key]['attachments'][] = [
                        'id' => (string)$file['id'],
                        'name' => $file['nama_asli'],
                        'type' => $file['mime_type'],
                        'token' => $file['public_token']
                    ];
                }
            }
            $attachments->free();
        }
    }

    $records = array_values($records);
    $currentMonth = date('Y-m');
    $summary = [
        'total' => count($records),
        'masuk' => 0,
        'keluar' => 0,
        'perlu_tindak_lanjut' => 0,
        'selesai_bulan_ini' => 0,
        'diproses' => 0,
        'ditolak' => 0
    ];
    foreach ($records as $record) {
        if ($record['kind'] === 'masuk') $summary['masuk']++;
        if ($record['kind'] === 'keluar') $summary['keluar']++;
        if ($record['status'] === 'Perlu Tindak Lanjut') $summary['perlu_tindak_lanjut']++;
        if ($record['status'] === 'Diproses') $summary['diproses']++;
        if ($record['status'] === 'Ditolak') $summary['ditolak']++;
        if ($record['status'] === 'Selesai' && substr($record['date'], 0, 7) === $currentMonth) $summary['selesai_bulan_ini']++;
    }

    sendJson(200, ['status' => true, 'data' => $records, 'summary' => $summary]);
} catch (Throwable $error) {
    error_log('get_surat.php: ' . $error->getMessage());
    sendJson(500, ['status' => false, 'message' => 'Gagal memuat data surat dari database.']);
} finally {
    if (isset($conn) && $conn instanceof mysqli) $conn->close();
}
