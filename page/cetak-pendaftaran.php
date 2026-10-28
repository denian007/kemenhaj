<?php
// 1. Matikan output warning/error HTML ke stream HTTP untuk mencegah error TCPDF
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

// Buka output buffering
ob_start();

session_start();

// 2. Cek Autentikasi User
if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

// 3. Ambil parameter UUID
$uuid = trim($_GET['uuid'] ?? '');
if (empty($uuid)) {
    die("Error: Parameter UUID tidak ditemukan.");
}

// 4. Panggil config database
$config_path = dirname(__DIR__) . '/config.php';
if (!file_exists($config_path)) {
    die("Error: File config.php tidak ditemukan di " . $config_path);
}
require_once $config_path;

// =========================================================================
// 5. LOAD LIBRARY VIA COMPOSER AUTOLOAD
// =========================================================================
$autoload_path = dirname(__DIR__) . '/page/vendor/autoload.php';
if (!file_exists($autoload_path)) {
    $autoload_path = dirname(__DIR__) . '/vendor/autoload.php';
}

if (!file_exists($autoload_path)) {
    die("Error: File vendor/autoload.php tidak ditemukan.");
}

require_once $autoload_path;

if (!class_exists('TCPDF')) {
    die("Error: Class TCPDF belum dapat dimuat oleh Autoloader Composer.");
}

// 6. Ambil Data Detail Jamaah berdasarkan UUID dari Database (BEBAS get_result)
try {
    $stmt = $conn->prepare("SELECT * FROM ta_pendaftaran WHERE uuid = ? OR id = ? LIMIT 1");
    if (!$stmt) {
        throw new Exception("Prepare statement gagal: " . $conn->error);
    }
    $stmt->bind_param("ss", $uuid, $uuid);
    $stmt->execute();

    // PERBAIKAN: Ambil metadata & bind_result secara dinamis tanpa get_result()
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

    $data = null;
    if ($stmt->fetch()) {
        $data = [];
        foreach ($row as $key => $val) {
            $data[$key] = $val;
        }
    }
    $stmt->close();

    if (!$data) {
        die("Data pendaftaran jamaah tidak ditemukan.");
    }
} catch (Exception $e) {
    die("Kesalahan database: " . $e->getMessage());
}

// 7. Format Data & Tanggal Indonesia
function formatTglIndo($tglStr)
{
    if (empty($tglStr) || $tglStr == '0000-00-00') return '-';
    $bulanIndo = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $time = strtotime($tglStr);
    if (!$time) return $tglStr;
    $d = date('j', $time);
    $m = (int)date('n', $time);
    $y = date('Y', $time);
    return $d . ' ' . ($bulanIndo[$m] ?? '') . ' ' . $y;
}

$no_porsi     = htmlspecialchars($data['no_porsi'] ?? '-');
$nama         = htmlspecialchars($data['nama'] ?? '-');
$nik          = htmlspecialchars($data['nik'] ?? '-');
$tempat_lahir = htmlspecialchars($data['tempat_lahir'] ?? '-');
$tgl_lahir    = formatTglIndo($data['tgl_lahir'] ?? '');
$ttl          = $tempat_lahir . ', ' . $tgl_lahir;
$usia         = !empty($data['usia']) ? htmlspecialchars($data['usia']) . ' Tahun' : '-';
$jenkel       = ($data['jenkel'] ?? '') === 'L' ? 'Laki-laki' : (($data['jenkel'] ?? '') === 'P' ? 'Perempuan' : htmlspecialchars($data['jenkel'] ?? '-'));
$pendidikan   = htmlspecialchars($data['pendidikan'] ?? '-');
$pekerjaan    = htmlspecialchars($data['pekerjaan'] ?? '-');
$telepon      = htmlspecialchars($data['telepon'] ?? '-');
$alamat       = htmlspecialchars($data['alamat'] ?? '-');
$desa         = htmlspecialchars($data['desa'] ?? '-');
$kecamatan    = htmlspecialchars($data['kecamatan'] ?? '-');
$kabupaten    = htmlspecialchars($data['kabupaten'] ?? '-');
$alamat_lengkap = $alamat . ', Desa ' . $desa . ', Kec. ' . $kecamatan . ', ' . $kabupaten;

$tahun_porsi  = htmlspecialchars($data['tahun'] ?? date('Y'));
$tgl_daftar   = formatTglIndo($data['tgl_daftar'] ?? date('Y-m-d'));
$status       = strtoupper(htmlspecialchars($data['status'] ?? 'PENDING'));
$bank         = htmlspecialchars($data['bank'] ?? '-');
$no_rek       = htmlspecialchars($data['no_rek'] ?? '-');
$no_validasi  = htmlspecialchars($data['no_validasi'] ?? '-');

