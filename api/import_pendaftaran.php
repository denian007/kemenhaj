<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) session_start();

function respondImport(int $code, array $payload): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respondImport(405, ['status' => false, 'message' => 'Metode request tidak didukung.']);
}
if (empty($_SESSION['user_id'])) {
    respondImport(401, ['status' => false, 'message' => 'Sesi login berakhir. Silakan login kembali.']);
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload) || !isset($payload['rows']) || !is_array($payload['rows'])) {
    respondImport(400, ['status' => false, 'message' => 'Format data import tidak valid.']);
}
$rows = $payload['rows'];
if (!$rows) respondImport(422, ['status' => false, 'message' => 'Tidak ada baris data untuk diimpor.']);
if (count($rows) > 2000) respondImport(413, ['status' => false, 'message' => 'Maksimal 2.000 baris per proses import.']);

require_once dirname(__DIR__) . '/config.php';
if (!isset($conn) || !($conn instanceof mysqli)) {
    respondImport(500, ['status' => false, 'message' => 'Koneksi database tidak tersedia.']);
}

function importText(array $row, string $key): string
{
    $value = $row[$key] ?? '';
    if (is_array($value) || is_object($value)) return '';
    if (is_float($value) && floor($value) === $value) $value = sprintf('%.0f', $value);
    return trim((string)$value);
}

function validImportDate(string $value, bool $nullable = false)
{
    if ($value === '' && $nullable) return null;
    $date = DateTime::createFromFormat('!Y-m-d', $value);
    $errors = DateTime::getLastErrors();
    if (!$date || $date->format('Y-m-d') !== $value || ($errors && ($errors['warning_count'] || $errors['error_count']))) return false;
    return $value;
}

function validYearValue(string $value, bool $nullable = false)
{
    if ($value === '' && $nullable) return null;
    if (!preg_match('/^\d{4}$/', $value)) return false;
    $year = (int)$value;
    return ($year >= 1901 && $year <= 2155) ? $value : false;
}

function normalizeImportRegionName(string $value): string
{
    $value = function_exists('mb_strtolower') ? mb_strtolower(trim($value), 'UTF-8') : strtolower(trim($value));
    $value = preg_replace('/^(desa|gampong|kampong|kelurahan|kecamatan)\s+/u', '', $value);
    return preg_replace('/[^a-z0-9]/', '', $value);
}

function loadImportRegionTable(mysqli $conn, string $table, array $nameCandidates, ?string $parentKeyword = null): array
{
    $schema = $conn->query("SHOW COLUMNS FROM `{$table}`");
    if (!$schema) throw new RuntimeException("Tabel wilayah {$table} tidak tersedia.");
    $columns = [];
    while ($column = $schema->fetch_assoc()) $columns[] = $column['Field'];
    $schema->free();

    $nameColumn = null;
    foreach ($nameCandidates as $candidate) {
        foreach ($columns as $column) {
            if (strtolower($column) === $candidate) { $nameColumn = $column; break 2; }
        }
    }
    $idColumn = in_array('id', $columns, true) ? 'id' : null;
    if ($idColumn === null) {
        foreach ($columns as $column) if (strpos(strtolower($column), 'id_') === 0) { $idColumn = $column; break; }
    }
    $parentColumn = null;
    if ($parentKeyword !== null) {
        $preferred = 'id_' . $parentKeyword;
        foreach ($columns as $column) {
            if (strtolower($column) === $preferred) { $parentColumn = $column; break; }
        }
        if ($parentColumn === null) {
            foreach ($columns as $column) {
                if (strpos(strtolower($column), $parentKeyword) !== false && stripos($column, 'id') !== false) { $parentColumn = $column; break; }
            }
        }
    }
    if ($nameColumn === null || $idColumn === null || ($parentKeyword !== null && $parentColumn === null)) {
        throw new RuntimeException("Kolom identitas wilayah {$table} tidak dapat dikenali.");
    }

    $select = "SELECT `{$idColumn}` AS region_id, `{$nameColumn}` AS region_name";
    if ($parentColumn !== null) $select .= ", `{$parentColumn}` AS parent_id";
    $result = $conn->query($select . " FROM `{$table}`");
    if (!$result) throw new RuntimeException("Data wilayah {$table} gagal dibaca.");
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => trim((string)$row['region_id']),
            'name' => trim((string)$row['region_name']),
            'parent' => isset($row['parent_id']) ? trim((string)$row['parent_id']) : ''
        ];
    }
    $result->free();
    return $items;
}

