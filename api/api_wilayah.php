<?php

/**
 * Endpoint autocomplete wilayah untuk pendaftaran.php.
 * Tabel sumber: ta_desa, ta_kec, ta_kab.
 * Menggunakan MySQLi sesuai dengan auth_proses.php
 * Mendukung Cascading Dropdown (Kabupaten -> Kecamatan -> Desa)
 */

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// Panggil file koneksi config.php
$config_path = dirname(__DIR__) . '/config.php';
if (!file_exists($config_path)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'File config.php tidak ditemukan.', 'data' => []]);
    exit;
}
require_once $config_path;

function respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Helper functions untuk kompatibilitas PHP 7.x - 8.x
if (!function_exists('my_str_starts_with')) {
    function my_str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}

if (!function_exists('my_str_contains')) {
    function my_str_contains(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

$tipe   = strtolower(trim((string)($_GET['tipe'] ?? '')));
$q      = trim((string)($_GET['q'] ?? ''));
$id_kab = trim((string)($_GET['id_kab'] ?? '')); // Parameter dari Kecamatan
$id_kec = trim((string)($_GET['id_kec'] ?? '')); // Parameter dari Desa

// Validasi tipe wilayah
$allowed_tables = [
    'desa'      => 'ta_desa',
    'kecamatan' => 'ta_kec',
    'kabupaten' => 'ta_kab',
];

if (!isset($allowed_tables[$tipe])) {
    respond(['success' => false, 'message' => 'Tipe wilayah tidak valid.', 'data' => []], 400);
}

if (mb_strlen($q) < 2) {
    respond(['success' => true, 'data' => []]);
}

$table = $allowed_tables[$tipe];

// Pastikan koneksi $conn tersedia dari config.php
if (!isset($conn) || !($conn instanceof mysqli)) {
    respond([
        'success' => false,
        'message' => 'Koneksi database gagal atau variabel $conn tidak ditemukan di config.php.',
        'data'    => []
    ], 500);
}

try {
    // Deteksi nama kolom yang tepat untuk setiap tabel
    $preferredColumns = [
        'desa'      => ['nm_desa', 'nama_desa', 'desa', 'nama', 'nm_kel', 'nama_kelurahan', 'kelurahan', 'uraian'],
        'kecamatan' => ['nm_kec', 'nama_kecamatan', 'kecamatan', 'nama', 'nm_kecamatan', 'uraian'],
        'kabupaten' => ['nm_kab', 'nama_kabupaten', 'kabupaten', 'nama', 'nm_kota', 'nama_kota', 'kab_kota', 'uraian'],
    ];

    // Ambil struktur kolom dari tabel
    $columns = [];
    $result  = $conn->query("SHOW COLUMNS FROM `$table`");
    if (!$result) {
        throw new RuntimeException("Tabel `$table` tidak ditemukan atau gagal dibaca di database.");
    }
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    $result->free();

    // 1. Cari kolom nama yang cocok
    $nameColumn = null;
    foreach ($preferredColumns[$tipe] as $candidate) {
        foreach ($columns as $column) {
            if (strtolower((string)$column) === $candidate) {
                $nameColumn = (string)$column;
                break 2;
            }
        }
    }

    if ($nameColumn === null) {
        respond([
            'success' => false,
            'message' => "Kolom nama wilayah tidak dikenali pada tabel {$table}. Kolom tersedia: " . implode(', ', $columns),
            'data'    => []
        ], 500);
    }

    // 2. Cari kolom ID (Primary Key)
    $idColumn = 'id';
    if (!in_array('id', $columns, true)) {
        $idColumn = null;
        foreach ($columns as $col) {
            if (my_str_starts_with(strtolower($col), 'id_')) {
                $idColumn = $col;
                break;
            }
        }
        if ($idColumn === null && isset($columns[0])) {
            $idColumn = $columns[0];
        }
    }

    // 3. Siapkan Query Dasar
    $sql    = "SELECT DISTINCT `$idColumn` AS id, `$nameColumn` AS nama FROM `$table` WHERE `$nameColumn` LIKE ?";
    $params = ['%' . $q . '%'];
    $types  = 's';

    // 4. Tambahkan Filter Relasi (Cascading)
    if ($tipe === 'kecamatan' && !empty($id_kab)) {
        $relColumn = 'id_kab';
        if (!in_array($relColumn, $columns, true)) {
            foreach ($columns as $col) {
                if (my_str_contains(strtolower($col), 'kab')) {
                    $relColumn = $col;
                    break;
                }
            }
        }
        if (in_array($relColumn, $columns, true)) {
            $sql     .= " AND `$relColumn` = ?";
            $params[] = $id_kab;
            $types   .= 's';
        }
    }

    if ($tipe === 'desa' && !empty($id_kec)) {
        $relColumn = 'id_kec';
        if (!in_array($relColumn, $columns, true)) {
            foreach ($columns as $col) {
                if (my_str_contains(strtolower($col), 'kec')) {
                    $relColumn = $col;
                    break;
                }
            }
        }
        if (in_array($relColumn, $columns, true)) {
            $sql     .= " AND `$relColumn` = ?";
            $params[] = $id_kec;
            $types   .= 's';
        }
    }

    $sql .= " ORDER BY `$nameColumn` ASC LIMIT 20";

    // Eksekusi Prepared Statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException('Gagal menyiapkan kueri: ' . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // PERBAIKAN UTAMA: Menggunakan bind_result & fetch (Tanpa get_result())
    $res_id   = null;
    $res_nama = null;
    $stmt->bind_result($res_id, $res_nama);

    $data = [];
    while ($stmt->fetch()) {
        $nama_str = trim((string)$res_nama);
        $id_str   = trim((string)$res_id);
        if ($nama_str !== '') {
            $data[] = [
                'id'   => $id_str,
                'nama' => $nama_str
            ];
        }
    }

    $stmt->close();

    respond(['success' => true, 'data' => $data]);
} catch (Throwable $e) {
    error_log('[api_wilayah] ' . $e->getMessage());
    respond([
        'success' => false,
        'message' => 'Error backend: ' . $e->getMessage(),
        'data'    => []
    ], 500);
}
