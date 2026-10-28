<?php

/**
 * Proses Autentikasi Login (AJAX)
 * Lokasi File: api/auth_proses.php
 */
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

$response = ['status' => false, 'message' => 'Terjadi kesalahan sistem.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $data = json_decode($inputJSON, true);

    $username = trim($data['username'] ?? $_POST['username'] ?? '');
    $password = trim($data['password'] ?? $_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $response['message'] = 'Username dan Password wajib diisi!';
        echo json_encode($response);
        exit;
    }

    // Gunakan prepared statement untuk keamanan dari SQL Injection
    $stmt = mysqli_prepare($conn, "SELECT id,id_prov,id_kab, username, password, nama_lengkap, role FROM users WHERE username = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        // Kompatibel tanpa mysqlnd menggunakan bind_result & fetch
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $id, $id_prov, $id_kab, $db_username, $db_password, $nama_lengkap, $role);
            mysqli_stmt_fetch($stmt);

            // Periksa apakah password cocok (Mendukung password_verify atau plaintext)
            $is_valid = false;
            if (password_verify($password, $db_password) || $password === $db_password) {
                $is_valid = true;
            }

            if ($is_valid) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['user_id'] = $id;
                $_SESSION['id_prov'] = $id_prov;
                $_SESSION['id_kab'] = $id_kab;
                $_SESSION['username'] = $db_username;
                $_SESSION['nama_lengkap'] = $nama_lengkap;
                $_SESSION['role'] = $role;

                $response['status'] = true;
                $response['message'] = 'Login berhasil! Mengalihkan ke dashboard...';
                $response['redirect'] = 'page/index.php';
            } else {
                $response['message'] = 'Password yang Anda masukkan salah!';
            }
        } else {
            $response['message'] = 'Username tidak ditemukan di database!';
        }
        mysqli_stmt_close($stmt);
    } else {
        $response['message'] = 'Gagal menyiapkan kueri database.';
    }
}

mysqli_close($conn);
echo json_encode($response);
