<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('X-Content-Type-Options: nosniff');

function chatRespond(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    chatRespond(405, ['status' => false, 'answer' => 'Gunakan formulir chat untuk mengirim pertanyaan.']);
}

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$now = time();
$recentChatRequests = $_SESSION['chat_assistant_requests'] ?? [];
if (!is_array($recentChatRequests)) $recentChatRequests = [];
$recentChatRequests = array_values(array_filter($recentChatRequests, static function ($time) use ($now) {
    return is_int($time) && $time > ($now - 60);
}));
if (count($recentChatRequests) >= 10) {
    $_SESSION['chat_assistant_requests'] = $recentChatRequests;
    chatRespond(429, ['status' => false, 'answer' => 'Batas pertanyaan sementara tercapai. Silakan coba kembali sebentar lagi.']);
}
$recentChatRequests[] = $now;
$_SESSION['chat_assistant_requests'] = $recentChatRequests;

$rawBody = file_get_contents('php://input');
if ($rawBody === false || strlen($rawBody) > 5000) {
    chatRespond(413, ['status' => false, 'answer' => 'Pesan terlalu panjang. Silakan kirim pertanyaan yang lebih singkat.']);
}
$payload = json_decode($rawBody, true);
$message = trim((string)($payload['message'] ?? ''));
$messageLength = function_exists('mb_strlen') ? mb_strlen($message, 'UTF-8') : strlen($message);
if ($message === '' || $messageLength > 500) {
    chatRespond(422, ['status' => false, 'answer' => 'Tulis pertanyaan maksimal 500 karakter.']);
}

$messageLower = function_exists('mb_strtolower') ? mb_strtolower($message, 'UTF-8') : strtolower($message);
$isStatusQuestion = preg_match('/\b(cek|periksa|lacak|status|data)\b.{0,35}\b(jamaah|jemaah|pendaftaran|porsi|nik)\b|\b(jamaah|jemaah|pendaftaran)\b.{0,35}\b(status|cek|data)\b/iu', $message) === 1
    || preg_match('/\b(nik|no\.?\s*porsi|nomor\s*porsi)\s*[:=]/iu', $message) === 1
    || preg_match('/\bnama(?:\s+lengkap)?\s*[:=]/iu', $message) === 1
    || preg_match('/(?<!\d)\d{16}(?!\d)/', $message) === 1;

