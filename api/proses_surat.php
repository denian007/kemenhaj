<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function jsonResponse(int $httpCode, array $payload): void
{
    http_response_code($httpCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    jsonResponse(405, ['status' => false, 'success' => false, 'message' => 'Metode request tidak didukung.']);
}

if (empty($_SESSION['user_id'])) {
    jsonResponse(401, ['status' => false, 'success' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
}

$configPath = dirname(__DIR__) . '/config.php';
if (!is_file($configPath)) {
    jsonResponse(500, ['status' => false, 'success' => false, 'message' => 'Konfigurasi database tidak ditemukan.']);
}
require_once $configPath;

if (!isset($conn) || !($conn instanceof mysqli)) {
    jsonResponse(500, ['status' => false, 'success' => false, 'message' => 'Koneksi database tidak tersedia.']);
}

$uploadDirectory = dirname(__DIR__) . '/uploads/surat';
$storedFiles = [];
$removedFilePaths = [];
$transactionStarted = false;

try {
    // Endpoint ini membuat tabel surat saat pertama kali digunakan.
    $schema = "CREATE TABLE IF NOT EXISTS ta_surat (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    if (!$conn->query($schema)) {
        throw new RuntimeException('Gagal menyiapkan tabel surat.');
    }

    $attachmentSchema = "CREATE TABLE IF NOT EXISTS ta_surat_lampiran (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    if (!$conn->query($attachmentSchema)) {
        throw new RuntimeException('Gagal menyiapkan tabel lampiran surat.');
    }

    // Migrasi aman untuk tabel lampiran yang dibuat sebelum tautan publik memakai token.
    $tokenColumn = $conn->query("SHOW COLUMNS FROM ta_surat_lampiran LIKE 'public_token'");
    if (!$tokenColumn) throw new RuntimeException('Struktur token lampiran tidak dapat diperiksa.');
    $tokenNeedsNotNull = false;
    if ($tokenColumn->num_rows === 0) {
        if (!$conn->query('ALTER TABLE ta_surat_lampiran ADD COLUMN public_token CHAR(64) NULL AFTER id')) {
            throw new RuntimeException('Kolom token tautan lampiran tidak dapat dibuat.');
        }
        $tokenNeedsNotNull = true;
    } else {
        $tokenColumnInfo = $tokenColumn->fetch_assoc();
        $tokenNeedsNotNull = ($tokenColumnInfo['Null'] ?? 'NO') === 'YES';
    }
    $tokenColumn->free();
    $missingTokens = $conn->query("SELECT id FROM ta_surat_lampiran WHERE public_token IS NULL OR public_token = ''");
    if (!$missingTokens) throw new RuntimeException('Token lampiran lama tidak dapat diperiksa.');
    $tokenUpdate = $conn->prepare('UPDATE ta_surat_lampiran SET public_token = ? WHERE id = ?');
    while ($missing = $missingTokens->fetch_assoc()) {
        $publicToken = bin2hex(random_bytes(32));
        $attachmentId = (int)$missing['id'];
        $tokenUpdate->bind_param('si', $publicToken, $attachmentId);
        if (!$tokenUpdate->execute()) throw new RuntimeException('Token untuk lampiran lama gagal dibuat.');
    }
    $tokenUpdate->close();
    $missingTokens->free();
    $tokenIndex = $conn->query("SHOW INDEX FROM ta_surat_lampiran WHERE Key_name = 'uq_ta_surat_lampiran_token'");
    if (!$tokenIndex || $tokenIndex->num_rows === 0) {
        if (!$conn->query('ALTER TABLE ta_surat_lampiran ADD UNIQUE KEY uq_ta_surat_lampiran_token (public_token)')) {
            throw new RuntimeException('Indeks token tautan lampiran tidak dapat dibuat.');
        }
    }
    if ($tokenIndex) $tokenIndex->free();
    if ($tokenNeedsNotNull && !$conn->query('ALTER TABLE ta_surat_lampiran MODIFY public_token CHAR(64) NOT NULL')) {
        throw new RuntimeException('Kolom token tautan lampiran tidak dapat divalidasi.');
    }

    $action = trim((string)($_POST['aksi'] ?? 'tambah'));
    $recordId = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    $kind = trim((string)($_POST['jenis_surat'] ?? ''));
    $number = trim((string)($_POST['nomor_surat'] ?? ''));
    $party = trim((string)($_POST['pengirim_penerima'] ?? ''));
    $date = trim((string)($_POST['tanggal_surat'] ?? ''));
    $subject = trim((string)($_POST['perihal'] ?? ''));
    $status = trim((string)($_POST['status'] ?? 'Perlu Tindak Lanjut'));
    $note = trim((string)($_POST['catatan'] ?? ''));

    if (!in_array($action, ['tambah', 'ubah', 'hapus'], true)) {
        jsonResponse(400, ['status' => false, 'success' => false, 'message' => 'Aksi surat tidak valid.']);
    }
    if (in_array($action, ['ubah', 'hapus'], true) && (!$recordId || $recordId < 1)) {
        jsonResponse(422, ['status' => false, 'success' => false, 'message' => 'ID surat tidak valid.']);
    }
    if ($action === 'hapus') {
        $filesToRemove = [];
        $filesQuery = $conn->prepare('SELECT nama_file FROM ta_surat_lampiran WHERE surat_id = ?');
        $filesQuery->bind_param('i', $recordId);
        $filesQuery->execute();
        $filesQuery->bind_result($storedFileName);
        while ($filesQuery->fetch()) $filesToRemove[] = basename($storedFileName);
        $filesQuery->close();

        $delete = $conn->prepare('DELETE FROM ta_surat WHERE id = ?');
        $delete->bind_param('i', $recordId);
        $delete->execute();
        $wasDeleted = $delete->affected_rows > 0;
        $delete->close();
        if (!$wasDeleted) {
            jsonResponse(404, ['status' => false, 'success' => false, 'message' => 'Data surat tidak ditemukan.']);
        }
        foreach ($filesToRemove as $fileName) {
            $path = $uploadDirectory . '/' . $fileName;
            if (is_file($path)) @unlink($path);
        }
        jsonResponse(200, ['status' => true, 'success' => true, 'message' => 'Surat berhasil dihapus.', 'data' => ['id' => $recordId]]);
    }
    if (!in_array($kind, ['masuk', 'keluar'], true) || $number === '' || $party === '' || $subject === '') {
        jsonResponse(422, ['status' => false, 'success' => false, 'message' => 'Jenis, nomor, pengirim/penerima, dan perihal surat wajib diisi.']);
    }
    $dateObject = DateTime::createFromFormat('!Y-m-d', $date);
    if (!$dateObject || $dateObject->format('Y-m-d') !== $date) {
        jsonResponse(422, ['status' => false, 'success' => false, 'message' => 'Tanggal surat tidak valid.']);
    }
    $allowedStatuses = ['Perlu Tindak Lanjut', 'Diproses', 'Selesai', 'Ditolak'];
    if (!in_array($status, $allowedStatuses, true)) {
        jsonResponse(422, ['status' => false, 'success' => false, 'message' => 'Status surat tidak valid.']);
    }
    if (mb_strlen($number) > 150 || mb_strlen($party) > 200 || mb_strlen($subject) > 1000) {
        jsonResponse(422, ['status' => false, 'success' => false, 'message' => 'Salah satu isian melewati batas panjang yang diizinkan.']);
    }

    if ($action === 'ubah') {
        $check = $conn->prepare('SELECT id FROM ta_surat WHERE id = ? LIMIT 1');
        $check->bind_param('i', $recordId);
        $check->execute();
        $check->store_result();
        $exists = $check->num_rows > 0;
        $check->close();
        if (!$exists) {
            jsonResponse(404, ['status' => false, 'success' => false, 'message' => 'Data surat yang akan diperbarui tidak ditemukan.']);
        }
    }

    $newUploads = [];
    if (isset($_FILES['lampiran'])) {
        $upload = $_FILES['lampiran'];
        if (is_array($upload['name'])) {
            foreach ($upload['name'] as $index => $name) {
                $newUploads[] = [
                    'name' => $name,
                    'type' => $upload['type'][$index] ?? '',
                    'tmp_name' => $upload['tmp_name'][$index] ?? '',
                    'error' => $upload['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                    'size' => $upload['size'][$index] ?? 0
                ];
            }
        } else {
            $newUploads[] = $upload;
        }
    }

    $newUploads = array_values(array_filter($newUploads, static function ($file) {
        return (int)$file['error'] !== UPLOAD_ERR_NO_FILE;
    }));
    if (count($newUploads) > 8) {
        jsonResponse(422, ['status' => false, 'success' => false, 'message' => 'Maksimal 8 lampiran dapat diunggah sekaligus.']);
    }

    $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg'];
    foreach ($newUploads as $file) {
        if ((int)$file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Salah satu lampiran gagal diunggah. Periksa ukuran file dan coba lagi.');
        }
        $extension = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions, true)) {
            throw new RuntimeException('Format lampiran tidak didukung. Gunakan PDF, Word, Excel, PNG, atau JPG.');
        }
        if ((int)$file['size'] > 15 * 1024 * 1024) {
            throw new RuntimeException('Ukuran setiap lampiran maksimal 15 MB.');
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Berkas lampiran tidak lolos pemeriksaan upload.');
        }
    }

    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0750, true) && !is_dir($uploadDirectory)) {
        throw new RuntimeException('Folder penyimpanan lampiran tidak dapat dibuat.');
    }
    $htaccess = $uploadDirectory . '/.htaccess';
    if (!is_file($htaccess)) {
        @file_put_contents($htaccess, "Options -Indexes\n<FilesMatch \".*\">\nRequire all denied\n</FilesMatch>\n");
    }

    $conn->begin_transaction();
    $transactionStarted = true;
    if ($action === 'tambah') {
        $stmt = $conn->prepare('INSERT INTO ta_surat (jenis_surat, nomor_surat, pengirim_penerima, tanggal_surat, perihal, status, catatan, dibuat_oleh) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $userId = (string)$_SESSION['user_id'];
        $stmt->bind_param('ssssssss', $kind, $number, $party, $date, $subject, $status, $note, $userId);
    } else {
        $stmt = $conn->prepare('UPDATE ta_surat SET jenis_surat = ?, nomor_surat = ?, pengirim_penerima = ?, tanggal_surat = ?, perihal = ?, status = ?, catatan = ? WHERE id = ?');
        $stmt->bind_param('sssssssi', $kind, $number, $party, $date, $subject, $status, $note, $recordId);
    }
    if (!$stmt || !$stmt->execute()) {
        throw new RuntimeException('Data surat gagal disimpan ke database.');
    }
    if ($action === 'tambah') {
        $recordId = (int)$stmt->insert_id;
    }
    $stmt->close();

    $removedNames = $_POST['hapus_lampiran'] ?? [];
    if (!is_array($removedNames)) $removedNames = [$removedNames];
    $removedNames = array_values(array_filter(array_map(static function ($name) {
        return basename(trim((string)$name));
    }, $removedNames)));

    $attachmentCount = 0;
    $countStmt = $conn->prepare('SELECT COUNT(*) FROM ta_surat_lampiran WHERE surat_id = ?');
    $countStmt->bind_param('i', $recordId);
    $countStmt->execute();
    $countStmt->bind_result($attachmentCount);
    $countStmt->fetch();
    $countStmt->close();
    $remainingCount = max(0, (int)$attachmentCount - count(array_unique($removedNames)));
    if ($remainingCount + count($newUploads) > 8) {
        throw new RuntimeException('Maksimal 8 lampiran dapat disimpan untuk setiap surat.');
    }

    foreach ($removedNames as $removedName) {
        $find = $conn->prepare('SELECT nama_file FROM ta_surat_lampiran WHERE surat_id = ? AND nama_asli = ? LIMIT 1');
        $find->bind_param('is', $recordId, $removedName);
        $find->execute();
        $find->bind_result($storedName);
        if ($find->fetch()) {
            $find->close();
            $delete = $conn->prepare('DELETE FROM ta_surat_lampiran WHERE surat_id = ? AND nama_asli = ?');
            $delete->bind_param('is', $recordId, $removedName);
            $delete->execute();
            $delete->close();
            $oldPath = $uploadDirectory . '/' . basename($storedName);
            if (is_file($oldPath)) $removedFilePaths[] = $oldPath;
        } else {
            $find->close();
        }
    }

    $savedNames = [];
    $savedPublicTokens = [];
    foreach ($newUploads as $file) {
        $originalName = basename((string)$file['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetPath = $uploadDirectory . '/' . $storedName;
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Lampiran gagal dipindahkan ke penyimpanan.');
        }
        $storedFiles[] = $targetPath;
        $mimeType = 'application/octet-stream';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $detectedMime = finfo_file($finfo, $targetPath);
                if (is_string($detectedMime) && $detectedMime !== '') $mimeType = $detectedMime;
                finfo_close($finfo);
            }
        }
        $fileSize = (int)filesize($targetPath);
        $publicToken = bin2hex(random_bytes(32));
        $insertFile = $conn->prepare('INSERT INTO ta_surat_lampiran (surat_id, nama_asli, nama_file, mime_type, ukuran, public_token) VALUES (?, ?, ?, ?, ?, ?)');
        $insertFile->bind_param('isssis', $recordId, $originalName, $storedName, $mimeType, $fileSize, $publicToken);
        if (!$insertFile->execute()) {
            throw new RuntimeException('Informasi lampiran gagal disimpan.');
        }
        $insertFile->close();
        $savedNames[] = $originalName;
        $savedPublicTokens[] = $publicToken;
    }

    $conn->commit();
    $transactionStarted = false;
    foreach ($removedFilePaths as $path) {
        if (is_file($path)) @unlink($path);
    }
    jsonResponse(200, [
        'status' => true,
        'success' => true,
        'message' => $action === 'tambah' ? 'Surat berhasil disimpan.' : 'Surat berhasil diperbarui.',
        'data' => [
            'id' => $recordId,
            'jenis_surat' => $kind,
            'nomor_surat' => $number,
            'pengirim_penerima' => $party,
            'tanggal_surat' => $date,
            'perihal' => $subject,
            'status' => $status,
            'catatan' => $note,
            'lampiran_baru' => $savedNames,
            'token_lampiran_baru' => $savedPublicTokens
        ]
    ]);
} catch (Throwable $error) {
    if ($transactionStarted && isset($conn) && $conn instanceof mysqli) {
        $conn->rollback();
    }
    foreach ($storedFiles as $path) {
        if (is_file($path)) @unlink($path);
    }
    error_log('proses_surat.php: ' . $error->getMessage());
    $message = $error instanceof RuntimeException ? $error->getMessage() : 'Terjadi kesalahan saat memproses surat.';
    jsonResponse(500, ['status' => false, 'success' => false, 'message' => $message]);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
