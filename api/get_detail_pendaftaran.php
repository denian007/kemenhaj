<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config_path = dirname(__DIR__) . '/config.php';
if (!file_exists($config_path)) {
    echo json_encode(['status' => false, 'message' => 'File config.php tidak ditemukan.']);
    exit;
}
require_once $config_path;

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
    exit;
}

$uuid = trim($_GET['uuid'] ?? '');

if (empty($uuid)) {
    echo json_encode(['status' => false, 'message' => 'UUID data tidak valid.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM ta_pendaftaran WHERE uuid = ? LIMIT 1");
    if (!$stmt) {
        throw new Exception("Prepare statement gagal: " . $conn->error);
    }

    $stmt->bind_param("s", $uuid);
    $stmt->execute();

    // =========================================================================
    // PERBAIKAN: Mengambil metadata & bind_result secara dinamis (Bebas get_result)
    // =========================================================================
    $meta = $stmt->result_metadata();
    if (!$meta) {
        throw new Exception("Gagal mengambil metadata tabel.");
    }

    $fields = [];
    $row = [];
    while ($field = $meta->fetch_field()) {
        $fields[] = &$row[$field->name];
    }

    call_user_func_array([$stmt, 'bind_result'], $fields);

    if ($stmt->fetch()) {
        // Copy nilai associative array agar tidak mereferensi ke memori sementara
        $data = [];
        foreach ($row as $key => $val) {
            $data[$key] = $val;
        }

        echo json_encode([
            'status' => true,
            'data'   => $data
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'status'  => false,
            'message' => 'Data pendaftaran tidak ditemukan.'
        ]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'status'  => false,
        'message' => 'Server Error: ' . $e->getMessage()
    ]);
}

if (isset($conn) && $conn instanceof mysqli) {
    mysqli_close($conn);
}
