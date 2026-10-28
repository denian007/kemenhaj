<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

// Cek koneksi
if (!isset($conn) || !($conn instanceof mysqli)) {
    echo json_encode(['data' => []]);
    exit;
}

// Daftar nama bulan dalam bahasa Indonesia
$bulanIndo = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$data = [];
// PERBAIKAN: Tambahkan tempat_lahir dan tgl_lahir di query SELECT
$sql = "SELECT * FROM ta_pendaftaran ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        // Proses format tgl_lahir (Y-m-d) menjadi (d Bulan Y)
        $tgl_lahir_format = '-';
        if (!empty($row['tgl_lahir']) && $row['tgl_lahir'] !== '0000-00-00') {
            $date = new DateTime($row['tgl_lahir']);
            $tanggal = $date->format('d'); // Tanggal (2 digit)
            $bulan   = (int)$date->format('m'); // Bulan (angka)
            $tahun   = $date->format('Y'); // Tahun

            // Gabungkan menjadi format: 07 Mei 1997
            $tgl_lahir_format = $tanggal . ' ' . $bulanIndo[$bulan] . ' ' . $tahun;
        }

        // Format tgl_daftar juga opsional jika ingin diubah di sini, 
        // tapi di JS DataTables sudah diformat menggunakan moment.js
        $data[] = [
            'uuid' => $row['uuid'],
            'no_porsi' => $row['no_porsi'],
            'nama' => $row['nama'],
            'nik' => $row['nik'],
            'status' => $row['status'],
            'tgl_daftar' => $row['tgl_daftar'],
            'tempat_lahir' => $row['tempat_lahir'] ?? '-',
            'tgl_lahir' => $tanggal,
            'tahun_lahir' => $tahun,
            'bulan_lahir' => $bulanIndo[$bulan] ?? '-',
            'layanan' => 'Haji Reguler',
            'usia' => $row['usia'],
            'jenis_kelamin' => $row['jenkel'] === 'L' ? 'Laki-laki' : ($row['jenkel'] === 'P' ? 'Perempuan' : '-'),
            'pendidikan' => $row['pendidikan'] ?? '-',
            'pekerjaan' => $row['pekerjaan'] ?? '-',
            'hp' => $row['telepon'] ?? '-',
            'alamat' => $row['alamat'] ?? '-',
            'id_provinsi' => $row['id_provinsi'] ?? '-',
            'id_kabupaten' => $row['id_kabupaten'] ?? '-',
            'id_kecamatan' => $row['id_kecamatan'] ?? '-',
            'kabupaten' => $row['kabupaten'] ?? '-',
            'kecamatan' => $row['kecamatan'] ?? '-',
            'desa' => $row['desa'] ?? '-',
            'tahun' => $row['tahun'] ?? '-',
            'tahun_berangkat' => $row['tahun_berangkat'] ?? '-',
            'bank' => $row['bank'] ?? '-',
            'no_rek' => $row['no_rek'] ?? '-',
            'no_validasi' => $row['no_validasi'] ?? '-'

            
        ];
    }
}

mysqli_close($conn);

// Kembalikan format JSON untuk DataTables
echo json_encode(['data' => $data]);
