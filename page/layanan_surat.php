<?php
session_start();
include '../crypt-start.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../logout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Layanan E-Surat — Kemenhaj</title>
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <link rel="stylesheet" href="../assets/style.css?v=<?= filemtime(__DIR__ . '/../assets/style.css') ?>" />
    <style>
        .mail-page { --mail-green: #0a5c43; }
        .mail-heading { display:flex; justify-content:space-between; align-items:flex-end; gap:18px; margin-bottom:22px; }
        .mail-heading h1 { color:var(--text-main); font-size:24px; font-weight:800; letter-spacing:-.5px; margin:0 0 5px; }
        .mail-heading p { color:var(--text-muted); font-size:13px; margin:0; }
        .mail-heading-icon { width:46px; height:46px; display:grid; place-items:center; border-radius:14px; color:#087653; background:#e8f7f0; font-size:20px; }
        [data-theme="dark"] .mail-heading-icon { background:rgba(16,185,129,.15); color:#6ee7b7; }
        .mail-hero { position:relative; overflow:hidden; padding:25px 28px; border-radius:18px; margin-bottom:20px; color:#fff; background:linear-gradient(115deg,#063f2e 0%,#087653 70%,#13936d 100%); box-shadow:0 12px 28px rgba(6,63,46,.18); }
        .mail-hero::after { content:""; position:absolute; width:230px; height:230px; border:1px solid rgba(255,255,255,.13); border-radius:50%; right:8%; top:-145px; box-shadow:0 0 0 28px rgba(255,255,255,.04),0 0 0 58px rgba(255,255,255,.035); }
        .mail-hero-content { position:relative; z-index:1; display:flex; justify-content:space-between; align-items:center; gap:20px; }
        .mail-hero h2 { font-size:20px; font-weight:800; margin:0 0 7px; }
        .mail-hero p { color:rgba(255,255,255,.76); font-size:12px; margin:0; }
        .mail-hero .btn { font-weight:700; font-size:12px; padding:10px 15px; border-radius:9px; box-shadow:0 6px 15px rgba(0,0,0,.12); white-space:nowrap; }
        .mail-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; margin-bottom:20px; }
        .mail-stat { display:flex; align-items:center; gap:13px; padding:16px; border:1px solid var(--border-color); border-radius:14px; background:var(--bg-card); box-shadow:var(--shadow-sm); }
        .mail-stat-icon { width:42px; height:42px; flex:0 0 42px; display:grid; place-items:center; border-radius:12px; font-size:16px; }
        .mail-stat-icon.in { color:#087653; background:#e8f7f0; }
        .mail-stat-icon.out { color:#2563eb; background:#eff6ff; }
        .mail-stat-icon.wait { color:#b77906; background:#fff7df; }
        .mail-stat-icon.done { color:#7c3aed; background:#f3eefe; }
        [data-theme="dark"] .mail-stat-icon { background:rgba(255,255,255,.08); }
        .mail-stat-label { color:var(--text-muted); font-size:11px; font-weight:600; margin-bottom:3px; }
        .mail-stat-value { color:var(--text-main); font-size:20px; line-height:1.1; font-weight:800; }
        .mail-card { overflow:hidden; border:1px solid var(--border-color); border-radius:16px; background:var(--bg-card); box-shadow:var(--shadow-sm); }
        .mail-tabs-bar { padding:15px 18px 0; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; gap:12px; }
        .mail-tabs { display:flex; gap:8px; border:0; }
        .mail-tabs .nav-link { border:0; border-radius:10px 10px 0 0; padding:12px 16px; color:var(--text-muted); font-size:12px; font-weight:700; background:transparent; }
        .mail-tabs .nav-link.active { color:#087653; background:#eaf7f1; box-shadow:inset 0 -3px #0a805b; }
        [data-theme="dark"] .mail-tabs .nav-link.active { background:rgba(16,185,129,.12); color:#6ee7b7; }
        .mail-toolbar { padding:16px 18px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
        .mail-search { position:relative; flex:1 1 260px; max-width:390px; }
        .mail-search i { position:absolute; top:50%; left:13px; transform:translateY(-50%); color:#94a3b8; font-size:13px; }
        .mail-search input,.mail-toolbar select { height:39px; border:1px solid var(--border-color); border-radius:9px; padding:0 12px; background:var(--bg-card); color:var(--text-main); font:500 12px 'Plus Jakarta Sans',sans-serif; outline:none; }
        .mail-search input { width:100%; padding-left:36px; }
        .mail-search input:focus,.mail-toolbar select:focus { border-color:#0a805b; box-shadow:0 0 0 3px rgba(10,128,91,.1); }
        .mail-toolbar select { min-width:145px; }
        .mail-toolbar-actions { display:flex; gap:8px; align-items:center; }
        .mail-btn-outline { height:39px; padding:0 13px; border:1px solid var(--border-color); border-radius:9px; background:var(--bg-card); color:var(--text-main); font-size:12px; font-weight:700; }
        .mail-btn-outline:hover { background:var(--bg-body); }
        .mail-table-wrap { overflow-x:auto; }
        .mail-table { width:100%; border-collapse:collapse; min-width:760px; }
        .mail-table th { padding:12px 17px; color:var(--text-muted); background:var(--bg-body); font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.55px; white-space:nowrap; }
        .mail-table td { padding:14px 17px; color:var(--text-main); border-top:1px solid var(--border-color); font-size:12px; vertical-align:middle; }
        .mail-table tbody tr:hover { background:rgba(10,128,91,.035); }
        .mail-number { color:#087653; font-weight:800; white-space:nowrap; }
        .mail-subject { max-width:270px; font-weight:700; }
        .mail-subject small { display:block; margin-top:3px; color:var(--text-muted); font-weight:500; }
        .mail-person { display:flex; align-items:center; gap:9px; min-width:155px; }
        .mail-avatar { width:32px; height:32px; display:grid; place-items:center; flex:0 0 32px; border-radius:10px; color:#087653; background:#eaf7f1; font-size:12px; font-weight:800; text-transform:uppercase; }
        .mail-person strong { display:block; font-size:11px; }
        .mail-person small { color:var(--text-muted); font-size:10px; }
        .mail-status { display:inline-flex; align-items:center; gap:6px; padding:5px 9px; border-radius:20px; font-size:10px; font-weight:800; white-space:nowrap; }
        .mail-status::before { content:""; width:6px; height:6px; border-radius:50%; background:currentColor; }
        .mail-status.pending { color:#a16207; background:#fef3c7; }
        .mail-status.process { color:#1d4ed8; background:#dbeafe; }
        .mail-status.done { color:#047857; background:#d1fae5; }
        .mail-status.reject { color:#b91c1c; background:#fee2e2; }
        .mail-actions { display:flex; gap:5px; }
        .mail-action { width:30px; height:30px; display:grid; place-items:center; border:1px solid var(--border-color); border-radius:8px; background:var(--bg-card); color:var(--text-muted); }
        .mail-action:hover { color:#087653; border-color:#a7ddc6; background:#effaf5; }
        .mail-empty { padding:42px 16px !important; text-align:center; color:var(--text-muted) !important; }
        .mail-empty i { display:block; margin-bottom:10px; color:#9ab7aa; font-size:28px; }
        .mail-table-footer { display:flex; justify-content:space-between; padding:13px 18px; border-top:1px solid var(--border-color); color:var(--text-muted); font-size:11px; }
        .mail-modal .modal-content { border:0; border-radius:16px; background:var(--bg-card); color:var(--text-main); box-shadow:0 25px 60px rgba(15,23,42,.25); overflow:hidden; }
        .mail-modal .modal-header { padding:18px 22px; color:white; border:0; background:linear-gradient(110deg,#063f2e,#0b805c); }
        .mail-modal .modal-title { font-size:15px; font-weight:800; }
        .mail-modal .btn-close { filter:invert(1); opacity:.8; }
        .mail-modal .modal-body { padding:22px; }
        .mail-modal .form-label { color:var(--text-main); font-size:11px; font-weight:700; }
        .mail-modal .form-control,.mail-modal .form-select { border-radius:9px; border-color:#d5e0dc; padding:10px 12px; font-size:12px; }
        .mail-modal .form-control:focus,.mail-modal .form-select:focus { border-color:#0a805b; box-shadow:0 0 0 3px rgba(10,128,91,.12); }
        .mail-modal .modal-footer { border-color:var(--border-color); padding:14px 22px; }
        .mail-dropzone { position:relative; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:150px; padding:20px; border:1.5px dashed #9dc9b5; border-radius:13px; background:linear-gradient(135deg,rgba(232,247,240,.72),rgba(248,252,250,.9)); text-align:center; cursor:pointer; transition:.2s ease; }
        .mail-dropzone:hover,.mail-dropzone.is-dragover { border-color:#087653; background:#e8f7f0; transform:translateY(-1px); }
        .mail-dropzone-icon { width:42px; height:42px; display:grid; place-items:center; margin-bottom:9px; border-radius:13px; color:#087653; background:#d8f1e5; font-size:17px; }
        .mail-dropzone strong { color:var(--text-main); font-size:12px; }
        .mail-dropzone small { margin-top:4px; color:var(--text-muted); font-size:10px; }
        .mail-file-list { display:flex; flex-wrap:wrap; gap:7px; margin-top:10px; }
        .mail-file-chip { display:inline-flex; align-items:center; gap:7px; max-width:100%; padding:6px 9px; border:1px solid var(--border-color); border-radius:9px; color:var(--text-main); background:var(--bg-card); font-size:10px; }
        .mail-file-chip span { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .mail-file-chip button { padding:0; border:0; color:#dc2626; background:transparent; }
        .mail-attachment-hint { display:block; margin-top:7px; color:var(--text-muted); font-size:10px; }
        @media(max-width:900px) { .mail-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } }
        @media(max-width:600px) { .mail-heading { align-items:flex-start; } .mail-heading h1 { font-size:20px; } .mail-hero { padding:20px; } .mail-hero-content { align-items:flex-start; flex-direction:column; } .mail-hero h2 { font-size:17px; } .mail-tabs-bar { padding:10px 10px 0; } .mail-tabs .nav-link { padding:11px 10px; font-size:11px; } .mail-toolbar { padding:13px 12px; } .mail-toolbar-actions { width:100%; } .mail-toolbar-actions select { flex:1; } .mail-stats { gap:9px; } .mail-stat { padding:12px 10px; gap:9px; } .mail-stat-icon { width:36px; height:36px; flex-basis:36px; } .mail-stat-value { font-size:17px; } .mail-stat-label { font-size:10px; } }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <main class="main-content" id="mainContent">
        <?php include 'topbar.php'; ?>
        <div class="content-wrapper mail-page">
            <div class="mail-heading">
                <div class="d-flex align-items-center gap-3">
                    <div class="mail-heading-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
                    <div><h1>Layanan E-Surat</h1><p>Kelola surat masuk dan surat keluar dalam satu tempat.</p></div>
                </div>
                <span class="text-muted small"><i class="fa-regular fa-calendar me-1"></i> Arsip surat terpadu</span>
            </div>

            <section class="mail-hero">
                <div class="mail-hero-content">
                    <div><h2><i class="fa-solid fa-inbox me-2"></i>Administrasi surat lebih tertata</h2><p>Pantau surat, tindak lanjut, dan status pengiriman dengan cepat.</p></div>
                    <button class="btn btn-light text-success" id="btnAddMail"><i class="fa-solid fa-plus me-1"></i> Catat Surat Baru</button>
                </div>
            </section>

            <section class="mail-stats" aria-label="Ringkasan surat">
                <div class="mail-stat"><div class="mail-stat-icon in"><i class="fa-solid fa-inbox"></i></div><div><div class="mail-stat-label">Surat Masuk</div><div class="mail-stat-value" id="countIncoming">0</div></div></div>
                <div class="mail-stat"><div class="mail-stat-icon out"><i class="fa-solid fa-paper-plane"></i></div><div><div class="mail-stat-label">Surat Keluar</div><div class="mail-stat-value" id="countOutgoing">0</div></div></div>
                <div class="mail-stat"><div class="mail-stat-icon wait"><i class="fa-regular fa-clock"></i></div><div><div class="mail-stat-label">Perlu Tindak Lanjut</div><div class="mail-stat-value" id="countPending">0</div></div></div>
                <div class="mail-stat"><div class="mail-stat-icon done"><i class="fa-solid fa-circle-check"></i></div><div><div class="mail-stat-label">Selesai Bulan Ini</div><div class="mail-stat-value" id="countDone">0</div></div></div>
                <div class="mail-stat"><div class="mail-stat-icon out"><i class="fa-solid fa-arrows-rotate"></i></div><div><div class="mail-stat-label">Sedang Diproses</div><div class="mail-stat-value" id="countProcess">0</div></div></div>
                <div class="mail-stat"><div class="mail-stat-icon wait"><i class="fa-solid fa-circle-exclamation"></i></div><div><div class="mail-stat-label">Ditolak</div><div class="mail-stat-value" id="countRejected">0</div></div></div>
            </section>

            <section class="mail-card">
                <div class="mail-tabs-bar">
                    <ul class="nav nav-tabs mail-tabs" id="mailTabs" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-kind="masuk" type="button"><i class="fa-solid fa-inbox me-2"></i>Surat Masuk <span class="badge rounded-pill text-bg-light ms-1" id="badgeMasuk">0</span></button></li>
                        <li class="nav-item"><button class="nav-link" data-kind="keluar" type="button"><i class="fa-solid fa-paper-plane me-2"></i>Surat Keluar <span class="badge rounded-pill text-bg-light ms-1" id="badgeKeluar">0</span></button></li>
                    </ul>
                </div>
                <div class="mail-toolbar">
                    <div class="mail-search"><i class="fa-solid fa-magnifying-glass"></i><input type="search" id="mailSearch" placeholder="Cari nomor, perihal, atau nama..." aria-label="Cari surat"></div>
                    <div class="mail-toolbar-actions">
                        <select id="statusFilter" aria-label="Filter status"><option value="">Semua Status</option><option>Perlu Tindak Lanjut</option><option>Diproses</option><option>Selesai</option><option>Ditolak</option></select>
                        <button class="mail-btn-outline" id="btnExport"><i class="fa-solid fa-download me-1"></i> Ekspor CSV</button>
                    </div>
                </div>
                <div class="mail-table-wrap">
                    <table class="mail-table">
                        <thead><tr><th>Nomor Surat</th><th>Pengirim / Penerima</th><th>Perihal</th><th>Tanggal Surat</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody id="mailRows"></tbody>
                    </table>
                </div>
                <div class="mail-table-footer"><span id="mailCountLabel">Menampilkan 0 surat</span><span><i class="fa-solid fa-shield-halved me-1"></i> Arsip administrasi</span></div>
            </section>
        </div>
    </main>

    <div class="modal fade mail-modal" id="mailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
            <form id="mailForm">
                <div class="modal-header"><h5 class="modal-title" id="mailModalTitle"><i class="fa-solid fa-file-circle-plus me-2"></i>Catat Surat Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body">
                    <input type="hidden" id="mailId">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="mailKind">Jenis Surat <span class="text-danger">*</span></label><select class="form-select" id="mailKind" required><option value="masuk">Surat Masuk</option><option value="keluar">Surat Keluar</option></select></div>
                        <div class="col-md-6"><label class="form-label" for="mailNumber">Nomor Surat <span class="text-danger">*</span></label><input class="form-control" id="mailNumber" required placeholder="Contoh: 005/SET-KH/2026"></div>
                        <div class="col-md-6"><label class="form-label" id="partyLabel" for="mailParty">Pengirim <span class="text-danger">*</span></label><input class="form-control" id="mailParty" required placeholder="Nama instansi atau pihak terkait"></div>
                        <div class="col-md-6"><label class="form-label" for="mailDate">Tanggal Surat <span class="text-danger">*</span></label><div class="input-group"><span class="input-group-text bg-light text-success"><i class="fa-regular fa-calendar-days"></i></span><input type="text" class="form-control" id="mailDate" required placeholder="Pilih tanggal surat..." autocomplete="off"><button class="btn btn-outline-secondary" type="button" id="mailDateButton" aria-label="Buka kalender"><i class="fa-regular fa-calendar"></i></button></div></div>
                        <div class="col-md-12"><label class="form-label" for="mailSubject">Perihal <span class="text-danger">*</span></label><textarea class="form-control" id="mailSubject" rows="2" required placeholder="Tuliskan perihal surat"></textarea></div>
                        <div class="col-md-6"><label class="form-label" for="mailStatus">Status</label><select class="form-select" id="mailStatus"><option>Perlu Tindak Lanjut</option><option>Diproses</option><option>Selesai</option><option>Ditolak</option></select></div>
                        <div class="col-md-6"><label class="form-label" for="mailNote">Catatan (opsional)</label><input class="form-control" id="mailNote" placeholder="Catatan disposisi atau pengiriman"></div>
                        <div class="col-12"><label class="form-label">Lampiran Surat</label><div class="mail-dropzone" id="mailDropzone" role="button" tabindex="0" aria-label="Pilih atau jatuhkan file lampiran surat"><span class="mail-dropzone-icon"><i class="fa-solid fa-cloud-arrow-up"></i></span><strong>Tarik dan lepaskan file di sini</strong><small>atau klik untuk memilih · PDF, Word, Excel, atau gambar · maks. 15 MB per file</small><input type="file" id="mailFiles" hidden multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"><div class="mail-file-list" id="mailFileList"></div></div><span class="mail-attachment-hint"><i class="fa-solid fa-link me-1"></i>File tersimpan di server dan dapat diunduh siapa pun yang memiliki tautan berbagi.</span></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-success" style="background:#0a5c43;border-color:#0a5c43"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan</button></div>
            </form>
        </div></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script>
        window.addEventListener('load', () => {
            let records = [];
            let databaseSummary = { total:0, masuk:0, keluar:0, perlu_tindak_lanjut:0, selesai_bulan_ini:0, diproses:0, ditolak:0 };
            let activeKind = 'masuk';
            const modal = new bootstrap.Modal(document.getElementById('mailModal'));
            const $ = (id) => document.getElementById(id);
            let pendingFiles = [];
            let savedAttachments = [];
            let removedAttachments = [];
            let attachmentDbPromise;
            const mailDatePicker = flatpickr('#mailDate', {
                locale: 'id',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                allowInput: true,
                disableMobile: true
            });
            mailDatePicker.input.required = false;
            mailDatePicker.altInput.required = true;
            const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
            const fmtDate = (value) => value ? new Date(value + 'T00:00:00').toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '-';
            const downloadUrl = (file) => {
                const query = file.token
                    ? `token=${encodeURIComponent(file.token)}`
                    : `id=${encodeURIComponent(file.id)}`;
                return new URL(`../api/download_lampiran_surat.php?${query}`, window.location.href).href;
            };
            const statusClass = (status) => ({'Perlu Tindak Lanjut':'pending','Diproses':'process','Selesai':'done','Ditolak':'reject'}[status] || 'pending');
            function attachmentDb() {
                if (!attachmentDbPromise) attachmentDbPromise = new Promise((resolve, reject) => {
                    const request = indexedDB.open('eSuratLampiran', 1);
                    request.onupgradeneeded = () => { if (!request.result.objectStoreNames.contains('files')) request.result.createObjectStore('files', {keyPath:'id'}); };
                    request.onsuccess = () => resolve(request.result); request.onerror = () => reject(request.error);
                });
                return attachmentDbPromise;
            }
            async function saveAttachment(recordId, file) {
                const id = `${recordId}-${Date.now()}-${Math.random().toString(36).slice(2,8)}`;
                const db = await attachmentDb();
                await new Promise((resolve, reject) => { const tx = db.transaction('files','readwrite'); tx.objectStore('files').put({id,recordId,name:file.name,type:file.type,blob:file}); tx.oncomplete=resolve; tx.onerror=()=>reject(tx.error); });
                return {id,name:file.name,type:file.type};
            }
            async function deleteAttachment(id) {
                const db = await attachmentDb();
                await new Promise((resolve,reject)=>{const tx=db.transaction('files','readwrite');tx.objectStore('files').delete(id);tx.oncomplete=resolve;tx.onerror=()=>reject(tx.error);});
            }
            async function getAttachmentFiles(recordId) {
                const db = await attachmentDb();
                return new Promise((resolve,reject)=>{const tx=db.transaction('files','readonly');const req=tx.objectStore('files').getAll();req.onsuccess=()=>resolve(req.result.filter((file)=>file.recordId===recordId));req.onerror=()=>reject(req.error);});
            }
            function renderFileList() {
                const entries = [...savedAttachments.filter((file)=>!removedAttachments.includes(file.id)).map((file)=>({name:file.name,id:file.id,saved:true})), ...pendingFiles.map((file,index)=>({name:file.name,id:String(index),saved:false}))];
                $('mailFileList').innerHTML = entries.map((file)=>`<span class="mail-file-chip"><i class="fa-regular fa-file-lines text-success"></i><span>${esc(file.name)}</span><button type="button" data-file-id="${esc(file.id)}" data-saved="${file.saved}" aria-label="Hapus lampiran"><i class="fa-solid fa-xmark"></i></button></span>`).join('');
            }
            function addFiles(fileList) {
                const allowed = /\.(pdf|docx?|xlsx?|png|jpe?g)$/i;
                const incoming = Array.from(fileList || []);
                const invalid = incoming.find((file)=>!allowed.test(file.name) || file.size > 15*1024*1024);
                if (invalid) { Swal.fire({icon:'warning',title:'File tidak dapat ditambahkan',text:`${invalid.name}: gunakan PDF, Word, Excel, atau gambar maksimal 15 MB.`}); return; }
                const available = Math.max(0, 8 - savedAttachments.filter((file)=>!removedAttachments.includes(file.id)).length - pendingFiles.length);
                pendingFiles.push(...incoming.slice(0,available)); renderFileList();
                if (incoming.length > available) Swal.fire({icon:'info',title:'Batas lampiran',text:'Maksimal 8 file untuk setiap surat.'});
            }
            function render() {
                const query = $('mailSearch').value.trim().toLocaleLowerCase('id-ID');
                const status = $('statusFilter').value;
                const filtered = records.filter((item) => item.kind === activeKind && (!status || item.status === status) && (!query || [item.number,item.party,item.subject,item.note].some((value) => String(value || '').toLocaleLowerCase('id-ID').includes(query))));
                $('mailRows').innerHTML = filtered.length ? filtered.map((item) => `<tr>
                    <td><span class="mail-number">${esc(item.number)}</span></td>
                    <td><div class="mail-person"><span class="mail-avatar">${esc((item.party || '?').slice(0,2))}</span><span><strong>${esc(item.party)}</strong><small>${item.kind === 'masuk' ? 'Asal surat' : 'Tujuan surat'}</small></span></div></td>
                    <td><div class="mail-subject">${esc(item.subject)}${item.note ? `<small>${esc(item.note)}</small>` : ''}${item.attachments?.length ? `<small><i class="fa-solid fa-paperclip me-1"></i>${item.attachments.length} lampiran</small>` : ''}</div></td>
                    <td>${fmtDate(item.date)}</td><td><span class="mail-status ${statusClass(item.status)}">${esc(item.status)}</span></td>
                    <td><div class="mail-actions"><button class="mail-action" data-action="share" data-id="${esc(item.id)}" title="Bagikan surat"><i class="fa-solid fa-share-nodes"></i></button><button class="mail-action" data-action="view" data-id="${esc(item.id)}" title="Lihat detail"><i class="fa-regular fa-eye"></i></button><button class="mail-action" data-action="edit" data-id="${esc(item.id)}" title="Ubah"><i class="fa-regular fa-pen-to-square"></i></button><button class="mail-action" data-action="delete" data-id="${esc(item.id)}" title="Hapus"><i class="fa-regular fa-trash-can"></i></button></div></td>
                </tr>`).join('') : `<tr><td class="mail-empty" colspan="6"><i class="fa-regular fa-folder-open"></i>Tidak ada surat yang cocok dengan pencarian.</td></tr>`;
                $('countIncoming').textContent = databaseSummary.masuk;
                $('countOutgoing').textContent = databaseSummary.keluar;
                $('countPending').textContent = databaseSummary.perlu_tindak_lanjut;
                $('countDone').textContent = databaseSummary.selesai_bulan_ini;
                $('countProcess').textContent = databaseSummary.diproses;
                $('countRejected').textContent = databaseSummary.ditolak;
                $('badgeMasuk').textContent = databaseSummary.masuk;
                $('badgeKeluar').textContent = databaseSummary.keluar;
                const kindTotal = activeKind === 'masuk' ? databaseSummary.masuk : databaseSummary.keluar;
                $('mailCountLabel').textContent = `Menampilkan ${filtered.length} dari ${kindTotal} surat`;
            }
            function loadRecords() {
                return jQuery.ajax({url:'../api/get_surat.php',method:'GET',dataType:'json',cache:false})
                    .done((response) => {
                        if (!response || response.status !== true) {
                            records = [];
                            databaseSummary = {total:0,masuk:0,keluar:0,perlu_tindak_lanjut:0,selesai_bulan_ini:0,diproses:0,ditolak:0};
                            render();
                            Swal.fire({icon:'error',title:'Gagal memuat surat',text:response?.message || 'Respons data surat tidak valid.'});
                            return;
                        }
                        records = Array.isArray(response.data) ? response.data : [];
                        databaseSummary = response.summary || databaseSummary;
                        render();
                    })
                    .fail((xhr) => {
                        records = [];
                        databaseSummary = {total:0,masuk:0,keluar:0,perlu_tindak_lanjut:0,selesai_bulan_ini:0,diproses:0,ditolak:0};
                        render();
                        const message = xhr.responseJSON?.message || 'Data surat dari database tidak dapat dimuat.';
                        Swal.fire({icon:'error',title:'Gagal memuat surat',text:message});
                    });
            }
            function setKind(kind) {
                activeKind = kind;
                document.querySelectorAll('#mailTabs .nav-link').forEach((tab) => tab.classList.toggle('active', tab.dataset.kind === kind));
                render();
            }
            function openForm(item = null) {
                $('mailForm').reset(); $('mailId').value = item?.id || '';
                $('mailKind').value = item?.kind || activeKind; $('mailNumber').value = item?.number || '';
                $('mailParty').value = item?.party || ''; $('mailSubject').value = item?.subject || '';
                mailDatePicker.setDate(item?.date || new Date(), true);
                $('mailStatus').value = item?.status || 'Perlu Tindak Lanjut'; $('mailNote').value = item?.note || '';
                pendingFiles = []; savedAttachments = item?.attachments || []; removedAttachments = []; renderFileList();
                $('mailModalTitle').innerHTML = `<i class="fa-solid fa-file-circle-${item ? 'check' : 'plus'} me-2"></i>${item ? 'Ubah Data Surat' : 'Catat Surat Baru'}`;
                updatePartyLabel(); modal.show();
            }
            function updatePartyLabel() {
                $('partyLabel').innerHTML = `${$('mailKind').value === 'masuk' ? 'Pengirim' : 'Penerima'} <span class="text-danger">*</span>`;
                $('mailParty').placeholder = $('mailKind').value === 'masuk' ? 'Nama instansi atau pihak pengirim' : 'Nama instansi atau pihak penerima';
            }
            document.querySelectorAll('#mailTabs .nav-link').forEach((tab) => tab.addEventListener('click', () => setKind(tab.dataset.kind)));
            $('mailSearch').addEventListener('input', render); $('statusFilter').addEventListener('change', render);
            $('btnAddMail').addEventListener('click', () => openForm()); $('mailKind').addEventListener('change', updatePartyLabel);
            $('mailDateButton').addEventListener('click', () => mailDatePicker.open());
            $('mailDropzone').addEventListener('click', (event) => { if (event.target !== $('mailFiles') && !event.target.closest('[data-file-id]')) $('mailFiles').click(); });
            $('mailDropzone').addEventListener('keydown', (event) => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); $('mailFiles').click(); } });
            $('mailDropzone').addEventListener('dragover', (event) => { event.preventDefault(); $('mailDropzone').classList.add('is-dragover'); });
            $('mailDropzone').addEventListener('dragleave', (event) => { if (!$('mailDropzone').contains(event.relatedTarget)) $('mailDropzone').classList.remove('is-dragover'); });
            $('mailDropzone').addEventListener('drop', (event) => { event.preventDefault(); $('mailDropzone').classList.remove('is-dragover'); addFiles(event.dataTransfer.files); });
            $('mailFiles').addEventListener('change', (event) => { addFiles(event.target.files); event.target.value = ''; });
            $('mailFileList').addEventListener('click', (event) => {
                const button = event.target.closest('[data-file-id]'); if (!button) return;
                if (button.dataset.saved === 'true') removedAttachments.push(button.dataset.fileId);
                else pendingFiles.splice(Number(button.dataset.fileId),1);
                renderFileList();
            });
            $('mailForm').addEventListener('submit', async (event) => {
                event.preventDefault();
                const id = $('mailId').value || `mail-${Date.now()}`;
                const oldItem = records.find((record) => record.id === id);
                const isServerUpdate = Boolean(oldItem?.serverId);
                const item = { id, kind:$('mailKind').value, number:$('mailNumber').value.trim(), party:$('mailParty').value.trim(), subject:$('mailSubject').value.trim(), date:$('mailDate').value, status:$('mailStatus').value, note:$('mailNote').value.trim(), attachments:savedAttachments.filter((file)=>!removedAttachments.includes(file.id)) };
                const formData = new FormData();
                formData.append('aksi', isServerUpdate ? 'ubah' : 'tambah');
                formData.append('id', isServerUpdate ? oldItem.serverId : '');
                formData.append('jenis_surat', item.kind);
                formData.append('nomor_surat', item.number);
                formData.append('pengirim_penerima', item.party);
                formData.append('tanggal_surat', item.date);
                formData.append('perihal', item.subject);
                formData.append('status', item.status);
                formData.append('catatan', item.note);
                item.attachments.forEach((file) => formData.append('lampiran_lama[]', file.name));
                removedAttachments.forEach((attachmentId) => {
                    const file = savedAttachments.find((attachment) => attachment.id === attachmentId);
                    if (file) formData.append('hapus_lampiran[]', file.name);
                });
                pendingFiles.forEach((file) => formData.append('lampiran[]', file, file.name));

                const submitButton = $('mailForm').querySelector('[type="submit"]');
                const originalButtonHtml = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Mengirim...';
                jQuery.ajax({
                    url: '../api/proses_surat.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: async function(response) {
                        if (!response || (response.status !== true && response.success !== true)) {
                            Swal.fire({icon:'error',title:'Surat gagal disimpan',text:response?.message || 'Backend tidak mengonfirmasi penyimpanan surat.'});
                            return;
                        }
                        item.serverId = String(response.data?.id || response.id || item.id);
                        item.id = item.serverId;
                        try {
                            for (const attachmentId of removedAttachments) await deleteAttachment(attachmentId);
                            for (const file of pendingFiles) item.attachments.push(await saveAttachment(item.id,file));
                        } catch (_) {
                            // Server sudah menyimpan surat; lampiran tetap terkirim melalui AJAX.
                        }
                        modal.hide();
                        await loadRecords();
                        setKind(item.kind);
                        Swal.fire({icon:'success',title:'Surat berhasil disimpan',text:response.message || 'Data surat telah dikirim ke server.',timer:1800,showConfirmButton:false});
                    },
                    error: function(xhr, status, error) {
                        let message = 'Tidak dapat menghubungi backend proses surat.';
                        if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
                        else if (xhr.responseText) {
                            try { message = JSON.parse(xhr.responseText).message || message; } catch (_) {}
                        }
                        console.error('AJAX proses surat gagal:', status, error);
                        Swal.fire({icon:'error',title:'Gagal mengirim surat',text:message});
                    },
                    complete: function() {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonHtml;
                    }
                });
            });
            $('mailRows').addEventListener('click', async (event) => {
                const button = event.target.closest('[data-action]'); if (!button) return;
                const item = records.find((record) => record.id === button.dataset.id); if (!item) return;
                if (button.dataset.action === 'edit') return openForm(item);
                if (button.dataset.action === 'share') {
                    const type = item.kind === 'masuk' ? 'Surat Masuk' : 'Surat Keluar';
                    const attachmentLinks = (item.attachments || []).map((file) => ({
                        name: file.name,
                        url: downloadUrl(file)
                    }));
                    const details = `${type}\nNomor: ${item.number}\n${item.kind === 'masuk' ? 'Pengirim' : 'Penerima'}: ${item.party}\nPerihal: ${item.subject}\nTanggal: ${fmtDate(item.date)}\nStatus: ${item.status}${item.note ? `\nCatatan: ${item.note}` : ''}${attachmentLinks.length ? `\n\nLink unduh lampiran:\n${attachmentLinks.map((file) => `${file.name}: ${file.url}`).join('\n')}` : '\n\nSurat ini tidak memiliki lampiran.'}`;
                    if (!attachmentLinks.length) {
                        Swal.fire({icon:'info',title:'Tidak ada file lampiran',text:'Surat ini belum memiliki file yang dapat dibagikan.'});
                        return;
                    }
                    if (navigator.share) {
                        try { await navigator.share({title:`${type} — ${item.number}`,text:details}); }
                        catch (error) { if (error.name !== 'AbortError') Swal.fire({icon:'error',title:'Gagal membagikan link',text:'Browser tidak dapat membuka fitur berbagi.'}); }
                    } else {
                        try {
                            await navigator.clipboard.writeText(details);
                            Swal.fire({icon:'success',title:'Link file disalin',text:'Kirim rincian yang disalin kepada penerima.'});
                        } catch (_) {
                            Swal.fire({title:'Link unduh file',html:`<div class="text-start small">${attachmentLinks.map((file)=>`<p><a href="${esc(file.url)}" target="_blank" rel="noopener">${esc(file.name)}</a><br><input class="form-control form-control-sm mt-1" readonly value="${esc(file.url)}"></p>`).join('')}</div>`,confirmButtonColor:'#0a5c43'});
                        }
                    }
                    return;
                }
                if (button.dataset.action === 'view') {
                    const attachmentHtml = item.attachments?.length ? `<hr><p><b>Lampiran:</b></p>${item.attachments.map((file)=>`<a class="btn btn-sm btn-outline-success me-1 mb-1" href="${esc(downloadUrl(file))}" target="_blank" rel="noopener"><i class="fa-solid fa-download me-1"></i>${esc(file.name)}</a>`).join('')}` : '<p><b>Lampiran:</b> -</p>';
                    await Swal.fire({title:'Detail Surat',html:`<div class="text-start small"><p><b>Jenis:</b> ${item.kind === 'masuk' ? 'Surat Masuk' : 'Surat Keluar'}</p><p><b>Nomor:</b> ${esc(item.number)}</p><p><b>${item.kind === 'masuk' ? 'Pengirim' : 'Penerima'}:</b> ${esc(item.party)}</p><p><b>Perihal:</b> ${esc(item.subject)}</p><p><b>Tanggal:</b> ${fmtDate(item.date)}</p><p><b>Status:</b> ${esc(item.status)}</p><p><b>Catatan:</b> ${esc(item.note || '-')}</p>${attachmentHtml}</div>`,confirmButtonColor:'#0a5c43'});
                    return;
                }
                const answer = await Swal.fire({title:'Hapus surat ini?',text:`${item.number} — ${item.subject}`,icon:'warning',showCancelButton:true,confirmButtonText:'Ya, hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'});
                if (answer.isConfirmed) {
                    jQuery.ajax({
                        url:'../api/proses_surat.php', method:'POST', dataType:'json',
                        data:{aksi:'hapus',id:item.serverId || item.id},
                        success: async (response) => {
                            if (!response || response.status !== true) return Swal.fire({icon:'error',title:'Surat gagal dihapus',text:response?.message || 'Server tidak mengonfirmasi penghapusan.'});
                            for (const file of item.attachments || []) await deleteAttachment(file.id).catch(()=>{});
                            await loadRecords();
                            Swal.fire({icon:'success',title:'Surat dihapus',text:response.message || 'Data surat berhasil dihapus.',timer:1600,showConfirmButton:false});
                        },
                        error: (xhr) => Swal.fire({icon:'error',title:'Gagal menghapus surat',text:xhr.responseJSON?.message || 'Tidak dapat menghubungi backend.'})
                    });
                }
            });
            $('btnExport').addEventListener('click', () => {
                const rows = records.filter((item) => item.kind === activeKind);
                const quote = (value) => `"${String(value ?? '').replace(/"/g,'""')}"`;
                const csv = ['Nomor Surat,Jenis,Pengirim atau Penerima,Perihal,Tanggal,Status,Catatan', ...rows.map((item) => [item.number,item.kind === 'masuk' ? 'Surat Masuk' : 'Surat Keluar',item.party,item.subject,item.date,item.status,item.note].map(quote).join(','))].join('\r\n');
                const link = document.createElement('a'); link.href = URL.createObjectURL(new Blob(['\ufeff' + csv],{type:'text/csv;charset=utf-8'})); link.download = `arsip-surat-${activeKind}-${new Date().toISOString().slice(0,10)}.csv`; link.click(); URL.revokeObjectURL(link.href);
            });
            render();
            loadRecords();
        });
    </script>
        <?php include 'footer.php'; ?>
</body>
</html>
