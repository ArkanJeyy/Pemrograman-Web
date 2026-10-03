<?php
// Guard clause: di-include di baris paling atas setiap halaman yang
// membutuhkan login (sebelum header.php mengeluarkan output apa pun),
// agar header('Location: ...') masih bisa dipanggil.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

/**
 * Memeriksa apakah user yang sedang login memiliki role yang diizinkan.
 * Jika tidak sesuai, panggilan akan dihentikan dan diarahkan ke index.php.
 * 
 * @param array $allowed_roles Contoh: ['admin'] atau ['admin', 'petugas']
 */
function check_role(array $allowed_roles) {
    $user_role = $_SESSION['role'] ?? '';

    if (!in_array($user_role, $allowed_roles)) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' => 'Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman tersebut.'
        ];
        header('Location: ../index.php');
        exit;
    }
}