// 8. Inisialisasi TCPDF Kustom
class MYPDF extends TCPDF
{
    public function Header() {}
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 10, 'Dokumen Resmi — Sistem Informasi Pendaftaran Haji Terpadu Kemenhaj Kota Subulussalam', 'T', false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Setting Dokumen
$pdf->SetCreator('Kemenhaj Subulussalam System');
$pdf->SetAuthor('Seksi PHU Kota Subulussalam');
$pdf->SetTitle('Bukti Pendaftaran Haji — ' . $nama);
$pdf->SetMargins(15, 12, 15);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->SetFont('helvetica', '', 9);
$pdf->AddPage('P', 'A4');

// Path Logo
$logo_path = dirname(__DIR__) . '/logo.png';
$logo_html = file_exists($logo_path)
    ? '<img src="' . $logo_path . '" width="70" height="70" />'
    : '<span style="font-size: 18px; font-weight: bold; color: #000000;">[ LOGO ]</span>';

// 9. Layout HTML Pro, Rapi, & Presisi
$html = '
<style>
    .kop-table {
        width: 100%;
        border-bottom: 3px double #000000;
        padding-bottom: 10px;
    }
    .kop-main {
        font-size: 13pt;
        font-weight: bold;
        color: #000000;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .kop-sub {
        font-size: 11pt;
        font-weight: bold;
        color: #000000;
        text-transform: uppercase;
        margin-top: 3px;
    }
    .kop-phu {
        font-size: 9pt;
        font-weight: bold;
        color: #000000;
        margin-top: 2px;
    }
    .kop-address {
        font-size: 8pt;
        color: #222222;
        margin-top: 3px;
    }
    .doc-header {
        text-align: center;
        margin-top: 15px;
        margin-bottom: 15px;
    }
    .doc-title {
        font-size: 13pt;
        font-weight: bold;
        color: #000000;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        text-decoration: underline;
    }
    .doc-sub {
        font-size: 9pt;
        color: #000000;
        font-weight: bold;
        margin-top: 4px;
    }
    .porsi-box {
        border: 2px solid #000000;
        padding: 10px;
        text-align: center;
        background-color: #fafafa;
    }
    .porsi-label {
        font-size: 8.5pt;
        color: #000000;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .porsi-value {
        font-size: 20pt;
        font-weight: bold;
        color: #000000;
        letter-spacing: 5px;
        margin-top: 2px;
    }
    .section-title {
        background-color: #1a1a1a;
        color: #ffffff;
        font-size: 8.5pt;
        font-weight: bold;
        padding: 6px 10px;
        margin-top: 16px;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table-data {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #d1d5db;
    }
    .table-data tr td {
        padding: 8px 10px;
        font-size: 9pt;
        vertical-align: middle;
        border-bottom: 1px solid #e5e7eb;
    }
    .bg-zebra {
        background-color: #f9fafb;
    }
    .col-label {
        width: 33%;
        font-weight: bold;
        color: #000000;
    }
    .col-colon {
        width: 3%;
        text-align: center;
        color: #000000;
        font-weight: bold;
    }
    .col-val {
        width: 64%;
        color: #000000;
    }
    .status-badge {
        font-weight: bold;
        padding: 3px 8px;
        border: 1px solid #000000;
        background-color: #f3f4f6;
        display: inline-block;
    }
    .ttd-table {
        width: 100%;
        margin-top: 35px;
    }
    .ttd-table td {
        text-align: center;
        font-size: 9pt;
        color: #000000;
    }
</style>

<!-- KOP SURAT RESMI -->
<table class="kop-table" cellpadding="0" cellspacing="0">
    <tr>
        <td width="14%" align="center" valign="middle">' . $logo_html . '</td>
        <td width="86%" align="center">
            <div class="kop-main"><b>KEMENTERIAN HAJI DAN UMRAH REPUBLIK INDONESIA</b></div>
            <div class="kop-sub"><b>KANTOR KOTA SUBULUSSALAM</b></div>
            <div class="kop-phu">Seksi Penyelenggaraan Haji dan Umrah (PHU)</div>
            <div class="kop-address">Jln. Teuku Umar No. 1, Subulussalam — Provinsi Aceh | Email: phu@kemenhaj-subulussalam.go.id</div>
        </td>
    </tr>
</table>

<br/><br/>

<!-- JUDUL DOKUMEN -->
<div class="doc-header">
    <div class="doc-title"><b>BUKTI PENDAFTARAN PORSI HAJI</b></div>
    <div class="doc-sub">Tahun Pendaftaran / Pemberangkatan: <b>' . $tahun_porsi . '</b></div>
</div>

<br/>

<!-- CARD NOMOR PORSI -->
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="20%"></td>
        <td width="60%">
            <div class="porsi-box">
                <div class="porsi-label"><b>NOMOR PORSI HAJI</b></div>
                <div class="porsi-value"><b>' . $no_porsi . '</b></div>
            </div>
        </td>
        <td width="20%"></td>
    </tr>
</table>

<br/>

<!-- SECTION I: DATA PRIBADI JAMAAH -->
<div class="section-title"><b>I. DATA PRIBADI JAMAAH</b></div>
<table class="table-data" cellpadding="0" cellspacing="0">
    <tr class="bg-zebra">
        <td class="col-label">Nama Lengkap Jamaah</td>
        <td class="col-colon">:</td>
        <td class="col-val"><b style="font-size: 9.5pt;">' . $nama . '</b></td>
    </tr>
    <tr>
        <td class="col-label">Nomor Induk Kependudukan (NIK)</td>
        <td class="col-colon">:</td>
        <td class="col-val"><b>' . $nik . '</b></td>
    </tr>
    <tr class="bg-zebra">
        <td class="col-label">Tempat, Tanggal Lahir</td>
        <td class="col-colon">:</td>
        <td class="col-val">' . $ttl . ' (' . $usia . ')</td>
    </tr>
    <tr>
        <td class="col-label">Jenis Kelamin</td>
        <td class="col-colon">:</td>
        <td class="col-val">' . $jenkel . '</td>
    </tr>
    <tr class="bg-zebra">
        <td class="col-label">Pendidikan Terakhir</td>
        <td class="col-colon">:</td>
        <td class="col-val">' . $pendidikan . '</td>
    </tr>
    <tr>
        <td class="col-label">Pekerjaan Utama</td>
        <td class="col-colon">:</td>
        <td class="col-val">' . $pekerjaan . '</td>
    </tr>
</table>

<!-- SECTION II: ALAMAT & KONTAK JAMAAH -->
<div class="section-title"><b>II. ALAMAT & KONTAK JAMAAH</b></div>
<table class="table-data" cellpadding="0" cellspacing="0">
    <tr class="bg-zebra">
        <td class="col-label">Alamat Lengkap Domisili</td>
        <td class="col-colon">:</td>
        <td class="col-val">' . $alamat_lengkap . '</td>
    </tr>
    <tr>
        <td class="col-label">Nomor Telepon / WhatsApp</td>
        <td class="col-colon">:</td>
        <td class="col-val"><b>' . $telepon . '</b></td>
    </tr>
</table>

<!-- SECTION III: DATA TRANSAKSI & BANK -->
<div class="section-title"><b>III. DATA TRANSAKSI & BANK (BPS-BPIH)</b></div>
<table class="table-data" cellpadding="0" cellspacing="0">
    <tr class="bg-zebra">
        <td class="col-label">Bank Penerima Setoran (BPS-BPIH)</td>
        <td class="col-colon">:</td>
        <td class="col-val"><b>' . $bank . '</b></td>
    </tr>
    <tr>
        <td class="col-label">Nomor Rekening Bank</td>
        <td class="col-colon">:</td>
        <td class="col-val"><b>' . $no_rek . '</b></td>
    </tr>
    <tr class="bg-zebra">
        <td class="col-label">Nomor Validasi Setoran</td>
        <td class="col-colon">:</td>
        <td class="col-val"><b>' . $no_validasi . '</b></td>
    </tr>
    <tr>
        <td class="col-label">Tanggal Resmi Pendaftaran</td>
        <td class="col-colon">:</td>
        <td class="col-val">' . $tgl_daftar . '</td>
    </tr>
    <tr class="bg-zebra">
        <td class="col-label">Status Verifikasi Berkas</td>
        <td class="col-colon">:</td>
        <td class="col-val">
            <span class="status-badge">
                ' . $status . '
            </span>
        </td>
    </tr>
</table>

<br/><br/>

<!-- AREA TANDA TANGAN -->
<table class="ttd-table" cellpadding="0" cellspacing="0">
    <tr>
        <td width="48%">
            Calon Jamaah Haji,<br/><br/><br/><br/><br/>
            <b>( ' . $nama . ' )</b>
        </td>
        <td width="4%"></td>
        <td width="48%">
            Subulussalam, ' . formatTglIndo(date('Y-m-d')) . '<br/>
            Petugas Pendaftaran PHU,<br/><br/><br/><br/><br/>
            <b>( <u>' . htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['nama_user'] ?? 'Teuku Agung Purnama, S.Kom') . '</u> )</b>
        </td>
    </tr>
</table>
';

// Render PDF ke output
$pdf->writeHTML($html, true, false, true, false, '');

// Bersihkan buffer agar output PDF diterima tanpa hambatan
if (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output('Bukti_Pendaftaran_Haji_' . str_replace(' ', '_', $nama) . '.pdf', 'I');
exit;