function resolveImportRegion(mysqli $conn, array &$clean, string $sessionKabupatenId): void
{
    static $regions = null;
    if ($clean['id_kabupaten'] !== '' && $clean['id_kecamatan'] !== '' && $clean['kabupaten'] !== '' && $clean['kecamatan'] !== '') return;

    if ($regions === null) {
        $regions = [
            'kabupaten' => loadImportRegionTable($conn, 'ta_kab', ['nm_kab','nama_kabupaten','kabupaten','nama','nm_kota','nama_kota','kab_kota','uraian']),
            'kecamatan' => loadImportRegionTable($conn, 'ta_kec', ['nm_kec','nama_kecamatan','kecamatan','nama','nm_kecamatan','uraian'], 'kab')
        ];
    }

    // Jika ID wilayah diisi pada workbook, jadikan ID sebagai sumber utama.
    if ($clean['id_kabupaten'] !== '' && $clean['id_kecamatan'] !== '') {
        $kabupaten = null;
        foreach ($regions['kabupaten'] as $item) {
            if ($item['id'] === $clean['id_kabupaten']) { $kabupaten = $item; break; }
        }
        $district = null;
        foreach ($regions['kecamatan'] as $item) {
            if ($item['id'] === $clean['id_kecamatan']) { $district = $item; break; }
        }
        if ($kabupaten && $district && $district['parent'] === $kabupaten['id']) {
            if ($sessionKabupatenId !== '' && $kabupaten['id'] !== $sessionKabupatenId) {
                throw new RuntimeException('ID kabupaten pada file berada di luar wilayah akun yang sedang login.');
            }
            $clean['kabupaten'] = $kabupaten['name'];
            $clean['kecamatan'] = $district['name'];
            return;
        }
    }

    if (!isset($regions['desa'])) {
        $regions['desa'] = loadImportRegionTable($conn, 'ta_desa', ['nm_desa','nama_desa','desa','nama','nm_kel','nama_kelurahan','kelurahan','uraian'], 'kec');
    }
    $providedKabupatenId = $clean['id_kabupaten'];
    $villageKey = normalizeImportRegionName($clean['desa']);
    $districtKey = normalizeImportRegionName($clean['kecamatan']);
    $villageMatches = array_values(array_filter($regions['desa'], function ($item) use ($villageKey) {
        return normalizeImportRegionName($item['name']) === $villageKey;
    }));
    if (!$villageMatches) throw new RuntimeException("Desa '{$clean['desa']}' tidak ditemukan pada data wilayah.");

    $districtById = [];
    foreach ($regions['kecamatan'] as $district) $districtById[$district['id']] = $district;
    $candidates = [];
    foreach ($villageMatches as $village) {
        $district = $districtById[$village['parent']] ?? null;
        if (!$district) continue;
        if ($providedKabupatenId !== '' && $district['parent'] !== $providedKabupatenId) continue;
        $score = 0;
        similar_text($districtKey, normalizeImportRegionName($district['name']), $score);
        $village['district'] = $district;
        $village['district_score'] = $score;
        $candidates[] = $village;
    }
    if (!$candidates) throw new RuntimeException("Kecamatan untuk desa '{$clean['desa']}' tidak dapat ditentukan.");

    usort($candidates, function ($a, $b) { return $b['district_score'] <=> $a['district_score']; });
    if (count($candidates) > 1) {
        $best = $candidates[0]['district_score'];
        $next = $candidates[1]['district_score'];
        if ($best < 70 || ($best - $next) < 5) {
            throw new RuntimeException("Desa '{$clean['desa']}' cocok pada beberapa kecamatan; wilayah perlu diperjelas.");
        }
    }
    $match = $candidates[0];
    $district = $match['district'];
    $kabupaten = null;
    foreach ($regions['kabupaten'] as $item) {
        if ($item['id'] === $district['parent']) { $kabupaten = $item; break; }
    }
    if (!$kabupaten) throw new RuntimeException("Kabupaten/kota untuk desa '{$clean['desa']}' tidak ditemukan.");
    if ($sessionKabupatenId !== '' && $district['parent'] !== $sessionKabupatenId) {
        throw new RuntimeException("Wilayah desa '{$clean['desa']}' berada di luar kabupaten/kota akun yang sedang login.");
    }

    $clean['desa'] = $match['name'];
    $clean['id_kecamatan'] = $district['id'];
    $clean['kecamatan'] = $district['name'];
    $clean['id_kabupaten'] = $kabupaten['id'];
    $clean['kabupaten'] = $kabupaten['name'];
}

