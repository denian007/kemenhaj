<?php

/**
 * Proses Minify HTML Aman dari Redeclare
 * Lokasi File: crypt-start.php (atau file sejenis)
 */

if (!function_exists('minify_html')) {
    function minify_html($buffer)
    {
        if (empty(trim($buffer))) return $buffer;

        // 1. Hapus komentar HTML
        $buffer = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $buffer);

        // 2. Proses isi tag <style> (Hapus komentar CSS multiline dan hilangkan enter)
        $buffer = preg_replace_callback('/(<style\b[^>]*>)(.*?)(<\/style>)/is', function ($matches) {
            $style = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $matches[2]);
            $style = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $style);
            return $matches[1] . $style . $matches[3];
        }, $buffer);

        // 3. Proses isi tag <script> (Hapus komentar JS secara aman dan hilangkan enter)
        $buffer = preg_replace_callback('/(<script\b[^>]*>)(.*?)(<\/script>)/is', function ($matches) {
            $script = $matches[2];

            // A. Hapus komentar multi-baris /* ... */
            $script = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $script);

            // B. Hapus komentar satu baris // (mengabaikan URL seperti http:// atau //domain.com)
            $script = preg_replace('/(?<!:|\'|\")\/\/.*$/m', '', $script);

            // C. Ganti enter dan tab dengan spasi
            $script = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $script);

            return $matches[1] . $script . $matches[3];
        }, $buffer);

        // 4. Bantai semua karakter ganti baris dan tab di SELURUH sisa dokumen HTML
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $buffer);

        // 5. Rapatkan jarak antar tag HTML
        $buffer = preg_replace('/>\s+</', '><', $buffer);

        // 6. Ubah spasi yang berlebihan menjadi satu spasi saja agar lebih padat
        $buffer = preg_replace('/ +/', ' ', $buffer);

        return $buffer;
    }
}

// Pastikan ob_start dengan minify_html hanya dipanggil jika belum aktif
if (ob_get_level() == 0 || !in_array('minify_html', ob_list_handlers())) {
    ob_start("minify_html");
}
