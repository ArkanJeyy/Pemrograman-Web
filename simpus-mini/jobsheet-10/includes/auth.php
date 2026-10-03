<?php
// Guard clause: di-include di baris paling atas setiap halaman yang
// membutuhkan login (sebelum header.php mengeluarkan output apa pun),
// agar header('Location: ...') masih bisa dipanggil.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/koneksi.php';

// FITUR: Auto-login menggunakan Cookie jika session belum set
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = :token");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['role']    = $user['role'];
    }
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

/**
 * Memeriksa apakah user yang sedang login memiliki role yang diizinkan.
 * 
 * @param array $allowed_roles Contoh: ['admin']
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