$required = ['no_porsi','nama','tempat_lahir','tgl_lahir','usia','jenkel','telepon','alamat','id_kabupaten','kabupaten','id_kecamatan','kecamatan','desa','tgl_daftar','bank','no_rek','no_validasi'];
$limits = [
    'no_porsi'=>50, 'nama'=>150, 'nik'=>20, 'tempat_lahir'=>100, 'jenkel'=>1,
    'pendidikan'=>20, 'pekerjaan'=>100, 'telepon'=>20, 'id_kabupaten'=>20, 'kabupaten'=>100,
    'id_kecamatan'=>20, 'kecamatan'=>100, 'desa'=>100, 'status'=>20, 'bank'=>50,
    'no_rek'=>50, 'no_validasi'=>50, 'id_prov'=>20, 'id_kab_user'=>20
];
$errors = [];
$cleanRows = [];
$seenNik = [];
$seenPorsi = [];

foreach ($rows as $index => $row) {
    $excelRow = isset($row['_baris_excel']) ? (int)$row['_baris_excel'] : $index + 2;
    if (!is_array($row)) {
        $errors[] = ['row'=>$excelRow, 'message'=>'Format baris tidak valid.'];
        continue;
    }

    $clean = [];
    foreach (['no_porsi','nama','nik','tempat_lahir','jenkel','pendidikan','pekerjaan','telepon','alamat','id_kabupaten','kabupaten','id_kecamatan','kecamatan','desa','status','bank','no_rek','no_validasi','id_prov','id_kab_user'] as $field) {
        $clean[$field] = importText($row, $field);
    }
    foreach (['id_kabupaten','id_kecamatan'] as $idField) {
        if (in_array(strtoupper($clean[$idField]), ['#N/A','#VALUE!','#REF!','N/A'], true)) $clean[$idField] = '';
    }
    try {
        resolveImportRegion($conn, $clean, (string)($_SESSION['id_kab'] ?? ''));
    } catch (Throwable $regionError) {
        $errors[] = ['row'=>$excelRow, 'message'=>$regionError->getMessage()];
    }
    foreach ($required as $field) {
        $requiredValue = array_key_exists($field, $clean) ? $clean[$field] : importText($row, $field);
        if ($requiredValue === '') {
            $errors[] = ['row'=>$excelRow, 'message'=>"Kolom {$field} wajib diisi."];
        }
    }

    $gender = strtolower($clean['jenkel']);
    if (in_array($gender, ['lk','laki-laki','laki laki','l'], true)) $clean['jenkel'] = 'L';
    elseif (in_array($gender, ['pr','perempuan','p'], true)) $clean['jenkel'] = 'P';
    else $clean['jenkel'] = strtoupper($clean['jenkel']);

    foreach ($limits as $field => $limit) {
        $fieldLength = function_exists('mb_strlen') ? mb_strlen($clean[$field], 'UTF-8') : strlen($clean[$field]);
        if ($clean[$field] !== '' && $fieldLength > $limit) {
            $errors[] = ['row'=>$excelRow, 'message'=>"Kolom {$field} melebihi {$limit} karakter."];
        }
    }
    if ($clean['jenkel'] !== '' && !in_array($clean['jenkel'], ['L','P'], true)) {
        $errors[] = ['row'=>$excelRow, 'message'=>'Jenis kelamin hanya boleh L/Lk atau P/Pr.'];
    }
    if ($clean['status'] === '') $clean['status'] = 'pending';
    if (!in_array($clean['status'], ['pending','terverifikasi','ditolak'], true)) {
        $errors[] = ['row'=>$excelRow, 'message'=>'status hanya boleh pending, terverifikasi, atau ditolak.'];
    }

    $clean['tgl_lahir'] = validImportDate(importText($row,'tgl_lahir'));
    $clean['tgl_daftar'] = validImportDate(importText($row,'tgl_daftar'));
    $clean['tgl_setor_bpih'] = validImportDate(importText($row,'tgl_setor_bpih'), true);
    if ($clean['tgl_lahir'] === false) $errors[] = ['row'=>$excelRow, 'message'=>'tgl_lahir harus tanggal valid berformat YYYY-MM-DD.'];
    if ($clean['tgl_daftar'] === false) $errors[] = ['row'=>$excelRow, 'message'=>'tgl_daftar harus tanggal valid berformat YYYY-MM-DD.'];
    if ($clean['tgl_setor_bpih'] === false) $errors[] = ['row'=>$excelRow, 'message'=>'tgl_setor_bpih harus kosong atau tanggal valid berformat YYYY-MM-DD.'];

    $ageText = importText($row,'usia');
    $clean['usia'] = filter_var($ageText, FILTER_VALIDATE_INT);
    if ($clean['usia'] === false || $clean['usia'] < 0 || $clean['usia'] > 150) {
        $errors[] = ['row'=>$excelRow, 'message'=>'usia harus berupa angka bulat antara 0 sampai 150.'];
    }
    $registrationYear = importText($row, 'tahun');
    if ($registrationYear === '' && $clean['tgl_daftar'] !== false) $registrationYear = substr((string)$clean['tgl_daftar'], 0, 4);
    $clean['tahun'] = validYearValue($registrationYear);
    $clean['tahun_berangkat'] = validYearValue(importText($row,'tahun_berangkat'), true);
    if ($clean['tahun'] === false) $errors[] = ['row'=>$excelRow, 'message'=>'tahun harus berisi tahun valid (1901–2155).'];
    if ($clean['tahun_berangkat'] === false) $errors[] = ['row'=>$excelRow, 'message'=>'tahun_berangkat harus kosong atau tahun valid (1901–2155).'];

    $amount = importText($row,'jumlah_setor');
    if ($amount === '') {
        $clean['jumlah_setor'] = null;
    } else {
        $normalizedAmount = preg_replace('/[^0-9-]/', '', $amount);
        if ($normalizedAmount === '' || !is_numeric($normalizedAmount)) {
            $errors[] = ['row'=>$excelRow, 'message'=>'jumlah_setor harus berupa nominal angka.'];
            $clean['jumlah_setor'] = null;
        } else {
            $clean['jumlah_setor'] = (float)$normalizedAmount;
        }
    }

    if ($clean['no_porsi'] !== '') {
        if (isset($seenPorsi[$clean['no_porsi']])) {
            $clean['_duplicate_source'] = true;
        } else {
            $seenPorsi[$clean['no_porsi']] = $excelRow;
        }
    }
    if ($clean['nik'] !== '') {
        if (isset($seenNik[$clean['nik']])) {
            $clean['_duplicate_source'] = true;
        } else {
            $seenNik[$clean['nik']] = $excelRow;
        }
    }

    $clean['id_prov'] = $clean['id_prov'] !== '' ? $clean['id_prov'] : (string)($_SESSION['id_prov'] ?? '');
    $clean['id_kab_user'] = $clean['id_kab_user'] !== '' ? $clean['id_kab_user'] : (string)($_SESSION['id_kab'] ?? '');
    $clean['_excel_row'] = $excelRow;
    $cleanRows[] = $clean;
}

