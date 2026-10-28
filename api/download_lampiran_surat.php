<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

function failDownload(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: text/plain; charset=utf-8');
    echo $message;
    exit;
}

require_once dirname(__DIR__) . '/config.php';

try {
    $publicToken = strtolower(trim((string)($_GET['token'] ?? '')));
    if (!preg_match('/^[a-f0-9]{64}$/', $publicToken)) {
        // Tautan yang pernah dibagikan sebelum token diterapkan memakai ?id=...
        // Alihkan tautan lama ke token acak agar tidak memutus tautan yang sudah tersebar.
        $legacyId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$legacyId || $legacyId < 1) {
            failDownload(400, 'Token tautan lampiran tidak valid. Buka kembali menu Bagikan untuk memperoleh tautan terbaru.');
        }

        $columnCheck = $conn->query("SHOW COLUMNS FROM ta_surat_lampiran LIKE 'public_token'");
        if (!$columnCheck) throw new RuntimeException('Kolom token tautan tidak dapat diperiksa.');
        if ($columnCheck->num_rows === 0) {
            if (!$conn->query('ALTER TABLE ta_surat_lampiran ADD COLUMN public_token CHAR(64) NULL AFTER id')) {
                throw new RuntimeException('Kolom token tautan tidak dapat dibuat.');
            }
        }
        $columnCheck->free();

        $legacy = $conn->prepare('SELECT public_token FROM ta_surat_lampiran WHERE id = ? LIMIT 1');
        $legacy->bind_param('i', $legacyId);
        $legacy->execute();
        $legacy->bind_result($publicToken);
        $legacyFound = $legacy->fetch();
        $legacy->close();
        if (!$legacyFound) failDownload(404, 'File lampiran tidak ditemukan.');

        if (!preg_match('/^[a-f0-9]{64}$/i', trim((string)$publicToken))) {
            $publicToken = bin2hex(random_bytes(32));
            $updateToken = $conn->prepare('UPDATE ta_surat_lampiran SET public_token = ? WHERE id = ?');
            $updateToken->bind_param('si', $publicToken, $legacyId);
            if (!$updateToken->execute()) throw new RuntimeException('Token tautan lama tidak dapat diperbarui.');
            $updateToken->close();
        }
        header('Location: download_lampiran_surat.php?token=' . rawurlencode($publicToken), true, 302);
        exit;
    }

    $stmt = $conn->prepare('SELECT l.nama_asli, l.nama_file, l.mime_type FROM ta_surat_lampiran l INNER JOIN ta_surat s ON s.id = l.surat_id WHERE l.public_token = ? LIMIT 1');
    if (!$stmt) throw new RuntimeException('Query lampiran tidak dapat disiapkan.');
    $stmt->bind_param('s', $publicToken);
    $stmt->execute();
    $stmt->bind_result($originalName, $storedName, $mimeType);
    if (!$stmt->fetch()) {
        $stmt->close();
        failDownload(404, 'File lampiran tidak ditemukan.');
    }
    $stmt->close();

    $baseDirectory = realpath(dirname(__DIR__) . '/uploads/surat');
    $safeStoredName = basename((string)$storedName);
    $filePath = $baseDirectory ? realpath($baseDirectory . DIRECTORY_SEPARATOR . $safeStoredName) : false;
    if (!$baseDirectory || !$filePath || strpos($filePath, $baseDirectory . DIRECTORY_SEPARATOR) !== 0 || !is_file($filePath)) {
        failDownload(404, 'File lampiran tidak tersedia di penyimpanan.');
    }

    $downloadName = basename((string)$originalName);
    $fallbackName = preg_replace('/[^A-Za-z0-9._-]/', '_', $downloadName);
    header('Content-Type: ' . (preg_match('/^[a-zA-Z0-9.+-]+\/[a-zA-Z0-9.+-]+$/', (string)$mimeType) ? $mimeType : 'application/octet-stream'));
    header('Content-Length: ' . filesize($filePath));
    header('Content-Disposition: attachment; filename="' . addcslashes($fallbackName, '"\\') . '"; filename*=UTF-8\'\'' . rawurlencode($downloadName));
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store, max-age=0');
    readfile($filePath);
    $conn->close();
    exit;
} catch (Throwable $error) {
    error_log('download_lampiran_surat.php: ' . $error->getMessage());
    failDownload(500, 'Terjadi kesalahan saat mengambil lampiran surat.');
} finally {
    if (isset($conn) && $conn instanceof mysqli) $conn->close();
}
