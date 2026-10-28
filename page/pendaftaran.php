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
    <title>Pendaftaran Porsi Haji — Kemenhaj</title>
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <link rel="shortcut icon" href="../logo.png" type="image/x-icon">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" />

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- DateRangePicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />

    <!-- Custom Global CSS -->
    <link rel="stylesheet" href="../assets/style.css?v=<?= filemtime(__DIR__ . '/../assets/style.css') ?>" />

    <!-- Custom CSS Khusus Halaman Ini & Form Keren -->
    <style>
        body {
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* --- FIX DROPDOWN & OVERFLOW --- */
        .table-responsive,
        .dataTables_wrapper,
        .table-custom-container {
            overflow: visible !important;
        }

        /* Pastikan dropdown menu melayang paling atas */
        .dropdown-menu {
            z-index: 99999 !important;
        }

        /* --- STYLING TABEL KUSTOM (BORDERED & HEADER PRIMARY) --- */
        .table-custom-container {
            border: 1px solid #dedede;
            border-radius: 12px;
            background: var(--bg-card);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            padding: 20px;
        }

        .table-custom {
            border-collapse: separate;
            border-spacing: 0;
            width: 100% !important;
            border: 1px solid #dedede;
            margin-bottom: 0 !important;
        }

        .table-custom thead th {
            background-color: var(--primary) !important;
            color: #ffffff !important;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dedede !important;
            border-right: 1px solid #dedede !important;
            border-top: none !important;
            padding: 14px 16px !important;
            font-weight: 700;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table-custom thead th:last-child {
            border-right: none !important;
        }

        .table-custom tbody td {
            padding: 12px 16px !important;
            vertical-align: middle;
            color: var(--text-main);
            border-bottom: 1px solid #dedede !important;
            border-right: 1px solid #dedede !important;
            font-size: 13px;
            font-weight: 500;
            background-color: var(--bg-card);
        }

        .table-custom tbody td:last-child {
            border-right: none !important;
        }

        .table-custom tbody tr:last-child td {
            border-bottom: none !important;
        }

        .table-custom.table-hover tbody tr:hover td {
            background-color: rgba(18, 140, 100, 0.05) !important;
        }

        /* Flatpickr */
        .flatpickr-calendar {
            z-index: 20000 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
            border: 1px solid var(--border-color) !important;
            font-family: inherit;
            font-size: 13px;
        }

        .flatpickr-months .flatpickr-month {
            background: var(--primary) !important;
            color: #fff !important;
            border-radius: 12px 12px 0 0;
            fill: #fff !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background: var(--primary) !important;
            color: #fff !important;
            font-weight: 700;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months .flatpickr-monthDropdown-month {
            background-color: var(--bg-card) !important;
            color: var(--text-main) !important;
        }

        .flatpickr-current-month input.cur-year {
            color: #fff !important;
            font-weight: 800;
        }

        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            color: #fff !important;
            fill: #fff !important;
        }

        .flatpickr-weekdays {
            background: var(--primary) !important;
        }

        span.flatpickr-weekday {
            background: var(--primary) !important;
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 600;
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected:hover {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        .autocomplete-wrap {
            position: relative;
        }

        .autocomplete-results {
            position: absolute;
            inset: calc(100% + 4px) 0 auto 0;
            z-index: 30000;
            display: none;
            max-height: 230px;
            overflow-y: auto;
            padding: 5px;
            background: var(--bg-card, #fff);
            border: 1px solid var(--border-color, #dbe2ea);
            border-radius: 10px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .16);
        }

        .autocomplete-item {
            display: block;
            width: 100%;
            border: 0;
            border-radius: 7px;
            padding: 9px 11px;
            text-align: left;
            background: transparent;
            color: var(--text-main, #1f2937);
            font: inherit;
            cursor: pointer;
        }

        .autocomplete-item:hover,
        .autocomplete-item:focus {
            background: rgba(18, 140, 100, .10);
            outline: none;
        }

        .autocomplete-message {
            padding: 9px 11px;
            color: var(--text-muted, #64748b);
            font-size: 12px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 4px;
            letter-spacing: -0.3px;
        }

        .page-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0;
        }

        .btn-tambah {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(18, 140, 100, 0.3);
        }

        .btn-tambah:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(18, 140, 100, 0.4);
            color: #fff;
        }

        .btn-excel {
            background: linear-gradient(135deg, #1d6f42, #157a45);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(29, 111, 66, 0.3);
        }

        .btn-excel:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(29, 111, 66, 0.4);
            color: #fff;
        }

        .badge-status {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 11.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-terverifikasi {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .badge-pending {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-ditolak {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        [data-theme="dark"] .badge-terverifikasi {
            background: rgba(4, 120, 87, 0.2);
            color: #34d399;
            border-color: rgba(4, 120, 87, 0.3);
        }

        [data-theme="dark"] .badge-pending {
            background: rgba(180, 83, 9, 0.2);
            color: #fbbf24;
            border-color: rgba(180, 83, 9, 0.3);
        }

        [data-theme="dark"] .badge-ditolak {
            background: rgba(185, 28, 28, 0.2);
            color: #f87171;
            border-color: rgba(185, 28, 28, 0.3);
        }

        .action-dropdown .dropdown-toggle {
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 5px rgba(59, 130, 246, 0.3);
            transition: var(--transition);
        }

        .action-dropdown .dropdown-toggle:hover,
        .action-dropdown .dropdown-toggle:focus,
        .action-dropdown.show .dropdown-toggle {
            background: #2563eb;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.4);
            color: #fff;
        }

        .action-dropdown .dropdown-toggle::after {
            display: none;
        }

        .action-dropdown {
            position: relative;
        }

        .action-dropdown .dropdown-menu {
            position: absolute;
            inset: auto 0 auto auto;
            margin-top: 4px;
        }

        .action-dropdown .dropdown-menu.dropdown-menu-up {
            top: auto;
            bottom: 100%;
            margin-top: 0;
            margin-bottom: 4px;
        }

        .action-dropdown .dropdown-menu {
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 8px;
            min-width: 160px;
            background: var(--bg-card);
        }

        .action-dropdown .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-main);
            border-radius: 8px;
            transition: var(--transition);
            cursor: pointer;
        }

        .action-dropdown .dropdown-item i {
            font-size: 15px;
            width: 20px;
            text-align: center;
        }

        .action-dropdown .dropdown-item.edit i {
            color: #3b82f6;
        }

        .action-dropdown .dropdown-item.cetak-pendaftaran i {
            color: #f59e0b;
        }

        .action-dropdown .dropdown-item.cetak-f i {
            color: #10b981;
        }

        .action-dropdown .dropdown-item.delete i {
            color: #ef4444;
        }

        .action-dropdown .dropdown-item:hover.edit {
            color: #3b82f6;
            background: #eff6ff;
        }

        .action-dropdown .dropdown-item:hover.cetak-d {
            color: #f59e0b;
            background: #fffbeb;
        }

        .action-dropdown .dropdown-item:hover.cetak-f {
            color: #10b981;
            background: #ecfdf5;
        }

        .action-dropdown .dropdown-item:hover.delete {
            color: #ef4444;
            background: #fef2f2;
        }

        .action-dropdown .dropdown-divider {
            margin: 6px 0;
            border-color: var(--border-color);
        }

        /* --- STYLING MODAL FORM SANGAT KEREN & INTERAKTIF --- */
        #modalForm .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            background: var(--bg-card, #ffffff);
            overflow: hidden;
        }

        #modalForm .modal-header {
            background: linear-gradient(135deg, var(--primary, #0a5c43), #047857);
            padding: 20px 28px;
            border-bottom: none;
        }

        #modalForm .modal-title {
            color: #ffffff !important;
            font-weight: 800;
            font-size: 17px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #modalForm .btn-close {
            filter: invert(1) brightness(200%);
            opacity: 0.8;
        }

        #modalForm .modal-body {
            padding: 26px 28px;
            background-color: var(--bg-body, #f8fafc);
            max-height: 75vh;
            overflow-y: auto;
        }

        .form-card-section {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }

        .form-card-section:hover {
            border-color: #cbd5e1;
        }

        .section-header-title {
            font-size: 13px;
            font-weight: 800;
            color: var(--primary, #0a5c43);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecfdf5;
        }

        .section-header-title i {
            font-size: 15px;
            background: #ecfdf5;
            color: #047857;
            padding: 7px;
            border-radius: 8px;
        }

        /* INPUT GROUP DENGAN ICON PRO */
        #modalForm .input-group-text {
            background-color: #f1f5f9;
            border: 1.5px solid #cbd5e1;
            border-right: none;
            color: var(--primary, #0a5c43);
            border-radius: 10px 0 0 10px;
            font-size: 13px;
        }

        #modalForm .input-group .form-control,
        #modalForm .input-group .form-select {
            border-radius: 0 10px 10px 0 !important;
        }

        #modalForm .input-group .input-group-text-right {
            border-left: none;
            border-right: 1.5px solid #cbd5e1;
            border-radius: 0 10px 10px 0 !important;
            cursor: pointer;
        }

        #modalForm .input-group .form-control-with-right-icon {
            border-radius: 0 !important;
        }

        #modalForm .form-control,
        #modalForm .form-select {
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 9px 13px;
            font-size: 13px;
            color: #0f172a;
            font-weight: 500;
            transition: all 0.2s;
        }

        #modalForm .form-control:focus,
        #modalForm .form-select:focus {
            border-color: #059669;
            box-shadow: 0 0 0 3.5px rgba(5, 150, 105, 0.15);
        }

        .form-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .form-control:disabled,
        .form-control[readonly] {
            background-color: var(--bg-body) !important;
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Samakan Select2 dengan tinggi dan susunan input di dalam input-group. */
        #modalForm .input-group {
            flex-wrap: nowrap;
        }

        #modalForm .input-group > .select2-container--bootstrap-5 {
            flex: 1 1 0%;
            width: 1% !important;
            min-width: 0;
        }

        #modalForm .input-group > .select2-container--bootstrap-5 .select2-selection {
            display: flex;
            align-items: center;
            width: 100%;
            height: 40px;
            min-height: 40px !important;
            padding: 0 13px;
            background-color: var(--bg-card) !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 0 10px 10px 0 !important;
        }

        #modalForm .input-group > .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            flex: 1 1 auto;
            min-width: 0;
            padding: 0;
            color: var(--text-main) !important;
            font-size: 13px;
            line-height: normal;
        }

        #modalForm .input-group > .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            position: relative;
            top: auto;
            right: auto;
            flex: 0 0 20px;
            height: 100%;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 99999 !important;
        }

        .filter-wrapper {
            margin-bottom: 20px;
        }

        .filter-toggle-btn {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        .filter-toggle-btn:hover {
            background: var(--bg-body);
        }

        .filter-panel {
            background: var(--bg-body);
            padding: 16px;
            border-radius: 8px;
            margin-top: 12px;
            border: 1px solid var(--border-color);
            display: none;
        }

        /* Penyegaran tampilan halaman pendaftaran */
        .registration-page { padding-top: 28px; }
        .registration-header { align-items: center; margin-bottom: 22px; }
        .registration-title-wrap { display:flex; align-items:center; gap:15px; }
        .registration-title-icon { width:54px; height:54px; display:grid; place-items:center; flex:0 0 54px; border-radius:17px; color:#fff; background:linear-gradient(145deg,#0b805d,#064b38); box-shadow:0 10px 22px rgba(6,75,56,.22); font-size:21px; }
        .registration-kicker { display:block; color:#128c64; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:1.1px; margin-bottom:4px; }
        .registration-header .page-title { font-size:25px; line-height:1.2; letter-spacing:-.65px; margin:0 0 5px; }
        .registration-header .page-subtitle { font-size:12px; }
        .registration-actions { align-items:center; gap:9px !important; }
        .registration-actions > button { min-height:42px; padding:0 15px; border-radius:11px; font-size:11px; white-space:nowrap; box-shadow:0 4px 12px rgba(15,23,42,.05); }
        .registration-actions .btn-excel { background:linear-gradient(135deg,#13784e,#0a5c43); box-shadow:0 7px 16px rgba(10,92,67,.2); }
        .registration-actions .btn-tambah { background:linear-gradient(135deg,#0a7656,#0b946b); box-shadow:0 7px 16px rgba(10,118,86,.22); }
        .registration-actions .filter-toggle-btn { border-color:#dbe7e1; }
        .registration-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin:0 0 20px; }
        .registration-stat { position:relative; display:flex; align-items:center; gap:13px; min-height:86px; padding:15px 17px; border:1px solid #e4ece8; border-radius:15px; background:linear-gradient(135deg,var(--bg-card),rgba(240,249,245,.55)); box-shadow:0 5px 16px rgba(20,55,42,.035); overflow:hidden; }
        [data-theme="dark"] .registration-stat { border-color:var(--border-color); background:var(--bg-card); }
        .registration-stat-icon { width:42px; height:42px; display:grid; place-items:center; flex:0 0 42px; border-radius:13px; background:#e5f5ed; color:#087653; font-size:16px; }
        .registration-stat.pending .registration-stat-icon { background:#fff4d8; color:#a36b00; }
        .registration-stat.verified .registration-stat-icon { background:#e2f7ec; color:#087c49; }
        .registration-stat.rejected .registration-stat-icon { background:#fde9e8; color:#c03932; }
        [data-theme="dark"] .registration-stat-icon { background:rgba(16,185,129,.13); }
        .registration-stat-label { color:var(--text-muted); font-size:10px; font-weight:700; margin-bottom:4px; }
        .registration-stat-value { color:var(--text-main); font-size:22px; line-height:1; font-weight:800; letter-spacing:-.4px; }
        .registration-panel { overflow:visible; border:1px solid #e2eae6; border-radius:17px; background:var(--bg-card); box-shadow:0 10px 28px rgba(18,55,39,.055); }
        .registration-panel-head { display:flex; align-items:center; justify-content:space-between; gap:15px; padding:18px 22px; border-bottom:1px solid #edf1ef; }
        .registration-panel-title { display:flex; align-items:center; gap:11px; }
        .registration-panel-title-icon { width:36px; height:36px; display:grid; place-items:center; border-radius:11px; color:#087653; background:#e8f6ef; }
        .registration-panel-title h2 { margin:0 0 3px; color:var(--text-main); font-size:13px; font-weight:800; }
        .registration-panel-title p { margin:0; color:var(--text-muted); font-size:10px; }
        .registration-live-badge { display:inline-flex; align-items:center; gap:7px; padding:6px 10px; border:1px solid #ccebdd; border-radius:30px; color:#087653; background:#f0faf5; font-size:10px; font-weight:700; white-space:nowrap; }
        .registration-live-badge::before { content:""; width:7px; height:7px; border-radius:50%; background:#13a36f; box-shadow:0 0 0 3px rgba(19,163,111,.12); }
        .registration-panel .panel-body { padding:18px 22px 21px; }
        .registration-panel .filter-wrapper { margin-bottom:13px; }
        .registration-filter-toolbar { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:12px; }
        .registration-filter-toolbar .filter-toolbar-actions { display:flex; align-items:center; flex-wrap:wrap; gap:8px; }
        .registration-filter-toolbar .filter-toggle-btn,.registration-filter-toolbar .btn-reset-filter { min-height:38px; padding:0 13px; border:1px solid #dbe7e1; border-radius:10px; background:var(--bg-card); color:#245747; font-size:11px; font-weight:800; }
        .registration-filter-toolbar .filter-toggle-btn:hover,.registration-filter-toolbar .btn-reset-filter:hover { background:#eff8f3; }
        .registration-filter-toolbar .btn-tambah { min-height:38px; padding:0 15px; border:0; border-radius:10px; background:linear-gradient(135deg,#0a7656,#0b946b); color:white; font-size:11px; font-weight:800; box-shadow:0 6px 14px rgba(10,118,86,.18); }
        .registration-year-toolbar { display:flex; align-items:center; justify-content:flex-end; flex-wrap:wrap; gap:8px; }
        .registration-year-toggle { min-width:48px; min-height:38px; display:inline-flex; align-items:center; justify-content:center; gap:9px; padding:0 13px; border:1px solid #ccebdd; border-radius:10px; background:#f0faf5; color:#087653; font-size:11px; font-weight:800; transition:all .18s ease; }
        .registration-year-toggle:hover { background:#e5f6ed; box-shadow:0 4px 12px rgba(8,118,83,.1); }
        .registration-year-count { min-width:22px; height:22px; display:inline-grid; place-items:center; padding:0 6px; border-radius:20px; background:#d8f0e4; color:#075b43; font-size:10px; font-weight:800; }
        .registration-panel .filter-panel { margin:0 0 16px; padding:14px; border:1px solid #e1ebe5; border-radius:12px; background:linear-gradient(135deg,#f8fbf9,#f3f9f6); }
        .registration-panel .filter-panel .form-label { color:#466357; font-size:10px; font-weight:800; }
        .registration-panel .filter-panel .form-control,.registration-panel .filter-panel .form-select { min-height:39px; border-color:#dce8e1; border-radius:9px; color:var(--text-main); font-size:11px; box-shadow:none; }
        .registration-panel .filter-panel .form-control:focus,.registration-panel .filter-panel .form-select:focus { border-color:#0b805d; box-shadow:0 0 0 3px rgba(11,128,93,.09); }
        .registration-search-wrap { position:relative; }
        .registration-search-wrap > i { position:absolute; z-index:2; top:50%; left:13px; color:#087653; transform:translateY(-50%); font-size:12px; }
        .registration-search-wrap input { padding-left:35px !important; }
        .registration-row-actions { display:flex; align-items:center; justify-content:center; gap:5px; white-space:nowrap; }
        .registration-row-action { width:29px; height:29px; display:inline-grid; place-items:center; padding:0; border:1px solid transparent; border-radius:9px; background:#eef7f2; color:#087653; transition:all .18s ease; }
        .registration-row-action:hover { transform:translateY(-1px); box-shadow:0 4px 10px rgba(15,55,40,.12); }
        .registration-row-action.edit { background:#e9f2ff; color:#2563eb; }
        .registration-row-action.cetak-pendaftaran { background:#fff5dc; color:#a66b00; }
        .registration-row-action.delete { background:#ffeded; color:#d13c3c; }
        .registration-departure-year { display:inline-flex; min-width:54px; justify-content:center; padding:5px 9px; border:1px solid #ccebdd; border-radius:20px; background:#effaf4; color:#087653; font-size:10px; font-weight:800; }
        .table-custom-container { padding:0; border:0; border-radius:12px; background:transparent; box-shadow:none; }
        .table-custom { width:100% !important; table-layout:fixed; border:1px solid #dce8e2; border-radius:11px; overflow:hidden; border-collapse:separate; border-spacing:0; }
        .table-custom thead th { padding:14px 10px !important; border-right:1px solid rgba(220,239,230,.22) !important; border-bottom:1px solid #176e55 !important; background:#075b43 !important; color:#f4fbf7 !important; font-size:10px; letter-spacing:.45px; }
        .table-custom thead th:first-child { border-radius:10px 0 0 0; }
        .table-custom thead th:last-child { border-radius:0 10px 0 0; }
        .table-custom thead th:last-child,.table-custom tbody td:last-child { border-right:0 !important; }
        .table-custom tbody td { padding:12px 10px !important; border-right:1px solid #e3ebe6 !important; border-bottom:1px solid #e3ebe6 !important; background:var(--bg-card); color:var(--text-main); font-size:11px; line-height:1.45; overflow-wrap:anywhere; }
        .table-custom tbody tr:nth-child(even) td { background:#f8fbf9; }
        .table-custom.table-hover tbody tr:hover td { background:#eff8f3 !important; }
        #tabelPendaftaran th:nth-child(1),#tabelPendaftaran td:nth-child(1) { width:3% !important; }
        #tabelPendaftaran th:nth-child(2),#tabelPendaftaran td:nth-child(2) { width:8% !important; }
        #tabelPendaftaran th:nth-child(3),#tabelPendaftaran td:nth-child(3) { width:10% !important; }
        #tabelPendaftaran th:nth-child(4),#tabelPendaftaran td:nth-child(4) { width:10% !important; }
        #tabelPendaftaran th:nth-child(5),#tabelPendaftaran td:nth-child(5) { width:28% !important; }
        #tabelPendaftaran th:nth-child(6),#tabelPendaftaran td:nth-child(6) { width:7% !important; }
        #tabelPendaftaran th:nth-child(7),#tabelPendaftaran td:nth-child(7) { width:7% !important; }
        #tabelPendaftaran th:nth-child(8),#tabelPendaftaran td:nth-child(8) { width:7% !important; }
        #tabelPendaftaran th:nth-child(9),#tabelPendaftaran td:nth-child(9) { width:9% !important; }
        #tabelPendaftaran th:nth-child(10),#tabelPendaftaran td:nth-child(10) { width:11% !important; }
        .registration-address-text { display:-webkit-box; overflow:hidden; -webkit-box-orient:vertical; -webkit-line-clamp:3; line-height:1.45; }
        [data-theme="dark"] .table-custom tbody tr:nth-child(even) td { background:rgba(255,255,255,.025); }
        .registration-panel .dataTables_wrapper { overflow:visible !important; }
        .registration-panel .table-responsive { overflow:visible !important; }
        .registration-panel .dataTables_filter { display:none !important; }
        .registration-panel .dataTables_filter label { display:flex; align-items:center; gap:9px; font-weight:700; }
        .registration-panel .dataTables_filter input { width:min(280px,55vw); height:38px; margin:0 !important; padding:0 12px; border:1px solid #dce7e1; border-radius:10px; outline:none; background:var(--bg-card); color:var(--text-main); font:500 11px 'Plus Jakarta Sans',sans-serif; box-shadow:0 2px 6px rgba(20,55,42,.025); }
        .registration-panel .dataTables_filter input:focus { border-color:#0b805d; box-shadow:0 0 0 3px rgba(11,128,93,.1); }
        .registration-panel .dataTables_empty { padding:35px !important; color:var(--text-muted) !important; }
        .registration-panel .dataTables_info,.registration-panel .dataTables_paginate { margin-top:12px; color:var(--text-muted) !important; font-size:10px; }
        .registration-panel .page-link { border-color:#e2eae6; color:#087653; font-size:11px; }
        .registration-panel .page-item.active .page-link { border-color:#087653; background:#087653; color:#fff; }
        .registration-panel .dataTables_scrollHead { border-radius:11px 11px 0 0; }
        .registration-panel .dataTables_scrollBody thead { visibility:collapse !important; }
        .registration-panel .dataTables_scrollBody { border-bottom:0 !important; }
        .registration-panel .dtr-details { width:100%; }
        .registration-panel .dtr-title { color:#62776d; font-weight:800; }
        .registration-panel .dtr-data { color:var(--text-main); }
        .registration-panel table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before { background:#0b805d; box-shadow:0 0 0 2px #e5f6ed; }
        @media(max-width:600px) { .registration-filter-toolbar { align-items:stretch; flex-direction:column; } .registration-filter-toolbar .filter-toolbar-actions { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); } .registration-filter-toolbar .filter-toolbar-actions > button { justify-content:center; } }
        @media(max-width:600px) { .registration-year-toolbar { display:grid; grid-template-columns:1fr; } .registration-year-toolbar > button { width:100%; justify-content:center; } }
        @media(max-width:1100px) { .registration-header { align-items:flex-start; flex-direction:column; } .registration-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } .registration-actions { width:100%; } }
        @media(max-width:767px) { .registration-panel .table-responsive { overflow-x:auto !important; overflow-y:hidden !important; } }
        @media(max-width:600px) { .registration-page { padding:18px 14px; } .registration-title-icon { width:46px; height:46px; flex-basis:46px; border-radius:14px; } .registration-header .page-title { font-size:20px; } .registration-actions { display:grid !important; grid-template-columns:repeat(2,minmax(0,1fr)); } .registration-actions > button { justify-content:center; padding:0 8px; font-size:10px; } .registration-stats { gap:9px; } .registration-stat { min-height:74px; padding:11px; gap:9px; } .registration-stat-icon { width:35px; height:35px; flex-basis:35px; font-size:13px; } .registration-stat-value { font-size:18px; } .registration-panel-head { padding:15px; } .registration-panel .panel-body { padding:13px; } .registration-live-badge { display:none; } .table-custom thead th,.table-custom tbody td { padding:11px 9px !important; } }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content" id="mainContent">
        <?php include 'topbar.php'; ?>

        <div class="content-wrapper registration-page">
            <!-- Header Halaman -->
            <div class="page-header registration-header">
                <div class="registration-title-wrap">
                    <div class="registration-title-icon"><i class="fa-solid fa-kaaba"></i></div>
                    <div>
                        <span class="registration-kicker">Layanan Pendaftaran Haji</span>
                        <h1 class="page-title">Pendaftaran Porsi Haji</h1>
                        <p class="page-subtitle">Kelola data pendaftaran calon jamaah haji secara terpadu.</p>
                    </div>
                </div>
                <div class="registration-actions d-flex gap-2 flex-wrap">
                    <button class="btn-excel" id="btnExportExcel">
                        <i class="fa-solid fa-file-excel"></i> Export Excel
                    </button>
                    <button class="filter-toggle-btn" id="btnDownloadTemplate" type="button">
                        <i class="fa-solid fa-download"></i> Template Import
                    </button>
                    <button class="filter-toggle-btn" id="btnImportExcel" type="button">
                        <i class="fa-solid fa-file-arrow-up"></i> Import Excel
                    </button>
                    <input type="file" id="fileImportExcel" accept=".xlsx,.xls" hidden>
                </div>
            </div>

            <section class="registration-stats" aria-label="Ringkasan pendaftaran jamaah">
                <div class="registration-stat total"><div class="registration-stat-icon"><i class="fa-solid fa-users"></i></div><div><div class="registration-stat-label">Total Jamaah</div><div class="registration-stat-value" id="statTotalJamaah">0</div></div></div>
                <div class="registration-stat verified"><div class="registration-stat-icon"><i class="fa-solid fa-circle-check"></i></div><div><div class="registration-stat-label">Terverifikasi</div><div class="registration-stat-value" id="statTerverifikasi">0</div></div></div>
                <div class="registration-stat pending"><div class="registration-stat-icon"><i class="fa-regular fa-clock"></i></div><div><div class="registration-stat-label">Menunggu Verifikasi</div><div class="registration-stat-value" id="statPending">0</div></div></div>
                <div class="registration-stat rejected"><div class="registration-stat-icon"><i class="fa-solid fa-circle-xmark"></i></div><div><div class="registration-stat-label">Ditolak</div><div class="registration-stat-value" id="statDitolak">0</div></div></div>
            </section>

            <!-- Panel Tabel -->
            <div class="panel registration-panel">
                <div class="registration-panel-head">
                    <div class="registration-panel-title">
                        <span class="registration-panel-title-icon"><i class="fa-solid fa-address-card"></i></span>
                        <div><h2>Daftar Jamaah Terdaftar</h2><p>Data pendaftaran terbaru tersimpan di sistem.</p></div>
                    </div>
                    <span class="registration-live-badge">Data terbaru</span>
                </div>
                <div class="panel-body p-4">

                    <!-- Filter dan pencarian ditempatkan langsung di atas tabel -->
                    <div class="filter-wrapper">
                        <div class="registration-filter-toolbar">
                            <div class="filter-toolbar-actions">
                                <button class="filter-toggle-btn" id="btnToggleFilter" type="button" aria-expanded="false" aria-controls="filterPanel">
                                    <i class="fa-solid fa-filter"></i> Filter Data
                                </button>
                                <button class="btn-reset-filter" id="btnResetFilter" type="button">
                                    <i class="fa-solid fa-rotate-left"></i> Reset Filter
                                </button>
                            </div>
                            <div class="registration-year-toolbar">
                                <button class="btn-tambah" id="btnTambah" type="button"><i class="fa-solid fa-plus-circle"></i> Tambah Jamaah</button>
                            </div>
                        </div>
                        <div class="filter-panel" id="filterPanel">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label mb-1">Cari Jamaah</label>
                                    <div class="registration-search-wrap"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="filterSearch" class="form-control form-control-sm" placeholder="Nama, NIK, atau nomor porsi..."></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label mb-1">Filter Status</label>
                                    <select id="filterStatus" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="Terverifikasi">Terverifikasi</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Ditolak">Ditolak</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label mb-1">Tahun Pendaftaran</label>
                                    <select id="filterTahun" class="form-select form-select-sm">
                                        <option value="">Semua Tahun</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label mb-1">Rentang Tanggal Daftar</label>
                                    <input type="text" id="filterTanggal" class="form-control form-control-sm" placeholder="Pilih rentang tanggal...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABEL DATA -->
                    <div class="table-custom-container">
                        <div class="table-responsive">
                            <table id="tabelPendaftaran" class="table table-custom table-hover w-100">
                                <thead>
                                    <tr>
                                        <th class="align-middle text-center">NO</th>
                                        <th class="align-middle text-center">NO. PORSI</th>
                                        <th class="align-middle text-center">NAMA JAMAAH</th>
                                        <th class="align-middle text-center">TEMPAT, TGL LAHIR</th>
                                        <th class="align-middle text-center">ALAMAT</th>
                                        <th class="align-middle text-center">TGL DAFTAR</th>
                                        <th class="align-middle text-center">TAHUN DAFTAR</th>
                                        <th class="align-middle text-center">TAHUN BERANGKAT</th>
                                        <th class="align-middle text-center">STATUS</th>
                                        <th class="align-middle text-center"><i class="fa-solid fa-cogs"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data diisi oleh JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'footer.php'; ?>
    </main>

    <!-- ================= MODAL FORM EKSKLUSIF, KEREN & LENGKAP ================= -->
    <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fa-solid fa-kaaba"></i> Form Pendaftaran Jamaah Haji
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPendaftaran">
                    <div class="modal-body">
                        <input type="hidden" id="idJamaah">

                        <!-- SECTION 1: DATA PRIBADI JAMAAH -->
                        <div class="form-card-section">
                            <div class="section-header-title">
                                <i class="fa-solid fa-user-gear"></i> 1. Data Pribadi Jamaah
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">No. Porsi Haji <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-hashtag"></i></span>
                                        <input type="text" class="form-control" id="noPorsi" required placeholder="Cth: 0100253421">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Nama Lengkap Jamaah (Sesuai KTP) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                        <input type="text" class="form-control" id="nama" required placeholder="Masukkan nama lengkap">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">NIK (16 Digit KTP) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                                        <input type="number" class="form-control" id="nik" required placeholder="Cth: 1175040107730072">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-city"></i></span>
                                        <input type="text" class="form-control" id="tempatLahir" required placeholder="Kota / Kabupaten">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <div class="input-group" id="datetimepickerLahir">
                                        <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                        <input type="text" class="form-control form-control-with-right-icon bg-white" id="tglLahir" required placeholder="Pilih tanggal..." autocomplete="off">
                                        <span class="input-group-text input-group-text-right" id="iconTglLahir">
                                            <i class="fa-solid fa-calendar text-muted"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Usia Jamaah</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-cake-candles"></i></span>
                                        <input type="text" class="form-control bg-light fw-bold text-success" id="usia" readonly placeholder="Otomatis">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-venus-mars"></i></span>
                                        <select class="form-select select2" id="jenkel" required>
                                            <option value="" disabled selected>Pilih Jenis Kelamin...</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Pendidikan Terakhir</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-graduation-cap"></i></span>
                                        <select class="form-select select2" id="pendidikan">
                                            <option value="" disabled selected>Pilih Pendidikan...</option>
                                            <option value="SD">SD / Sederajat</option>
                                            <option value="SMP">SMP / Sederajat</option>
                                            <option value="SMA">SMA / SMK / MA</option>
                                            <option value="D3">Diploma (D3)</option>
                                            <option value="S1">Sarjana (S1)</option>
                                            <option value="S2">Magister (S2)</option>
                                            <option value="S3">Doktor (S3)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Pekerjaan Utama</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-briefcase"></i></span>
                                        <input type="text" class="form-control" id="pekerjaan" placeholder="Cth: PNS, Farmer, Swasta">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: ALAMAT & KONTAK -->
                        <div class="form-card-section">
                            <div class="section-header-title">
                                <i class="fa-solid fa-map-location-dot"></i> 2. Domisili & Kontak Jamaah
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">No. Telepon / WhatsApp <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-brands fa-whatsapp"></i></span>
                                        <input type="number" class="form-control" id="telepon" required placeholder="Cth: 081264774428">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Alamat Lengkap Domisili <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-house-user"></i></span>
                                        <input type="text" class="form-control" id="alamat" required placeholder="Nama Jalan, Lorong, Dusun, RT/RW">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kabupaten / Kota <span class="text-danger">*</span></label>
                                    <div class="autocomplete-wrap">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
                                            <input type="text" class="form-control" id="kabupaten" placeholder="Ketik nama Kabupaten..." autocomplete="off" aria-autocomplete="list" aria-expanded="false">
                                        </div>
                                        <input type="hidden" id="id_kabupaten">
                                        <div class="autocomplete-results" id="hasilKabupaten" role="listbox"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                    <div class="autocomplete-wrap">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-location-dot"></i></span>
                                            <input type="text" class="form-control" id="kecamatan" placeholder="Pilih Kabupaten dulu..." autocomplete="off" aria-autocomplete="list" aria-expanded="false" disabled>
                                        </div>
                                        <input type="hidden" id="id_kecamatan">
                                        <div class="autocomplete-results" id="hasilKecamatan" role="listbox"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Desa / Kelurahan <span class="text-danger">*</span></label>
                                    <div class="autocomplete-wrap">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-tree-city"></i></span>
                                            <input type="text" class="form-control" id="desa" placeholder="Pilih Kecamatan dulu..." autocomplete="off" aria-autocomplete="list" aria-expanded="false" disabled>
                                        </div>
                                        <div class="autocomplete-results" id="hasilDesa" role="listbox"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: DATA SETORAN BPIH & BANK -->
                        <div class="form-card-section">
                            <div class="section-header-title">
                                <i class="fa-solid fa-vault"></i> 3. Data Transaksi Setoran BPIH & Perbankan
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Bank Penerima Setoran (BPS-BPIH) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-building-columns"></i></span>
                                        <select class="form-select select2" id="bank" required>
                                            <option value="" disabled selected>Memuat daftar bank...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. Rekening Bank <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-credit-card"></i></span>
                                        <input type="text" class="form-control" id="noRek" required placeholder="Nomor rekening BPS-BPIH">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. Validasi Setoran <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-file-shield"></i></span>
                                        <input type="text" class="form-control" id="noValidasi" required placeholder="Nomor validasi setoran">
                                    </div>
                                </div>

                                <!-- FIELD BARU 1: TANGGAL SETOR BPIH -->
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Setor BPIH <span class="text-danger">*</span></label>
                                    <div class="input-group" id="datetimepickerSetor">
                                        <span class="input-group-text"><i class="fa-solid fa-receipt"></i></span>
                                        <input type="text" class="form-control form-control-with-right-icon bg-white" id="tglSetorBpih" required placeholder="Pilih tanggal setor..." autocomplete="off">
                                        <span class="input-group-text input-group-text-right" id="iconTglSetor">
                                            <i class="fa-solid fa-calendar text-muted"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- FIELD BARU 2: JUMLAH SETOR BPIH -->
                                <div class="col-md-4">
                                    <label class="form-label">Jumlah Setor BPIH (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold text-success"><i class="fa-solid fa-coins me-1"></i> Rp</span>
                                        <input type="text" class="form-control fw-bold text-dark" id="jumlahSetorBpih" required placeholder="Cth: 25.000.000">
                                    </div>
                                </div>

                                <!-- FIELD BARU 3: ESTIMASI TAHUN KEBERANGKATAN -->
                                <div class="col-md-4">
                                    <label class="form-label">Estimasi Tahun Keberangkatan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-plane-departure"></i></span>
                                        <input type="number" class="form-control fw-bold text-primary" id="tahunKeberangkatan" required placeholder="Cth: 2028" min="2024" max="2070">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Tahun Pendaftaran <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-calendar"></i></span>
                                        <input type="number" class="form-control" id="tahun" required value="<?= date('Y') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Resmi Pendaftaran <span class="text-danger">*</span></label>
                                    <div class="input-group" id="datetimepickerDaftar">
                                        <span class="input-group-text"><i class="fa-solid fa-calendar-check"></i></span>
                                        <input type="text" class="form-control form-control-with-right-icon bg-white" id="tglDaftar" required autocomplete="off">
                                        <span class="input-group-text input-group-text-right" id="iconTglDaftar">
                                            <i class="fa-solid fa-calendar text-muted"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status Verifikasi Berkas <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-circle-check"></i></span>
                                        <select class="form-select select2" id="status" required>
                                            <option value="pending">Pending (Menunggu Verifikasi)</option>
                                            <option value="terverifikasi">Terverifikasi (Resmi)</option>
                                            <option value="ditolak">Ditolak / Batal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="timeStamp" value="<?= date('Y-m-d H:i:s') ?>">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border: 1.5px solid #cbd5e1; font-weight: 600; font-size: 13px;">
                            <i class="fa-solid fa-xmark me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary" style="background: var(--primary, #0a5c43); border: none; font-weight: 700; padding: 9px 26px; font-size: 13px;">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ================= MODAL LIHAT DETAIL ================= -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="fa-solid fa-id-badge me-2 text-primary"></i> Detail Data Jamaah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted fw-bold d-block">No. Porsi</small>
                            <span id="detPorsi" class="fw-bold fs-6 text-primary"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted fw-bold d-block">Nama Lengkap</small>
                            <span id="detNama" class="fw-bold fs-6"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted fw-bold d-block">NIK</small>
                            <span id="detNik"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted fw-bold d-block">Layanan</small>
                            <span id="detLayanan"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted fw-bold d-block">Tanggal Daftar</small>
                            <span id="detTgl"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted fw-bold d-block">Status</small>
                            <span id="detStatus"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal" style="background: var(--primary); border: none; font-weight: 700; font-size: 13px;">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= SCRIPTS ================= -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>

    <script>
        $(document).ready(function() {

            // Format Currency Rupiah Otomatis pada Input Jumlah Setor BPIH
            $('#jumlahSetorBpih').on('keyup input', function() {
                var value = $(this).val().replace(/[^0-9]/g, '');
                if (value !== '') {
                    $(this).val(parseInt(value, 10).toLocaleString('id-ID'));
                } else {
                    $(this).val('');
                }
            });

            // ==========================================
            // MEMUAT DAFTAR BANK DARI API GET_BANK.PHP VIA AJAX
            // ==========================================
            function loadOptionsBank(selectedValue) {
                var $bankSelect = $('#bank');

                return $.ajax({
                    url: '../api/get_bank.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $bankSelect.empty();
                        $bankSelect.append('<option value="" disabled selected>Pilih Bank...</option>');

                        if (response && response.status === true && Array.isArray(response.data) && response.data.length > 0) {
                            response.data.forEach(function(item) {
                                $bankSelect.append(new Option(item.nm_bank, item.nm_bank));
                            });
                        } else {
                            $bankSelect.append('<option value="" disabled>Tidak ada bank aktif</option>');
                        }

                        if (selectedValue) {
                            $bankSelect.val(selectedValue).trigger('change');
                        } else {
                            $bankSelect.val('').trigger('change');
                        }
                    },
                    error: function() {
                        $bankSelect.empty().append('<option value="" disabled>Gagal memuat bank</option>');
                    }
                });
            }

            // ==========================================
            // FITUR DEPENDENSI WILAYAH (LENGKAP WITH KEYBOARD NAV)
            // ==========================================
            function setupWilayahAutocomplete(config) {
                const $input = $(config.input);
                const $results = $(config.results);
                const $hiddenId = config.hiddenId ? $(config.hiddenId) : null;
                let timer = null;
                let activeIndex = -1;

                function closeResults() {
                    $results.hide().empty();
                    $input.attr('aria-expanded', 'false');
                    activeIndex = -1;
                }

                function showMessage(message) {
                    $results.html($('<div>', {
                        class: 'autocomplete-message',
                        text: message
                    })).show();
                    $input.attr('aria-expanded', 'true');
                }

                $input.on('input', function() {
                    const keyword = this.value.trim();
                    clearTimeout(timer);
                    if ($hiddenId) $hiddenId.val('');
                    if (keyword.length < 2) {
                        closeResults();
                        return;
                    }
                    if (config.parentId && !$(config.parentId).val()) {
                        showMessage('Harap pilih ' + config.parentLabel + ' terlebih dahulu.');
                        return;
                    }

                    timer = setTimeout(function() {
                        showMessage('Memuat data...');
                        let reqData = {
                            tipe: config.tipe,
                            q: keyword
                        };
                        if (config.parentId) {
                            reqData[config.parentParam] = $(config.parentId).val();
                        }

                        $.ajax({
                            url: '../api/api_wilayah.php',
                            method: 'GET',
                            dataType: 'json',
                            data: reqData
                        }).done(function(response) {
                            $results.empty();
                            if (!response || response.success !== true || !Array.isArray(response.data)) {
                                showMessage((response && response.message) || 'Data tidak dapat dimuat.');
                                return;
                            }
                            if (!response.data.length) {
                                showMessage('Data tidak ditemukan.');
                                return;
                            }
                            response.data.forEach(function(item) {
                                const $button = $('<button>', {
                                    type: 'button',
                                    class: 'autocomplete-item',
                                    role: 'option',
                                    text: item.nama
                                });
                                $button.on('mousedown', function(e) {
                                    e.preventDefault();
                                    $input.val(item.nama).trigger('change');
                                    if ($hiddenId) $hiddenId.val(item.id);
                                    if (config.onSelect) config.onSelect(item);
                                    closeResults();
                                });
                                $results.append($button);
                            });
                            $results.show();
                            $input.attr('aria-expanded', 'true');
                        }).fail(function(xhr, status) {
                            if (status === 'abort') return;
                            showMessage('Gagal menghubungi backend.');
                        });
                    }, 250);
                });

                $input.on('keydown', function(e) {
                    const $items = $results.find('.autocomplete-item');
                    if (!$items.length || !$results.is(':visible')) return;
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        activeIndex = Math.min(activeIndex + 1, $items.length - 1);
                        $items.removeClass('active').eq(activeIndex).addClass('active').trigger('focus');
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        activeIndex = Math.max(activeIndex - 1, 0);
                        $items.removeClass('active').eq(activeIndex).addClass('active').trigger('focus');
                    } else if (e.key === 'Escape') closeResults();
                    else if (e.key === 'Enter' && activeIndex >= 0) {
                        e.preventDefault();
                        $items.eq(activeIndex).trigger('mousedown');
                    }
                });

                $(document).on('click', function(e) {
                    if (!$(e.target).closest(config.input + ', ' + config.results).length) closeResults();
                });
            }

            setupWilayahAutocomplete({
                input: '#kabupaten',
                results: '#hasilKabupaten',
                hiddenId: '#id_kabupaten',
                tipe: 'kabupaten',
                onSelect: function(item) {
                    $('#kecamatan').prop('disabled', false).attr('placeholder', 'Ketik nama kecamatan...').val('');
                    $('#id_kecamatan').val('');
                    $('#desa').prop('disabled', true).attr('placeholder', 'Pilih Kecamatan dulu...').val('');
                }
            });
            setupWilayahAutocomplete({
                input: '#kecamatan',
                results: '#hasilKecamatan',
                hiddenId: '#id_kecamatan',
                tipe: 'kecamatan',
                parentId: '#id_kabupaten',
                parentParam: 'id_kab',
                parentLabel: 'Kabupaten',
                onSelect: function(item) {
                    $('#desa').prop('disabled', false).attr('placeholder', 'Ketik nama desa...').val('');
                }
            });
            setupWilayahAutocomplete({
                input: '#desa',
                results: '#hasilDesa',
                tipe: 'desa',
                parentId: '#id_kecamatan',
                parentParam: 'id_kec',
                parentLabel: 'Kecamatan'
            });

            // ==========================================
            // DATATABLES CONFIGURATION
            // ==========================================
            var registrationShowPriorYears = false;
            var registrationCurrentYear = String(new Date().getFullYear());
            var registrationArchivedCount = 0;

            function updateRegistrationYearToggle(archivedCount) {
                if (typeof archivedCount === 'number') registrationArchivedCount = archivedCount;
                $('#yearArchiveCount').text(new Intl.NumberFormat('id-ID').format(registrationArchivedCount));
                $('#btnToggleYearArchive')
                    .attr('aria-expanded', registrationShowPriorYears ? 'true' : 'false')
                    .attr('aria-label', registrationShowPriorYears ? 'Sembunyikan data tahun sebelumnya' : 'Tampilkan data tahun sebelumnya')
                    .attr('title', registrationShowPriorYears ? 'Sembunyikan data dari tahun sebelumnya' : 'Tampilkan data dari tahun pendaftaran sebelumnya');
            }

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData) {
                if (settings.nTable.id !== 'tabelPendaftaran') return true;
                var record = rowData || (settings.aoData[dataIndex] && settings.aoData[dataIndex]._aData) || {};
                var selectedYear = $('#filterTahun').val() || '';
                if (selectedYear) return String(record.tahun || '') === selectedYear;
                if (registrationShowPriorYears) return true;
                return String(record.tahun || '') === registrationCurrentYear;
            });

            var table = $('#tabelPendaftaran').DataTable({
                processing: true,
                serverSide: false,
                responsive: {details:{type:'inline',target:0}},
                paging: true,
                pageLength: 10,
                lengthChange: false,
                info: true,
                ordering: true,
                language: {
                    emptyTable: 'Belum ada data pendaftaran',
                    zeroRecords: 'Data jamaah tidak ditemukan',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ jamaah',
                    infoEmpty: 'Belum ada data jamaah',
                    paginate: {previous:'‹',next:'›'}
                },
                ajax: {
                    url: '../api/get_pendaftaran.php',
                    dataSrc: function(response) {
                        var records = response && Array.isArray(response.data) ? response.data : [];
                        var numberFormat = new Intl.NumberFormat('id-ID');
                        $('#statTotalJamaah').text(numberFormat.format(records.length));
                        $('#statTerverifikasi').text(numberFormat.format(records.filter(function(row){return row.status === 'terverifikasi';}).length));
                        $('#statPending').text(numberFormat.format(records.filter(function(row){return row.status === 'pending';}).length));
                        $('#statDitolak').text(numberFormat.format(records.filter(function(row){return row.status === 'ditolak';}).length));
                        var archivedCount = records.filter(function(row){return String(row.tahun || '') !== registrationCurrentYear;}).length;
                        updateRegistrationYearToggle(archivedCount);
                        var years = Array.from(new Set(records.map(function(row){return String(row.tahun || '').trim();}).filter(function(year){return /^\d{4}$/.test(year);}))).sort(function(a,b){return Number(b)-Number(a);});
                        var $yearFilter = $('#filterTahun');
                        var selectedYear = $yearFilter.val();
                        $yearFilter.empty().append($('<option>', {value:'', text:'Semua Tahun'}));
                        years.forEach(function(year){$yearFilter.append($('<option>', {value:year, text:year}));});
                        if (selectedYear && years.indexOf(selectedYear) !== -1) $yearFilter.val(selectedYear);
                        return records;
                    }
                },
                columns: [{
                        data: null,
                        searchable: false,
                        className: 'text-center dtr-control',
                        responsivePriority: 1,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'no_porsi',
                        responsivePriority: 2
                    },
                    {
                        data: 'nama',
                        responsivePriority: 1,
                        render: function(data, type, row) {
                            return `<span class="fw-bold text-primary-dark">${row.nama}<br><small class="text-muted">${row.nik}</small></span>`;
                        }
                    },
                    {
                        data: 'tempat_lahir',
                        responsivePriority: 8,
                        render: function(data, type, row) {
                            return `<span class="fw-bold text-primary-dark">${row.tempat_lahir}, ${row.tgl_lahir} ${row.bulan_lahir} ${row.tahun_lahir}</span>`;
                        }
                    },
                    {
                        data: 'nik',
                        className: 'registration-address-cell',
                        responsivePriority: 10,
                        render: function(data, type, row) {
                            var address = `${row.alamat}, Desa ${row.desa} Kec. ${row.kecamatan} ${row.kabupaten}`;
                            var escapedAddress = $('<div>').text(address).html();
                            var addressTitle = escapedAddress.replace(/"/g, '&quot;');
                            return `<span class="registration-address-text" title="${addressTitle}">${escapedAddress}</span>`;
                        }
                    },
                    {
                        data: 'tgl_daftar',
                        responsivePriority: 5,
                        render: function(data) {
                            return data ? moment(data).format('DD MMM YYYY') : '-';
                        }
                    },
                    {
                        data: 'tahun',
                        className: 'text-center',
                        responsivePriority: 4,
                        render: function(data) {
                            return data && data !== '-' ? `<span class="registration-departure-year">${data}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'tahun_berangkat',
                        className: 'text-center',
                        responsivePriority: 5,
                        render: function(data) {
                            return data && data !== '0000' ? `<span class="registration-departure-year">${data}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'status',
                        responsivePriority: 3,
                        render: function(data) {
                            if (data === 'terverifikasi') return '<span class="badge-status badge-terverifikasi"><i class="fa-solid fa-check-circle"></i> Terverifikasi</span>';
                            if (data === 'pending') return '<span class="badge-status badge-pending"><i class="fa-solid fa-clock"></i> Pending</span>';
                            return '<span class="badge-status badge-ditolak"><i class="fa-solid fa-times-circle"></i> Ditolak</span>';
                        }
                    },
                    {
                        data: 'uuid',
                        className: 'text-center',
                        searchable: false,
                        orderable: false,
                        responsivePriority: 2,
                        render: function(data) {
                            var uuid = $('<div>').text(data || '').html();
                            return `
                            <div class="registration-row-actions">
                                <button type="button" class="registration-row-action edit" data-uuid="${uuid}" title="Edit data" aria-label="Edit data"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button type="button" class="registration-row-action cetak-pendaftaran" data-uuid="${uuid}" title="Cetak data" aria-label="Cetak data"><i class="fa-solid fa-print"></i></button>
                                <button type="button" class="registration-row-action delete" data-uuid="${uuid}" title="Hapus data" aria-label="Hapus data"><i class="fa-solid fa-trash-can"></i></button>
                            </div>`;
                        }
                    }
                ]
            });

            // EXPORT WORKBOOK PROFESIONAL
            $('#btnExportExcel').on('click', function() {
                var $btn = $(this);
                var originalHtml = $btn.html();
                if (!window.XLSX || !window.XLSX.utils || !window.XLSX.writeFile) {
                    Swal.fire({icon:'error', title:'Library Excel tidak tersedia', text:'xlsx.bundle.js gagal dimuat. Periksa koneksi internet lalu muat ulang halaman.'});
                    return;
                }

                $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Menyiapkan Excel...');
                $.ajax({
                    url: '../api/get_pendaftaran.php',
                    type: 'GET',
                    dataType: 'json',
                    cache: false
                }).done(function(response) {
                    var records = response && Array.isArray(response.data) ? response.data : [];
                    if (!records.length) {
                        Swal.fire({icon:'warning', title:'Data Kosong', text:'Tidak ada data pendaftaran untuk diekspor.', confirmButtonColor:'#0a5c43'});
                        return;
                    }

                    var XLSX_ENGINE = window.XLSX;
                    var wb = XLSX_ENGINE.utils.book_new();
                    var exportedAt = new Date().toLocaleString('id-ID', {day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit'});
                    var cleanText = function(value) {
                        var element = document.createElement('div');
                        element.innerHTML = String(value == null ? '' : value);
                        return (element.textContent || element.innerText || '').trim();
                    };
                    var statusLabel = function(status) {
                        return status === 'terverifikasi' ? 'Terverifikasi' : (status === 'pending' ? 'Pending' : (status === 'ditolak' ? 'Ditolak' : '-'));
                    };
                    var headers = [
                        'NO', 'NO. PORSI', 'NAMA JAMAAH', 'NIK', 'TEMPAT LAHIR', 'TANGGAL LAHIR', 'USIA',
                        'JENIS KELAMIN', 'PENDIDIKAN', 'PEKERJAAN', 'NO. TELP/WA', 'ALAMAT', 'DESA/KELURAHAN',
                        'KECAMATAN', 'KABUPATEN/KOTA', 'TAHUN DAFTAR', 'TANGGAL DAFTAR', 'BANK',
                        'NO. REKENING', 'NO. VALIDASI', 'STATUS'
                    ];
                    var rows = records.map(function(row, index) {
                        var birthDate = row.tgl_lahir && row.bulan_lahir && row.tahun_lahir
                            ? String(row.tgl_lahir).padStart(2, '0') + ' ' + row.bulan_lahir + ' ' + row.tahun_lahir
                            : '-';
                        var registeredDate = row.tgl_daftar && moment(row.tgl_daftar).isValid()
                            ? moment(row.tgl_daftar).format('DD MMM YYYY')
                            : '-';
                        return [
                            index + 1, String(row.no_porsi || '-'), cleanText(row.nama || '-'), String(row.nik || '-'),
                            cleanText(row.tempat_lahir || '-'), birthDate, row.usia ? String(row.usia) + ' Tahun' : '-',
                            row.jenis_kelamin || '-', row.pendidikan || '-', cleanText(row.pekerjaan || '-'),
                            String(row.hp || '-'), cleanText(row.alamat || '-'), cleanText(row.desa || '-'),
                            cleanText(row.kecamatan || '-'), cleanText(row.kabupaten || '-'), String(row.tahun || '-'),
                            registeredDate, cleanText(row.bank || '-'), String(row.no_rek || '-'),
                            String(row.no_validasi || '-'), statusLabel(row.status)
                        ];
                    });

                    var totalVerified = records.filter(function(row) { return row.status === 'terverifikasi'; }).length;
                    var totalPending = records.filter(function(row) { return row.status === 'pending'; }).length;
                    var totalRejected = records.filter(function(row) { return row.status === 'ditolak'; }).length;
                    var totalMale = records.filter(function(row) { return row.jenis_kelamin === 'Laki-laki'; }).length;
                    var totalFemale = records.filter(function(row) { return row.jenis_kelamin === 'Perempuan'; }).length;
                    var summaryData = [
                        ['LAPORAN PENDAFTARAN JAMAAH HAJI'],
                        ['Kementerian Haji dan Umrah Kota Subulussalam'],
                        ['Diekspor pada ' + exportedAt],
                        [],
                        ['RINGKASAN DATA'],
                        ['INDIKATOR', 'JUMLAH'],
                        ['Total Jamaah', records.length],
                        ['Laki-laki', totalMale],
                        ['Perempuan', totalFemale],
                        ['Terverifikasi', totalVerified],
                        ['Pending', totalPending],
                        ['Ditolak', totalRejected]
                    ];
                    var summaryWs = XLSX_ENGINE.utils.aoa_to_sheet(summaryData);
                    summaryWs['!merges'] = [
                        {s:{r:0,c:0},e:{r:0,c:1}},
                        {s:{r:1,c:0},e:{r:1,c:1}},
                        {s:{r:2,c:0},e:{r:2,c:1}},
                        {s:{r:4,c:0},e:{r:4,c:1}}
                    ];
                    summaryWs['!cols'] = [{wch:30},{wch:18}];
                    summaryWs['!rows'] = [{hpt:34},{hpt:22},{hpt:20},{hpt:10},{hpt:25},{hpt:22}];
                    var summaryRange = XLSX_ENGINE.utils.decode_range(summaryWs['!ref']);
                    for (var sr = summaryRange.s.r; sr <= summaryRange.e.r; sr++) {
                        for (var sc = summaryRange.s.c; sc <= summaryRange.e.c; sc++) {
                            var summaryRef = XLSX_ENGINE.utils.encode_cell({r:sr,c:sc});
                            if (!summaryWs[summaryRef]) continue;
                            if (sr === 0 || sr === 4) {
                                summaryWs[summaryRef].s = {fill:{patternType:'solid',fgColor:{rgb:'075B43'}},font:{name:'Aptos Display',sz:sr===0?18:12,bold:true,color:{rgb:'FFFFFF'}},alignment:{vertical:'center',horizontal:sc===1?'right':'left'}};
                            } else if (sr === 1 || sr === 2) {
                                summaryWs[summaryRef].s = {font:{name:'Aptos',sz:sr===1?11:9,italic:sr===2,color:{rgb:sr===2?'64748B':'334155'}},alignment:{vertical:'center'}};
                            } else if (sr === 5) {
                                summaryWs[summaryRef].s = {fill:{patternType:'solid',fgColor:{rgb:'DCEFE7'}},font:{name:'Aptos',sz:10,bold:true,color:{rgb:'075B43'}},border:{bottom:{style:'thin',color:{rgb:'A7C9B8'}}},alignment:{vertical:'center'}};
                            } else if (sr >= 6) {
                                summaryWs[summaryRef].s = {fill:{patternType:'solid',fgColor:{rgb:sr%2?'F5F9F7':'FFFFFF'}},font:{name:'Aptos',sz:10,bold:sc===1,color:{rgb:'1F2937'}},border:{bottom:{style:'thin',color:{rgb:'E2E8F0'}}},alignment:{vertical:'center',horizontal:sc===1?'center':'left'}};
                            }
                        }
                    }
                    XLSX_ENGINE.utils.book_append_sheet(wb, summaryWs, 'Ringkasan');

                    var detailData = [
                        ['DATA PENDAFTARAN JAMAAH HAJI'],
                        ['Kementerian Haji dan Umrah Kota Subulussalam'],
                        ['Diekspor pada ' + exportedAt + '  |  Total data: ' + records.length + ' jamaah'],
                        [],
                        headers
                    ].concat(rows);
                    var ws = XLSX_ENGINE.utils.aoa_to_sheet(detailData);
                    var lastColumn = XLSX_ENGINE.utils.encode_col(headers.length - 1);
                    ws['!merges'] = [
                        {s:{r:0,c:0},e:{r:0,c:headers.length-1}},
                        {s:{r:1,c:0},e:{r:1,c:headers.length-1}},
                        {s:{r:2,c:0},e:{r:2,c:headers.length-1}}
                    ];
                    ws['!autofilter'] = {ref:'A5:' + lastColumn + (detailData.length)};
                    ws['!freeze'] = {xSplit:2,ySplit:5,topLeftCell:'C6',activePane:'bottomRight',state:'frozen'};
                    ws['!cols'] = [
                        {wch:6},{wch:17},{wch:29},{wch:20},{wch:19},{wch:22},{wch:12},{wch:17},{wch:17},{wch:22},
                        {wch:19},{wch:40},{wch:22},{wch:22},{wch:25},{wch:15},{wch:19},{wch:22},{wch:22},{wch:24},{wch:19}
                    ];
                    ws['!rows'] = [{hpt:36},{hpt:22},{hpt:20},{hpt:9},{hpt:30}];
                    for (var dr = 5; dr < detailData.length; dr++) ws['!rows'].push({hpt:23});

                    var detailRange = XLSX_ENGINE.utils.decode_range(ws['!ref']);
                    for (var r = detailRange.s.r; r <= detailRange.e.r; r++) {
                        for (var c = detailRange.s.c; c <= detailRange.e.c; c++) {
                            var ref = XLSX_ENGINE.utils.encode_cell({r:r,c:c});
                            if (!ws[ref]) continue;
                            if (r === 0) {
                                ws[ref].s = {fill:{patternType:'solid',fgColor:{rgb:'075B43'}},font:{name:'Aptos Display',sz:18,bold:true,color:{rgb:'FFFFFF'}},alignment:{vertical:'center'}};
                            } else if (r === 1 || r === 2) {
                                ws[ref].s = {fill:{patternType:'solid',fgColor:{rgb:'F2F7F4'}},font:{name:'Aptos',sz:r===1?11:9,italic:r===2,color:{rgb:r===1?'334155':'64748B'}},alignment:{vertical:'center'}};
                            } else if (r === 4) {
                                ws[ref].s = {fill:{patternType:'solid',fgColor:{rgb:'0E8060'}},font:{name:'Aptos',sz:10,bold:true,color:{rgb:'FFFFFF'}},alignment:{horizontal:'center',vertical:'center',wrapText:true},border:{top:{style:'thin',color:{rgb:'0A5C43'}},bottom:{style:'medium',color:{rgb:'064B38'}}}};
                            } else {
                                var fillColor = r % 2 === 0 ? 'F4F8F6' : 'FFFFFF';
                                var bodyStyle = {fill:{patternType:'solid',fgColor:{rgb:fillColor}},font:{name:'Aptos',sz:10,color:{rgb:'24332D'}},alignment:{horizontal:[0,1,3,5,6,7,15,16,20].indexOf(c)!==-1?'center':'left',vertical:'center',wrapText:[2,11,12,13,14].indexOf(c)!==-1},border:{bottom:{style:'thin',color:{rgb:'E1EAE5'}}}};
                                if ([1,3,10,18,19].indexOf(c)!==-1) ws[ref].t = 's';
                                if (c === 20) {
                                    var status = rows[r-5][20];
                                    var statusColors = status === 'Terverifikasi' ? ['E5F6ED','087443'] : (status === 'Pending' ? ['FFF5D9','9A6700'] : ['FDECEC','B42318']);
                                    bodyStyle.fill = {patternType:'solid',fgColor:{rgb:statusColors[0]}};
                                    bodyStyle.font = {name:'Aptos',sz:9,bold:true,color:{rgb:statusColors[1]}};
                                }
                                ws[ref].s = bodyStyle;
                            }
                        }
                    }
                    ws['!pageSetup'] = {orientation:'landscape',fitToWidth:1,fitToHeight:0};
                    XLSX_ENGINE.utils.book_append_sheet(wb, ws, 'Data Jamaah');
                    wb.Props = {Title:'Laporan Pendaftaran Jamaah Haji',Subject:'Data pendaftaran jamaah haji',Author:'Kementerian Haji dan Umrah Kota Subulussalam',CreatedDate:new Date()};
                    XLSX_ENGINE.writeFile(wb, 'Laporan_Pendaftaran_Jamaah_Haji_' + moment().format('YYYYMMDD_HHmmss') + '.xlsx');
                    Swal.fire({icon:'success',title:'Export Berhasil',text:'Workbook Excel profesional berhasil diunduh.',confirmButtonColor:'#0a5c43',timer:2000,showConfirmButton:false});
                }).fail(function() {
                    Swal.fire({icon:'error',title:'Gagal Export',text:'Data pendaftaran tidak dapat diambil dari server.',confirmButtonColor:'#dc2626'});
                }).always(function() {
                    $btn.html(originalHtml).prop('disabled', false);
                });
            });

            // Template dan impor Excel mengikuti nama kolom pada tabel ta_pendaftaran.
            var importColumns = [
                'no_porsi', 'nama', 'nik', 'tempat_lahir', 'tgl_lahir', 'usia', 'jenkel', 'pendidikan',
                'pekerjaan', 'telepon', 'alamat', 'id_kabupaten', 'kabupaten', 'id_kecamatan', 'kecamatan',
                'desa', 'tahun', 'tgl_daftar', 'status', 'bank', 'no_rek', 'tgl_setor_bpih',
                'jumlah_setor', 'no_validasi', 'id_prov', 'id_kab_user', 'tahun_berangkat'
            ];
            var importRequiredColumns = [
                'no_porsi', 'nama', 'tempat_lahir', 'tgl_lahir', 'usia', 'jenkel', 'telepon', 'alamat',
                'kecamatan', 'desa', 'tgl_daftar',
                'bank', 'no_rek', 'no_validasi'
            ];

            $('#btnDownloadTemplate').on('click', function() {
                if (!window.XLSX || !window.XLSX.utils) {
                    return Swal.fire({icon:'error',title:'Library Excel tidak tersedia',text:'xlsx.bundle.js gagal dimuat.'});
                }
                var templateBook = XLSX.utils.book_new();
                var dataSheet = XLSX.utils.aoa_to_sheet([importColumns]);
                dataSheet['!cols'] = importColumns.map(function(column) {
                    return {wch: Math.max(16, Math.min(26, column.length + 4))};
                });
                dataSheet['!rows'] = [{hpt:30}];
                dataSheet['!autofilter'] = {ref:'A1:' + XLSX.utils.encode_col(importColumns.length - 1) + '1'};
                importColumns.forEach(function(column, index) {
                    var cell = XLSX.utils.encode_cell({r:0,c:index});
                    dataSheet[cell].s = {
                        fill:{patternType:'solid',fgColor:{rgb:'075B43'}},
                        font:{name:'Aptos',sz:10,bold:true,color:{rgb:'FFFFFF'}},
                        alignment:{horizontal:'center',vertical:'center',wrapText:true},
                        border:{bottom:{style:'medium',color:{rgb:'064B38'}}}
                    };
                });
                XLSX.utils.book_append_sheet(templateBook, dataSheet, 'Data Pendaftaran');

                var guideRows = [
                    ['PETUNJUK TEMPLATE IMPORT PENDAFTARAN'],
                    ['Gunakan sheet "Data Pendaftaran" untuk mengisi data. Jangan mengubah nama kolom pada baris pertama.'],
                    ['Kolom wajib: ' + importRequiredColumns.join(', ')],
                    ['Tanggal isi dengan format YYYY-MM-DD, contoh: 2026-10-28; template berbahasa Indonesia dengan hari/bulan/tahun lahir juga didukung.'],
                    ['jenkel hanya L atau P. status boleh dikosongkan (default pending) atau diisi pending, terverifikasi, ditolak.'],
                    ['Kolom opsional yang boleh kosong: pendidikan, pekerjaan, tgl_setor_bpih, jumlah_setor, id_prov, id_kab_user, tahun_berangkat.'],
                    ['no_porsi, nik, telepon, no_rek, no_validasi, dan kode wilayah sebaiknya diformat sebagai Teks agar angka nol di depan tidak hilang. NIK boleh kosong jika tidak tersedia pada sumber.'],
                    ['ID kabupaten/kecamatan yang valid akan digunakan. Jika kosong atau berisi error Excel seperti #N/A, sistem mencocokkan wilayah lewat desa dan kecamatan.'],
                    ['Baris kosong diabaikan. Baris duplikat NIK atau nomor porsi dilewati; data tidak valid membatalkan seluruh proses import.'],
                    ['Kolom id, uuid, created_at, dan updated_at dibuat otomatis oleh database dan tidak perlu dimasukkan.'],
                    ['Maksimal 2.000 baris per proses import.']
                ];
                var guideSheet = XLSX.utils.aoa_to_sheet(guideRows);
                guideSheet['!cols'] = [{wch:112}];
                guideSheet['!rows'] = [{hpt:34},{hpt:32},{hpt:30},{hpt:25},{hpt:30},{hpt:30},{hpt:30},{hpt:30},{hpt:30},{hpt:30},{hpt:25}];
                guideRows.forEach(function(row, index) {
                    var cell = 'A' + (index + 1);
                    guideSheet[cell].s = index === 0
                        ? {fill:{patternType:'solid',fgColor:{rgb:'075B43'}},font:{name:'Aptos Display',sz:16,bold:true,color:{rgb:'FFFFFF'}},alignment:{vertical:'center'}}
                        : {fill:{patternType:'solid',fgColor:{rgb:index%2?'F3F8F5':'FFFFFF'}},font:{name:'Aptos',sz:10,color:{rgb:'26352E'}},alignment:{vertical:'center',wrapText:true},border:{bottom:{style:'thin',color:{rgb:'E2EAE5'}}}};
                });
                XLSX.utils.book_append_sheet(templateBook, guideSheet, 'Petunjuk');
                XLSX.writeFile(templateBook, 'Template_Import_Pendaftaran_Jamaah.xlsx');
            });

            $('#btnImportExcel').on('click', function() { $('#fileImportExcel').trigger('click'); });
            $('#fileImportExcel').on('change', function() {
                var file = this.files && this.files[0];
                this.value = '';
                if (!file) return;
                if (!window.XLSX || !window.XLSX.read) {
                    return Swal.fire({icon:'error',title:'Library Excel tidak tersedia',text:'xlsx.bundle.js gagal dimuat.'});
                }
                var reader = new FileReader();
                reader.onerror = function() { Swal.fire({icon:'error',title:'File tidak dapat dibaca',text:'Silakan pilih file Excel yang valid.'}); };
                reader.onload = function(event) {
                    try {
                        var workbook = XLSX.read(event.target.result, {type:'array',cellDates:false});
                        var sheet = workbook.Sheets['Data Pendaftaran'] || workbook.Sheets[workbook.SheetNames[0]];
                        if (!sheet) throw new Error('Sheet data pendaftaran tidak ditemukan.');
                        var matrix = XLSX.utils.sheet_to_json(sheet, {header:1,defval:'',raw:true,blankrows:false});
                        if (!matrix.length) throw new Error('Sheet Excel tidak memiliki baris header.');

                        var normalizeHeader = function(value) {
                            return String(value || '').trim().toLowerCase().replace(/[^a-z0-9]/g, '');
                        };
                        var aliases = {
                            no_porsi:['noporsi'], nama:['nama','namacalonjamaahhaji','namajamaah'], nik:['nik'],
                            tempat_lahir:['tempatlahir'], tgl_lahir:['tgllahir','tanggallahir'],
                            bulan_lahir:['bulanlahir'], tahun_lahir:['tahunlahir'], usia:['usia','usiasaatmendaftar'],
                            jenkel:['jenkel','jeniskelamin'], pendidikan:['pendidikan','pendidikanterakhir'], pekerjaan:['pekerjaan'],
                            telepon:['telepon','nohp','nohptelp','notelpon'], alamat:['alamat'], desa:['desa'],
                            kecamatan:['kecamatan'], kabupaten:['kabupaten','kabkota','namakabupaten'],
                            id_kabupaten:['idkabupaten','idkab'], id_kecamatan:['idkecamatan','idekecamatan','idkec'],
                            tahun:['tahun','tahunpendaftaran'], tgl_daftar:['tgldaftar','tanggaldaftar'], status:['status'],
                            bank:['bank','bankpenerimasetoranbpih'], no_rek:['norek','nomorrekening'], no_validasi:['novalidasi'],
                            tgl_setor_bpih:['tglsetorbpih','tanggalsetoranbpih'],
                            jumlah_setor:['jumlahsetor','jumlahsetoranawalbpih'], id_prov:['idprov','idprovinsi'],
                            id_kab_user:['idkabuser'], tahun_berangkat:['tahunberangkat','tahunkeberangkatan']
                        };
                        var headerMap = {};
                        matrix[0].forEach(function(header, index) {
                            var key = normalizeHeader(header);
                            if (key) headerMap[key] = index;
                        });
                        var templateUsesBirthParts = ['tanggallahir','bulanlahir','tahunlahir'].every(function(key) { return headerMap[key] !== undefined; });
                        if (templateUsesBirthParts) aliases.tgl_lahir = [];
                        var canonicalHeaderMap = {};
                        Object.keys(aliases).forEach(function(column) {
                            var found = aliases[column].find(function(alias) { return headerMap[alias] !== undefined; });
                            if (found !== undefined) canonicalHeaderMap[column] = headerMap[found];
                        });
                        if (templateUsesBirthParts) {
                            canonicalHeaderMap._birth_day = headerMap.tanggallahir;
                            canonicalHeaderMap.tgl_lahir = headerMap.tanggallahir;
                        }
                        var missingHeaders = importRequiredColumns.filter(function(column) { return canonicalHeaderMap[column] === undefined; });
                        if (missingHeaders.length) throw new Error('Kolom wajib tidak ditemukan: ' + missingHeaders.join(', '));

                        var dateColumns = ['tgl_lahir','tgl_daftar','tgl_setor_bpih'];
                        var pad2 = function(number) { return String(number).padStart(2,'0'); };
                        var monthNumbers = {januari:1,februari:2,maret:3,april:4,mei:5,juni:6,juli:7,agustus:8,september:9,oktober:10,november:11,desember:12};
                        var correctedDateYearCount = 0;
                        var normalizeDate = function(value) {
                            if (value === '' || value === null || value === undefined) return '';
                            if (typeof value === 'number') {
                                var parsed = XLSX.SSF.parse_date_code(value);
                                if (!parsed) return String(value);
                                return parsed.y + '-' + pad2(parsed.m) + '-' + pad2(parsed.d);
                            }
                            var text = String(value).trim();
                            var iso = text.match(/^(\d{4})[-/](\d{1,2})[-/](\d{1,2})(?:[ T].*)?$/);
                            if (iso) return iso[1] + '-' + pad2(iso[2]) + '-' + pad2(iso[3]);
                            var local = text.match(/^(\d{1,2})[/.\-](\d{1,2})[/.\-](\d{4})$/);
                            if (local) return local[3] + '-' + pad2(local[2]) + '-' + pad2(local[1]);
                            var indonesian = text.match(/^(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4,5})$/);
                            if (indonesian && monthNumbers[indonesian[2].toLowerCase()]) {
                                var year = indonesian[3];
                                if (year === '29026') { year = '2026'; correctedDateYearCount++; }
                                if (year.length === 4) return year + '-' + pad2(monthNumbers[indonesian[2].toLowerCase()]) + '-' + pad2(indonesian[1]);
                            }
                            return text;
                        };
                        var rows = [];
                        for (var rowIndex = 1; rowIndex < matrix.length; rowIndex++) {
                            var cells = matrix[rowIndex] || [];
                            if (!cells.some(function(value) { return value !== '' && value !== null && value !== undefined; })) continue;
                            var row = {};
                            importColumns.forEach(function(column) {
                                var cellIndex = canonicalHeaderMap[column];
                                var value = cellIndex === undefined ? '' : cells[cellIndex];
                                if (column === 'tgl_lahir' && canonicalHeaderMap._birth_day !== undefined) {
                                    var day = cells[canonicalHeaderMap._birth_day];
                                    var month = String(cells[headerMap.bulanlahir] || '').trim().toLowerCase();
                                    var birthYear = cells[headerMap.tahunlahir];
                                    value = monthNumbers[month] && day && birthYear
                                        ? String(birthYear).slice(0,4) + '-' + pad2(monthNumbers[month]) + '-' + pad2(day)
                                        : '';
                                } else if (dateColumns.indexOf(column) !== -1) value = normalizeDate(value);
                                if (column === 'jenkel') {
                                    var gender = String(value || '').trim().toLowerCase();
                                    if (['lk','l','laki-laki','laki laki'].indexOf(gender) !== -1) value = 'L';
                                    else if (['pr','p','perempuan'].indexOf(gender) !== -1) value = 'P';
                                }
                                if (value === null || value === undefined) value = '';
                                row[column] = typeof value === 'string' ? value.trim() : value;
                            });
                            if (!row.tahun && row.tgl_daftar) row.tahun = String(row.tgl_daftar).slice(0,4);
                            row._baris_excel = rowIndex + 1;
                            rows.push(row);
                        }
                        if (!rows.length) throw new Error('Tidak ada baris data untuk diimpor.');
                        if (rows.length > 2000) throw new Error('Maksimal 2.000 baris per proses import.');

                        Swal.fire({
                            icon:'question',title:'Import data pendaftaran?',
                            html:'File <b>' + $('<div>').text(file.name).html() + '</b> berisi <b>' + rows.length + '</b> baris. Baris duplikat akan dilewati; baris tidak valid membatalkan seluruh import.' + (correctedDateYearCount ? '<br><small class="text-warning">' + correctedDateYearCount + ' tanggal dengan tahun 29026 akan dinormalkan menjadi 2026.</small>' : ''),
                            showCancelButton:true,confirmButtonText:'Ya, Import',cancelButtonText:'Batal',confirmButtonColor:'#0a5c43'
                        }).then(function(result) {
                            if (!result.isConfirmed) return;
                            Swal.fire({title:'Mengimpor Data...',allowOutsideClick:false,didOpen:function(){Swal.showLoading();}});
                            $.ajax({
                                url:'../api/import_pendaftaran.php',type:'POST',data:JSON.stringify({rows:rows}),
                                contentType:'application/json; charset=utf-8',dataType:'json'
                            }).done(function(response) {
                                if (!response || response.status !== true) {
                                    var details = response && Array.isArray(response.errors) ? '\n' + response.errors.slice(0,8).map(function(error){return 'Baris ' + error.row + ': ' + error.message;}).join('\n') : '';
                                    return Swal.fire({icon:'error',title:'Import dibatalkan',text:(response && response.message ? response.message : 'Data gagal diimpor.') + details,confirmButtonColor:'#dc2626'});
                                }
                                Swal.fire({icon:'success',title:'Import Berhasil',text:response.message || (response.imported + ' data berhasil dimasukkan.'),confirmButtonColor:'#0a5c43'});
                                if (typeof table !== 'undefined') table.ajax.reload(null,false);
                            }).fail(function(xhr) {
                                var response = xhr.responseJSON || {};
                                var details = Array.isArray(response.errors) ? '\n' + response.errors.slice(0,8).map(function(error){return 'Baris ' + error.row + ': ' + error.message;}).join('\n') : '';
                                Swal.fire({icon:'error',title:'Import Gagal',text:(response.message || 'Server tidak dapat memproses file import.') + details,confirmButtonColor:'#dc2626'});
                            });
                        });
                    } catch (error) {
                        Swal.fire({icon:'error',title:'Template tidak sesuai',text:error.message || 'Gagal membaca struktur file Excel.',confirmButtonColor:'#dc2626'});
                    }
                };
                reader.readAsArrayBuffer(file);
            });

            // ==========================================
            // FILTER KUSTOM
            // ==========================================
            $('#btnToggleFilter').on('click', function() {
                var $panel = $('#filterPanel');
                var opening = !$panel.is(':visible');
                $panel.stop(true, true).slideToggle(180);
                $(this).attr('aria-expanded', opening ? 'true' : 'false');
            });

            var registrationDateRange = null;
            $.fn.dataTable.ext.search.push(function(settings, data) {
                if (settings.nTable.id !== 'tabelPendaftaran' || !registrationDateRange) return true;
                var date = moment(data[5], 'DD MMM YYYY', true);
                return date.isValid() && date.isBetween(registrationDateRange.start, registrationDateRange.end, 'day', '[]');
            });

            $('#filterSearch').on('input', function() {
                table.search(this.value).draw();
            });
            $('#btnToggleYearArchive').on('click', function() {
                $('#filterTahun').val('');
                registrationShowPriorYears = !registrationShowPriorYears;
                updateRegistrationYearToggle();
                table.draw();
            });
            $('#filterStatus').on('change', function() {
                table.column(8).search(this.value.toLowerCase()).draw();
            });
            $('#filterTahun').on('change', function() {
                var selectedYear = $(this).val() || '';
                registrationShowPriorYears = !!selectedYear && selectedYear !== registrationCurrentYear;
                updateRegistrationYearToggle();
                table.draw();
            });

            $('#filterTanggal').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD MMM YYYY'
                }
            });
            $('#filterTanggal').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD MMM YYYY') + ' - ' + picker.endDate.format('DD MMM YYYY'));
                registrationDateRange = {start: picker.startDate.clone().startOf('day'), end: picker.endDate.clone().endOf('day')};
                table.draw();
            });
            $('#filterTanggal').on('cancel.daterangepicker', function() {
                $(this).val('');
                registrationDateRange = null;
                table.draw();
            });

            $('#btnResetFilter').on('click', function() {
                $('#filterSearch').val('');
                $('#filterStatus').val('');
                $('#filterTahun').val('');
                $('#filterTanggal').val('');
                registrationDateRange = null;
                registrationShowPriorYears = false;
                updateRegistrationYearToggle();
                table.search('').columns().search('').draw();
            });

            // ==========================================
            // HANDLER EDIT DATA
            // ==========================================
            $('#tabelPendaftaran tbody').on('click', '.edit', function(e) {
                e.preventDefault();
                var uuid = $(this).data('uuid');
                if (!uuid) {
                    Swal.fire('Error', 'UUID data tidak ditemukan.', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Memuat Data...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: '../api/get_detail_pendaftaran.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        uuid: uuid
                    },
                    success: function(response) {
                        Swal.close();
                        if (response && response.status === true && response.data) {
                            var d = response.data;

                            $('#modalTitle').html('<i class="fa-solid fa-pen-to-square text-primary"></i> Edit Data Pendaftaran Jamaah');
                            $('#idJamaah').val(d.uuid || d.id || '');
                            $('#noPorsi').val(d.no_porsi || '');
                            $('#nama').val(d.nama || '');
                            $('#nik').val(d.nik || '');
                            $('#tempatLahir').val(d.tempat_lahir || '');
                            $('#pekerjaan').val(d.pekerjaan || '');
                            $('#telepon').val(d.telepon || '');
                            $('#alamat').val(d.alamat || '');
                            $('#tahun').val(d.tahun || new Date().getFullYear());
                            $('#noRek').val(d.no_rek || '');
                            $('#noValidasi').val(d.no_validasi || '');

                            // Load Field Baru saat Edit
                            $('#tahunKeberangkatan').val(d.tahun_berangkat || (parseInt(d.tahun) + 2));
                            if (d.jumlah_setor) {
                                $('#jumlahSetorBpih').val(parseInt(d.jumlah_setor, 10).toLocaleString('id-ID'));
                            } else {
                                $('#jumlahSetorBpih').val('25.000.000');
                            }
                            if (d.tgl_setor_bpih) pickerSetor.setDate(d.tgl_setor_bpih, true);

                            if (d.tgl_lahir && d.tgl_lahir !== '0000-00-00') {
                                // Database menyimpan DATE sebagai YYYY-MM-DD, sedangkan
                                // Flatpickr menampilkan tanggal dengan format "d F Y".
                                const tanggalLahirDatabase = String(d.tgl_lahir).trim();
                                const tanggalLahirUntukPicker = /^\d{4}-\d{2}-\d{2}$/.test(tanggalLahirDatabase)
                                    ? new Date(`${tanggalLahirDatabase}T00:00:00`)
                                    : tanggalLahirDatabase;
                                pickerLahir.setDate(tanggalLahirUntukPicker, true);
                                $('#usia').val((d.usia ? d.usia : moment().diff(moment(tanggalLahirUntukPicker), 'years')) + ' Tahun');
                            } else {
                                pickerLahir.clear();
                                $('#usia').val('');
                            }

                            if (d.tgl_daftar) pickerDaftar.setDate(d.tgl_daftar, true);
                            else pickerDaftar.setDate(new Date(), true);

                            $('#jenkel').val(d.jenkel || '').trigger('change');
                            $('#pendidikan').val(d.pendidikan || '').trigger('change');
                            $('#status').val(d.status || 'pending').trigger('change');

                            // Load Bank via AJAX dan otomatis selesaikan nilai tersimpan
                            loadOptionsBank(d.bank);

                            $('#id_kabupaten').val(d.id_kabupaten || '');
                            $('#kabupaten').val(d.kabupaten || '');
                            $('#id_kecamatan').val(d.id_kecamatan || '');
                            $('#kecamatan').val(d.kecamatan || '').prop('disabled', !d.id_kabupaten);
                            $('#desa').val(d.desa || '').prop('disabled', !d.id_kecamatan);

                            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalForm')).show();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Memuat Data',
                                text: response.message || 'Data jamaah tidak ditemukan.'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Server',
                            text: 'Gagal menghubungi server untuk mengambil detail data.'
                        });
                    }
                });
            });

            // ==========================================
            // HANDLER CETAK BUKTI
            // ==========================================
            $('#tabelPendaftaran tbody').on('click', '.cetak-pendaftaran', function(e) {
                e.preventDefault();
                var uuid = $(this).data('uuid');
                if (!uuid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mencetak',
                        text: 'UUID data pendaftaran tidak ditemukan.'
                    });
                    return;
                }
                window.open('cetak-pendaftaran.php?uuid=' + encodeURIComponent(uuid), '_blank');
            });

            // ==========================================
            // HANDLER HAPUS DATA
            // ==========================================
            $('#tabelPendaftaran tbody').on('click', '.delete', function(e) {
                e.preventDefault();
                var uuid = $(this).data('uuid');
                var $tr = $(this).closest('tr');
                var rowData = table.row($tr).data();
                var namaJamaah = rowData && rowData.nama ? rowData.nama : 'Jamaah';

                Swal.fire({
                    title: 'Hapus Data Jamaah?',
                    html: `Apakah Anda yakin ingin menghapus data jamaah <b>${namaJamaah}</b>? Data yang dihapus tidak dapat dikembalikan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-trash-can me-1"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus Data...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        $.ajax({
                            url: '../api/hapus_pendaftaran.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                uuid: uuid
                            },
                            success: function(response) {
                                if (response && (response.status === true || response.success === true)) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil Dihapus!',
                                        text: response.message || 'Data jamaah berhasil dihapus.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    table.ajax.reload(null, false);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal Menghapus',
                                        text: response.message || 'Terjadi kesalahan saat menghapus data.'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan Server',
                                    text: 'Gagal menghubungkan ke server.'
                                });
                            }
                        });
                    }
                });
            });

            // ==========================================
            // SELECT2 & FLATPICKR INITIALIZATION
            // ==========================================
            $('.select2').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalForm'),
                width: '100%'
            });

            const pickerLahir = flatpickr("#tglLahir", {
                locale: 'id',
                dateFormat: 'd F Y',
                maxDate: 'today',
                allowInput: true,
                clickOpens: true,
                disableMobile: true,
                monthSelectorType: 'static',
                yearSelector: true,
                onChange: function(selectedDates) {
                    if (selectedDates.length) {
                        $('#usia').val(Math.max(0, moment().diff(moment(selectedDates[0]), 'years')) + ' Tahun');
                    } else {
                        $('#usia').val('');
                    }
                }
            });

            const pickerDaftar = flatpickr("#tglDaftar", {
                locale: 'id',
                dateFormat: 'd F Y',
                defaultDate: new Date(),
                allowInput: true,
                clickOpens: true,
                disableMobile: true,
                monthSelectorType: 'static'
            });

            const pickerSetor = flatpickr("#tglSetorBpih", {
                locale: 'id',
                dateFormat: 'd F Y',
                defaultDate: new Date(),
                allowInput: true,
                clickOpens: true,
                disableMobile: true,
                monthSelectorType: 'static'
            });

            $('#iconTglLahir').on('click', function(e) {
                e.preventDefault();
                pickerLahir.open();
            });
            $('#iconTglDaftar').on('click', function(e) {
                e.preventDefault();
                pickerDaftar.open();
            });
            $('#iconTglSetor').on('click', function(e) {
                e.preventDefault();
                pickerSetor.open();
            });

            // ==========================================
            // TOMBOL TAMBAH JAMAAH
            // ==========================================
            $('#btnTambah').click(function() {
                $('#modalTitle').html('<i class="fa-solid fa-kaaba"></i> Form Pendaftaran Jamaah Haji');
                $('#formPendaftaran')[0].reset();
                $('#idJamaah').val('');
                $('#usia').val('');
                $('.select2').val(null).trigger('change');
                $('#status').val('pending').trigger('change');
                pickerLahir.clear();
                pickerDaftar.setDate(new Date(), true);
                pickerSetor.setDate(new Date(), true);
                $('#jumlahSetorBpih').val('25.000.000');
                $('#tahunKeberangkatan').val(new Date().getFullYear() + 2);
                $('#kabupaten, #id_kabupaten').val('');
                $('#kecamatan, #id_kecamatan').val('').prop('disabled', true).attr('placeholder', 'Pilih Kabupaten dulu...');
                $('#desa').val('').prop('disabled', true).attr('placeholder', 'Pilih Kecamatan dulu...');

                loadOptionsBank();

                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalForm')).show();
            });

            // ==========================================
            // SUBMIT FORM PENDAFTARAN
            // ==========================================
            $('#formPendaftaran').submit(function(e) {
                e.preventDefault();

                if (!$('#id_kabupaten').val() || !$('#id_kecamatan').val() || !$('#desa').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Wilayah Belum Lengkap',
                        text: 'Harap isi Kabupaten, Kecamatan, dan Desa dengan benar.',
                        confirmButtonColor: '#0a5c43'
                    });
                    return;
                }

                var cleanJumlahSetor = $('#jumlahSetorBpih').val().replace(/[^0-9]/g, '');

                var formData = {
                    idJamaah: $('#idJamaah').val(),
                    no_porsi: $('#noPorsi').val(),
                    nama: $('#nama').val(),
                    nik: $('#nik').val(),
                    tempat_lahir: $('#tempatLahir').val(),
                    tgl_lahir: $('#tglLahir').val(),
                    usia: $('#usia').val().replace(' Tahun', ''),
                    jenkel: $('#jenkel').val(),
                    pendidikan: $('#pendidikan').val(),
                    pekerjaan: $('#pekerjaan').val(),
                    telepon: $('#telepon').val(),
                    alamat: $('#alamat').val(),
                    id_kabupaten: $('#id_kabupaten').val(),
                    kabupaten: $('#kabupaten').val(),
                    id_kecamatan: $('#id_kecamatan').val(),
                    kecamatan: $('#kecamatan').val(),
                    desa: $('#desa').val(),
                    tahun: $('#tahun').val(),
                    tgl_daftar: $('#tglDaftar').val(),
                    tgl_setor_bpih: $('#tglSetorBpih').val(),
                    jumlah_setor: cleanJumlahSetor,
                    tahun_berangkat: $('#tahunKeberangkatan').val(),
                    status: $('#status').val(),
                    bank: $('#bank').val(),
                    no_rek: $('#noRek').val(),
                    no_validasi: $('#noValidasi').val()
                };

                var $btnSubmit = $(this).find('button[type="submit"]');
                var originalBtnText = $btnSubmit.html();
                $btnSubmit.html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Menyimpan...').prop('disabled', true);

                fetch('../api/simpan_pendaftaran.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(async response => {
                        const text = await response.text();
                        try {
                            return JSON.parse(text);
                        } catch (err) {
                            throw new Error("Server Error: \n\n" + text);
                        }
                    })
                    .then(data => {
                        if (data.status === true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonColor: '#0a5c43',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalForm')).hide();
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan',
                                text: data.message,
                                confirmButtonColor: '#dc2626'
                            });
                        }
                        $btnSubmit.html(originalBtnText).prop('disabled', false);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Server',
                            html: `<pre style="text-align:left; font-size:12px;">${error.message}</pre>`,
                            confirmButtonColor: '#dc2626'
                        });
                        $btnSubmit.html(originalBtnText).prop('disabled', false);
                    });
            });
        });
    </script>
</body>

</html>