if ($isStatusQuestion) {
    $name = '';
    if (preg_match('/(?:nama(?:\s+lengkap)?|atas\s+nama)\s*[:=]\s*(.+?)(?=\s*(?:\||;|,?\s*(?:nik|no\.?\s*porsi|nomor\s*porsi)\s*[:=])|$)/iu', $message, $nameMatch)) {
        $name = trim(preg_replace('/\s+/u', ' ', $nameMatch[1]));
    }

    $nik = '';
    if (preg_match('/\bnik\s*[:=]?\s*(\d{16})\b/iu', $message, $nikMatch)) {
        $nik = $nikMatch[1];
    } elseif (preg_match('/(?<!\d)(\d{16})(?!\d)/', $message, $nikMatch)) {
        $nik = $nikMatch[1];
    }

    $noPorsi = '';
    if (preg_match('/\b(?:nomor\s*porsi|no\.?\s*porsi|porsi)\s*[:=]\s*([A-Za-z0-9][A-Za-z0-9.\/-]{2,49})/iu', $message, $porsiMatch)) {
        $noPorsi = trim($porsiMatch[1]);
    }

    if ($name === '' || (strlen($name) < 3 || strlen($name) > 150) || ($nik === '' && $noPorsi === '')) {
        $missing = [];
        if ($name === '' || strlen($name) < 3 || strlen($name) > 150) $missing[] = 'nama lengkap yang tercatat';
        if ($nik === '' && $noPorsi === '') $missing[] = 'NIK 16 digit atau nomor porsi';
        chatRespond(200, [
            'status' => true,
            'intent' => 'registration_lookup',
            'answer' => "Untuk melindungi data jamaah, saya perlu memverifikasi dua identitas. Mohon kirim nama lengkap dan salah satu nomor berikut:\n\nNama: Nama Lengkap\nNIK: 16 digit\n\natau\n\nNama: Nama Lengkap | No Porsi: nomor porsi\n\nSilakan lengkapi " . implode(' serta ', $missing) . '. Saya hanya akan menampilkan status pendaftaran.'
        ]);
    }

    require_once dirname(__DIR__) . '/config.php';
    try {
        if ($nik !== '') {
            $lookup = $conn->prepare('SELECT status FROM ta_pendaftaran WHERE nama = ? AND nik = ? LIMIT 2');
            if (!$lookup) throw new RuntimeException('Pencarian data tidak dapat disiapkan.');
            $lookup->bind_param('ss', $name, $nik);
        } else {
            $lookup = $conn->prepare('SELECT status FROM ta_pendaftaran WHERE nama = ? AND no_porsi = ? LIMIT 2');
            if (!$lookup) throw new RuntimeException('Pencarian data tidak dapat disiapkan.');
            $lookup->bind_param('ss', $name, $noPorsi);
        }

        if (!$lookup->execute()) throw new RuntimeException('Pencarian data gagal.');
        $lookup->bind_result($registrationStatus);
        $found = $lookup->fetch();
        $matchedStatus = $found ? (string)$registrationStatus : '';
        $ambiguous = $found && $lookup->fetch();
        $lookup->close();
        $conn->close();

        if (!$found || $ambiguous) {
            chatRespond(200, [
                'status' => true,
                'intent' => 'registration_lookup',
                'answer' => 'Data belum dapat diverifikasi. Pastikan nama lengkap dan nomor yang dikirim sama persis dengan data pendaftaran. Untuk menjaga privasi, saya tidak menyebutkan bagian data yang cocok atau tidak cocok.'
            ]);
        }

        $statusText = [
            'pending' => 'Menunggu verifikasi',
            'terverifikasi' => 'Terverifikasi',
            'ditolak' => 'Ditolak'
        ][$matchedStatus] ?? 'Status belum tersedia';
        $nextStep = [
            'pending' => 'Silakan menunggu pemeriksaan petugas. Jika membutuhkan kepastian, hubungi kantor layanan dengan membawa dokumen pendaftaran.',
            'terverifikasi' => 'Pendaftaran sudah terverifikasi. Untuk informasi tahap berikutnya, silakan hubungi petugas layanan.',
            'ditolak' => 'Silakan menghubungi petugas layanan untuk mengetahui tindak lanjut yang dapat dilakukan.'
        ][$matchedStatus] ?? 'Silakan menghubungi petugas layanan untuk informasi lebih lanjut.';

        chatRespond(200, [
            'status' => true,
            'intent' => 'registration_lookup',
            'answer' => "Status pendaftaran: {$statusText}.\n{$nextStep}"
        ]);
    } catch (Throwable $error) {
        error_log('chat_assistant lookup: ' . $error->getMessage());
        if (isset($conn) && $conn instanceof mysqli) $conn->close();
        chatRespond(500, ['status' => false, 'answer' => 'Layanan pengecekan sedang mengalami kendala. Silakan coba lagi nanti atau hubungi petugas.']);
    }
}