if ($errors) {
    mysqli_close($conn);
    respondImport(422, ['status'=>false, 'message'=>'Periksa kesalahan pada template. Tidak ada data yang disimpan.', 'errors'=>array_slice($errors, 0, 50)]);
}

try {
    $duplicateCheck = $conn->prepare("SELECT id FROM ta_pendaftaran WHERE no_porsi = ? OR (? <> '' AND nik = ?) LIMIT 1");
    if (!$duplicateCheck) throw new RuntimeException('Pemeriksaan data duplikat gagal disiapkan.');
    foreach ($cleanRows as &$row) {
        if (!empty($row['_duplicate_source'])) continue;
        $nik = $row['nik'];
        $porsi = $row['no_porsi'];
        $duplicateCheck->bind_param('sss', $porsi, $nik, $nik);
        $duplicateCheck->execute();
        $duplicateCheck->store_result();
        if ($duplicateCheck->num_rows > 0) $row['_duplicate_database'] = true;
        $duplicateCheck->free_result();
    }
    unset($row);
    $duplicateCheck->close();
    $rowsToInsert = array_values(array_filter($cleanRows, function ($row) {
        return empty($row['_duplicate_source']) && empty($row['_duplicate_database']);
    }));

    $sql = 'INSERT INTO ta_pendaftaran (uuid,no_porsi,nama,nik,tempat_lahir,tgl_lahir,usia,jenkel,pendidikan,pekerjaan,telepon,alamat,id_kabupaten,kabupaten,id_kecamatan,kecamatan,desa,tahun,tgl_daftar,status,bank,no_rek,tgl_setor_bpih,jumlah_setor,no_validasi,id_prov,id_kab_user,tahun_berangkat) VALUES (UUID(),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    $insert = $conn->prepare($sql);
    if (!$insert) throw new RuntimeException('Query penyimpanan import tidak dapat disiapkan.');
    $conn->begin_transaction();
    $imported = 0;
    $types = str_repeat('s',5) . 'i' . str_repeat('s',16) . 'd' . str_repeat('s',4);

    foreach ($rowsToInsert as $row) {
        $values = [
            $row['no_porsi'], $row['nama'], $row['nik'], $row['tempat_lahir'], $row['tgl_lahir'], $row['usia'],
            $row['jenkel'], $row['pendidikan'] !== '' ? $row['pendidikan'] : null,
            $row['pekerjaan'] !== '' ? $row['pekerjaan'] : null, $row['telepon'], $row['alamat'],
            $row['id_kabupaten'], $row['kabupaten'], $row['id_kecamatan'], $row['kecamatan'], $row['desa'],
            $row['tahun'], $row['tgl_daftar'], $row['status'], $row['bank'], $row['no_rek'],
            $row['tgl_setor_bpih'], $row['jumlah_setor'], $row['no_validasi'],
            $row['id_prov'] !== '' ? $row['id_prov'] : null,
            $row['id_kab_user'] !== '' ? $row['id_kab_user'] : null,
            $row['tahun_berangkat']
        ];
        $bind = [$types];
        foreach ($values as $key => &$value) $bind[] = &$value;
        if (!call_user_func_array([$insert, 'bind_param'], $bind) || !$insert->execute()) {
            throw new RuntimeException('Gagal menyimpan data pada baris Excel ' . $row['_excel_row'] . '.');
        }
        unset($value);
        $imported++;
    }

    $conn->commit();
    $insert->close();
    $missingNikCount = count(array_filter($rowsToInsert, function ($row) { return $row['nik'] === ''; }));
    $skippedDuplicateCount = count($cleanRows) - count($rowsToInsert);
    mysqli_close($conn);
    $message = $imported . ' data jamaah berhasil diimpor.';
    if ($skippedDuplicateCount > 0) $message .= ' ' . $skippedDuplicateCount . ' baris duplikat dilewati.';
    if ($missingNikCount > 0) $message .= ' ' . $missingNikCount . ' data tidak memiliki NIK dan disimpan dengan NIK kosong sesuai file sumber.';
    respondImport(200, [
        'status' => true,
        'imported' => $imported,
        'skipped_duplicates' => $skippedDuplicateCount,
        'missing_nik' => $missingNikCount,
        'message' => $message
    ]);
} catch (Throwable $error) {
    if (isset($conn) && $conn instanceof mysqli) {
        try { $conn->rollback(); } catch (Throwable $ignored) {}
        $conn->close();
    }
    error_log('import_pendaftaran.php: ' . $error->getMessage());
    respondImport(500, ['status'=>false, 'message'=>$error instanceof RuntimeException ? $error->getMessage() : 'Terjadi kesalahan saat menyimpan data import. Tidak ada data yang disimpan.']);
}
