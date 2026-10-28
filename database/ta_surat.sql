CREATE TABLE IF NOT EXISTS `ta_surat` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `jenis_surat` ENUM('masuk', 'keluar') NOT NULL COMMENT 'Jenis arsip: masuk atau keluar',
    `nomor_surat` VARCHAR(150) NOT NULL,
    `pengirim_penerima` VARCHAR(200) NOT NULL COMMENT 'Pengirim untuk surat masuk, penerima untuk surat keluar',
    `tanggal_surat` DATE NOT NULL,
    `perihal` VARCHAR(1000) NOT NULL,
    `status` VARCHAR(40) NOT NULL DEFAULT 'Perlu Tindak Lanjut',
    `catatan` TEXT NULL,
    `dibuat_oleh` VARCHAR(100) NOT NULL,
    `dibuat_pada` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ta_surat_jenis_tanggal` (`jenis_surat`, `tanggal_surat`),
    KEY `idx_ta_surat_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ta_surat_lampiran` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `public_token` CHAR(64) NOT NULL,
    `surat_id` BIGINT UNSIGNED NOT NULL,
    `nama_asli` VARCHAR(255) NOT NULL,
    `nama_file` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(120) NOT NULL,
    `ukuran` BIGINT UNSIGNED NOT NULL DEFAULT 0,
    `dibuat_pada` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ta_surat_lampiran_token` (`public_token`),
    KEY `idx_ta_surat_lampiran_surat` (`surat_id`),
    CONSTRAINT `fk_ta_surat_lampiran_ta_surat`
        FOREIGN KEY (`surat_id`) REFERENCES `ta_surat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
