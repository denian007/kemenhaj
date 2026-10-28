<?php
// 1. Set header JSON dan matikan display error HTML agar response tidak merusak parsing JSON
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

// 2. Mulai Session jika belum berjalan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Panggil file koneksi/config dengan path yang aman
$config_path = dirname(__DIR__) . '/config.php';
if (!file_exists($config_path)) {
    echo json_encode(['status' => false, 'message' => 'File config.php tidak ditemukan di ' . $config_path]);
    exit;
}
require_once $config_path;

// 4. Cek Autentikasi User
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
    exit;
}

$response = ['status' => false, 'message' => 'Metode request tidak diizinkan.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tangkap input baik dari FormData/POST biasa maupun JSON body
    $inputJSON = file_get_contents('php://input');
    $dataJSON  = json_decode($inputJSON, true);

    $uuid = trim($_POST['uuid'] ?? $dataJSON['uuid'] ?? '');

    // Validasi parameter UUID
    if (empty($uuid)) {
        echo json_encode(['status' => false, 'message' => 'Identifier data (UUID) tidak ditemukan.']);
        exit;
    }

    try {
        // Cek keberadaan data sebelum dihapus
        $stmt_cek = $conn->prepare("SELECT id FROM ta_pendaftaran WHERE uuid = ?");
        if (!$stmt_cek) {
            throw new Exception("Prepare statement cek gagal: " . $conn->error);
        }

        $stmt_cek->bind_param("s", $uuid);
        $stmt_cek->execute();
        $stmt_cek->store_result();

        if ($stmt_cek->num_rows === 0) {
            echo json_encode(['status' => false, 'message' => 'Data pendaftaran tidak ditemukan atau sudah dihapus.']);
            $stmt_cek->close();
            exit;
        }
        $stmt_cek->close();

        // Eksekusi Hapus Data
        $stmt_del = $conn->prepare("DELETE FROM ta_pendaftaran WHERE uuid = ?");
        if (!$stmt_del) {
            throw new Exception("Prepare statement hapus gagal: " . $conn->error);
        }

        $stmt_del->bind_param("s", $uuid);

        if ($stmt_del->execute()) {
            $response['status']  = true;
            $response['message'] = 'Data pendaftaran berhasil dihapus.';
        } else {
            throw new Exception("Gagal menghapus data: " . $stmt_del->error);
        }

        $stmt_del->close();
    } catch (Exception $e) {
        $response['status']  = false;
        $response['message'] = 'Server Error: ' . $e->getMessage();
    }
}

// Tutup koneksi database
if (isset($conn) && $conn instanceof mysqli) {
    mysqli_close($conn);
}

echo json_encode($response);