$answer = 'Saya dapat membantu menjelaskan layanan haji dan umrah, alur pendaftaran, manasik, atau mengecek status pendaftaran. Untuk cek status, kirim nama lengkap beserta NIK atau nomor porsi.';
$intents = [
    [
        'patterns' => ['/\b(assalamu|assalamualaikum|halo|hai|hi|selamat pagi|selamat siang|selamat sore|selamat malam)\b/iu'],
        'answer' => 'Waalaikumsalam. Saya asisten layanan Kementerian Haji dan Umrah Kota Subulussalam. Saya bisa membantu menjelaskan layanan dan alur pendaftaran, serta mengecek status jamaah dengan verifikasi nama lengkap dan NIK atau nomor porsi.'
    ],
    [
        'patterns' => ['/\b(cek|periksa|lacak)\b.{0,35}\b(status|data|jamaah|jemaah|pendaftaran)\b/iu', '/\b(status\s+(?:jamaah|jemaah|pendaftaran)|cek\s+status)\b/iu', '/\b(nik|no\.?\s*porsi|nomor\s*porsi)\b/iu', '/\bnama\s*[:=]/iu'],
        'answer' => "Untuk melindungi data jamaah, pengecekan memerlukan nama lengkap dan satu nomor identitas yang terdaftar.\n\nContoh:\nNama: Nama Lengkap | NIK: 16 digit\natau\nNama: Nama Lengkap | No Porsi: nomor porsi"
    ],
    [
        'patterns' => ['/\b(syarat|persyaratan|dokumen|berkas)\b/iu'],
        'answer' => 'Persyaratan dapat berbeda menurut jenis layanan dan ketentuan yang berlaku. Siapkan identitas serta dokumen pendukung sesuai pengajuan, lalu konfirmasikan daftar terbaru kepada petugas agar berkas tidak kurang atau kedaluwarsa.'
    ],
    [
        'patterns' => ['/\b(alur|proses|tahapan|cara daftar|pendaftaran)\b/iu'],
        'answer' => 'Alur umum layanan adalah menyiapkan dokumen, mengajukan pendaftaran, menunggu pemeriksaan/verifikasi, lalu mengikuti arahan pembinaan atau manasik. Ketentuan rinci mengikuti jenis layanan. Anda juga dapat menanyakan status pendaftaran dengan nama lengkap dan NIK atau nomor porsi.'
    ],
    [
        'patterns' => ['/\b(biaya|harga|tarif|bayar|setoran)\b/iu'],
        'answer' => 'Biaya dan setoran mengikuti ketentuan resmi serta dapat berubah. Asisten ini tidak menebak angka biaya; mohon konfirmasikan nominal terbaru kepada petugas atau bank penerima setoran yang ditunjuk.'
    ],
    [
        'patterns' => ['/\b(manasik|bimbingan)\b/iu'],
        'answer' => 'Informasi jadwal dan lokasi manasik mengikuti pengumuman petugas untuk kelompok masing-masing. Silakan hubungi kantor layanan untuk jadwal terbaru.'
    ],
    [
        'patterns' => ['/\b(umrah|travel|ppiu)\b/iu'],
        'answer' => 'Landing page ini menyediakan informasi layanan umrah dan dokumen perjalanan. Untuk pendaftaran, jadwal, biaya, atau pemeriksaan legalitas travel, pastikan informasi terbaru dikonfirmasi kepada petugas dan kanal resmi pemerintah.'
    ],
    [
        'patterns' => ['/\b(kontak|alamat|telepon|nomor kantor|jam kerja|hubungi)\b/iu'],
        'answer' => 'Informasi alamat, kontak, dan jam pelayanan tersedia pada bagian “Kontak Kami” di bagian bawah halaman ini. Gunakan kanal resmi tersebut untuk konfirmasi yang membutuhkan petugas.'
    ],
    [
        'patterns' => ['/\b(terima kasih|makasih|thanks)\b/iu'],
        'answer' => 'Sama-sama. Jika ada pertanyaan lain tentang layanan atau status pendaftaran, silakan kirim di sini.'
    ]
];

foreach ($intents as $intent) {
    foreach ($intent['patterns'] as $pattern) {
        if (preg_match($pattern, $messageLower)) {
            $answer = $intent['answer'];
            break 2;
        }
    }
}

chatRespond(200, ['status' => true, 'intent' => 'faq', 'answer' => $answer]);
