<?php
// 1. Paksa output menjadi JSON dan matikan error HTML
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0); // Jangan tampilkan error HTML
error_reporting(E_ALL);

// 2. Mulai Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Panggil config dengan path yang aman
$config_path = dirname(__DIR__) . '/config.php';
if (!file_exists($config_path)) {
    echo json_encode(['status' => false, 'message' => 'File config.php tidak ditemukan di ' . $config_path]);
    exit;
}
require_once $config_path;

// 4. Cek Login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login ulang.']);
    exit;
}

$response = ['status' => false, 'message' => 'Terjadi kesalahan sistem.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $data = json_decode($inputJSON, true);

    if (!$data) {
        echo json_encode(['status' => false, 'message' => 'Data tidak valid atau kosong.']);
        exit;
    }

    // ==========================================
    // FUNGSI KONVERSI TANGGAL INDONESIA KE MYSQL
    // ==========================================
    function parseTanggalIndo($tglStr, $nullable = false)
    {
        $tglStr = trim((string)$tglStr);
        if ($tglStr === '') return $nullable ? null : date('Y-m-d');

        // Tanggal dari date input atau database sudah berbentuk YYYY-MM-DD.
        $date = DateTime::createFromFormat('!Y-m-d', $tglStr);
        if ($date && $date->format('Y-m-d') === $tglStr) return $tglStr;

        $bulanIndo = [
            'Januari'   => '01',
            'Februari'  => '02',
            'Maret'     => '03',
            'April'     => '04',
            'Mei'       => '05',
            'Juni'      => '06',
            'Juli'      => '07',
            'Agustus'   => '08',
            'September' => '09',
            'Oktober'   => '10',
            'November'  => '11',
            'Desember'  => '12'
        ];

        $parts = explode(' ', trim($tglStr));
        if (count($parts) === 3) {
            $day       = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
            $monthName = ucfirst(strtolower($parts[1]));
            $year      = $parts[2];

            if (isset($bulanIndo[$monthName])) {
                return $year . '-' . $bulanIndo[$monthName] . '-' . $day;
            }
        }

        $time = strtotime($tglStr);
        return $time ? date('Y-m-d', $time) : date('Y-m-d');
    }

    // Ambil data
    $idJamaah     = trim($data['idJamaah'] ?? ''); // KUNCI UTAMA: UUID untuk penentu mode Edit / Tambah
    $no_porsi     = trim($data['no_porsi'] ?? '');
    $nama         = trim($data['nama'] ?? '');
    $nik          = trim($data['nik'] ?? '');
    $tempat_lahir = trim($data['tempat_lahir'] ?? '');
    $tgl_lahir    = parseTanggalIndo($data['tgl_lahir'] ?? '');
    $usia         = (int)($data['usia'] ?? 0);
    $jenkel       = trim($data['jenkel'] ?? '');
    $pendidikan   = trim($data['pendidikan'] ?? '');
    $pekerjaan    = trim($data['pekerjaan'] ?? '');
    $telepon      = trim($data['telepon'] ?? '');
    $alamat       = trim($data['alamat'] ?? '');
    $id_kabupaten = trim($data['id_kabupaten'] ?? '');
    $kabupaten    = trim($data['kabupaten'] ?? '');
    $id_kecamatan = trim($data['id_kecamatan'] ?? '');
    $kecamatan    = trim($data['kecamatan'] ?? '');
    $desa         = trim($data['desa'] ?? '');
    $tahun        = trim($data['tahun'] ?? date('Y'));
    $tgl_daftar   = parseTanggalIndo($data['tgl_daftar'] ?? '');
    $status       = trim($data['status'] ?? 'pending');
    $bank         = trim($data['bank'] ?? '');
    $no_rek       = trim($data['no_rek'] ?? '');
    $no_validasi  = trim($data['no_validasi'] ?? '');
    $tglSetorBpih = parseTanggalIndo($data['tgl_setor_bpih'] ?? $data['tglSetorBpih'] ?? '', true);
    $jumlahSetorRaw = $data['jumlah_setor'] ?? $data['jumlah_setor_bpih'] ?? '';
    $jumlahSetor = ($jumlahSetorRaw === '' || $jumlahSetorRaw === null)
        ? null
        : (float)preg_replace('/[^0-9.]/', '', (string)$jumlahSetorRaw);
    $tahunBerangkatRaw = $data['tahun_berangkat'] ?? $data['tahun_keberangkatan'] ?? '';
    $tahunBerangkat = ($tahunBerangkatRaw === '' || $tahunBerangkatRaw === null) ? null : (int)$tahunBerangkatRaw;

    // Validasi form
    if (empty($no_porsi) || empty($nama) || empty($nik) || empty($id_kabupaten) || empty($id_kecamatan) || empty($desa)) {
        echo json_encode(['status' => false, 'message' => 'Harap lengkapi semua field yang wajib diisi.']);
        exit;
    }

    try {
        if (!empty($idJamaah)) {
            // ==========================================
            // MODE EDIT / UPDATE DATA
            // ==========================================

            // Cek duplikasi NIK / No. Porsi selain data jamaah yang sedang diedit ini
            $stmt_cek = $conn->prepare("SELECT id FROM ta_pendaftaran WHERE (nik = ? OR no_porsi = ?) AND uuid != ?");
            if (!$stmt_cek) throw new Exception("Prepare cek edit gagal: " . $conn->error);

            $stmt_cek->bind_param("sss", $nik, $no_porsi, $idJamaah);
            $stmt_cek->execute();
            $stmt_cek->store_result();
            if ($stmt_cek->num_rows > 0) {
                echo json_encode(['status' => false, 'message' => 'NIK atau No. Porsi sudah terdaftar pada jamaah lain.']);
                $stmt_cek->close();
                exit;
            }
            $stmt_cek->close();

            // SQL Update Data
            $sql_update = "UPDATE ta_pendaftaran SET 
                no_porsi = ?, nama = ?, nik = ?, tempat_lahir = ?, tgl_lahir = ?, usia = ?, jenkel = ?, 
                pendidikan = ?, pekerjaan = ?, telepon = ?, alamat = ?, id_kabupaten = ?, kabupaten = ?, 
                id_kecamatan = ?, kecamatan = ?, desa = ?, tahun = ?, tgl_daftar = ?, status = ?, 
                bank = ?, no_rek = ?, no_validasi = ?, tgl_setor_bpih = ?,
                jumlah_setor = ?, tahun_berangkat = ?
                WHERE uuid = ?";

            $stmt = $conn->prepare($sql_update);
            if (!$stmt) throw new Exception("Prepare update gagal: " . $conn->error);

            // Jumlah setoran bertipe decimal; kolom lain memakai string/int.
            $types = str_repeat('s', 26);
            $types[5] = 'i';   // usia
            $types[23] = 'd';  // jumlah_setor
            $stmt->bind_param(
                $types,
                $no_porsi,
                $nama,
                $nik,
                $tempat_lahir,
                $tgl_lahir,
                $usia,
                $jenkel,
                $pendidikan,
                $pekerjaan,
                $telepon,
                $alamat,
                $id_kabupaten,
                $kabupaten,
                $id_kecamatan,
                $kecamatan,
                $desa,
                $tahun,
                $tgl_daftar,
                $status,
                $bank,
                $no_rek,
                $no_validasi,
                $tglSetorBpih,
                $jumlahSetor,
                $tahunBerangkat,
                $idJamaah
            );

            if ($stmt->execute()) {
                $response['status']  = true;
                $response['message'] = 'Data pendaftaran berhasil diperbarui.';
            } else {
                throw new Exception("Gagal eksekusi update: " . $stmt->error);
            }
            $stmt->close();
        } else {
            // ==========================================
            // MODE TAMBAH BARU / INSERT DATA
            // ==========================================

            // Cek Duplikasi NIK / No. Porsi di seluruh tabel
            $stmt_cek = $conn->prepare("SELECT id FROM ta_pendaftaran WHERE nik = ? OR no_porsi = ?");
            if (!$stmt_cek) throw new Exception("Prepare cek insert gagal: " . $conn->error);

            $stmt_cek->bind_param("ss", $nik, $no_porsi);
            $stmt_cek->execute();
            $stmt_cek->store_result();
            if ($stmt_cek->num_rows > 0) {
                echo json_encode(['status' => false, 'message' => 'NIK atau No. Porsi sudah terdaftar di sistem.']);
                $stmt_cek->close();
                exit;
            }
            $stmt_cek->close();

            // Insert Database (Sertakan UUID() otomatis untuk identifier)
            $sql_insert = "INSERT INTO ta_pendaftaran (
                        uuid, no_porsi, nama, nik, tempat_lahir, tgl_lahir, usia, jenkel, 
                        pendidikan, pekerjaan, telepon, alamat, id_kabupaten, kabupaten, 
                        id_kecamatan, kecamatan, desa, tahun, tgl_daftar, status, 
                        bank, no_rek, tgl_setor_bpih, jumlah_setor, no_validasi,
                        id_prov, id_kab_user, tahun_berangkat
                    ) VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql_insert);
            if (!$stmt) throw new Exception("Prepare insert gagal: " . $conn->error);

            $id_prov     = $_SESSION['id_prov'] ?? '';
            $id_kab_user = $_SESSION['id_kab'] ?? '';

            $types = str_repeat('s', 27);
            $types[5] = 'i';   // usia
            $types[22] = 'd';  // jumlah_setor
            $stmt->bind_param(
                $types,
                $no_porsi,
                $nama,
                $nik,
                $tempat_lahir,
                $tgl_lahir,
                $usia,
                $jenkel,
                $pendidikan,
                $pekerjaan,
                $telepon,
                $alamat,
                $id_kabupaten,
                $kabupaten,
                $id_kecamatan,
                $kecamatan,
                $desa,
                $tahun,
                $tgl_daftar,
                $status,
                $bank,
                $no_rek,
                $tglSetorBpih,
                $jumlahSetor,
                $no_validasi,
                $id_prov,
                $id_kab_user,
                $tahunBerangkat
            );

            if ($stmt->execute()) {
                $response['status']  = true;
                $response['message'] = 'Data pendaftaran berhasil disimpan.';
            } else {
                throw new Exception("Gagal eksekusi insert: " . $stmt->error);
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        $response['message'] = 'Server Error: ' . $e->getMessage();
    }
}

if (isset($conn) && $conn instanceof mysqli) {
    mysqli_close($conn);
}

echo json_encode($response);
