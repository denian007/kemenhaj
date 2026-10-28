<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

// Cek koneksi database sesuai standar skrip Anda
if (!isset($conn) || !($conn instanceof mysqli)) {
    echo json_encode(['status' => false, 'message' => 'Koneksi database gagal.', 'data' => []]);
    exit;
}

$data = [];

// Query mengambil data bank aktif (status = '1') dari tabel ta_bank
$sql = "SELECT id, nm_bank FROM ta_bank WHERE status = '1' ORDER BY nm_bank ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id'      => $row['id'],
            'nm_bank' => $row['nm_bank']
        ];
    }
}

mysqli_close($conn);

// Kembalikan format JSON untuk dikonsumsi AJAX Select2 di pendaftaran.php
echo json_encode([
    'status' => true,
    'data'   => $data
], JSON_UNESCAPED_UNICODE);
